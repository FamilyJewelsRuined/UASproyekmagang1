<?php
class model_trx_ppa extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
    }


		function dataMain($array) {
			$this->load->database();

			// Keyword filter
			$wh = '';
			if (isset($array['q']) && $array['q'] != '') {
				$keyword = strtoupper($array['q']);
				if ($array['field'] == 'ALL') {
					$wh = "AND (
						UPPER(a.no_ppa) LIKE '%$keyword%' OR
						UPPER(b.nama) LIKE '%$keyword%' OR
						UPPER(c.nama) LIKE '%$keyword%'
					)";
				} else {
					$tblField = $array['field'] == 'NO_PPA' ? "a.no_ppa" : ($array['field'] == 'NAMA_KAPAL' ? "b.nama" : "c.nama");
					$wh = " AND UPPER($tblField) LIKE '%$keyword%'";
				}
			}

			// Filter tanggal
			$whtgl = '';
			$tglawal  = $array['tw'];
			$tglakhir = $array['tk'];
			if ($tglawal != '' && $tglakhir != '') {
				$whtgl = " AND DATE(a.tanggal) BETWEEN DATE('$tglawal') AND DATE('$tglakhir')";
			} elseif ($tglawal != '') {
				$whtgl = " AND DATE(a.tanggal) >= DATE('$tglawal')";
			} elseif ($tglakhir != '') {
				$whtgl = " AND DATE(a.tanggal) <= DATE('$tglakhir')";
			}

			// User role filter (customer/agen scope)
			$whuser = $this->buildUserFilterClause('a');

			
			// Sorting - Map to actual column names
			$columnMap = [
				'NO_PPA' => 'a.no_ppa',
				'TANGGAL' => 'a.tanggal',
				'STATUS_ID' => 'a.status_id',
				'NAMA_KAPAL' => 'b.nama',
				'NAMA_TONGKANG' => 'c.nama',
				'JENIS_MUATAN' => 'f.nama',
				'BERAT_MUATAN' => 'a.berat_muatan',
				'TARIF_USD' => 'a.tarif_usd',
				'PPN' => 'a.ppn',
				'TOTAL_USD' => 'a.total_usd',
				'NAMA_CUSTOMER' => 'g.nama'
			];
			
			$allowedColumns = array_keys($columnMap);
			$sort  = in_array($array['sort'], $allowedColumns) ? $array['sort'] : 'TANGGAL';
			$order = strtoupper($array['order']) == 'DESC' ? 'DESC' : 'ASC';
			
			$sortColumn = isset($columnMap[$sort]) ? $columnMap[$sort] : 'a.tanggal';

			if($sort == 'TANGGAL' && $order=='DESC'){
				$newSort = $sortColumn.' '.$order.', a.no_ppa DESC';
			}elseif($sort == 'TANGGAL' && $order=='ASC'){
				$newSort = $sortColumn.' '.$order.', a.no_ppa ASC';
			}elseif($sort == 'NO_PPA'){
				$newSort = $sortColumn.' '.$order;
			}else{
				$newSort = $sortColumn.' '.$order.', a.tanggal, a.no_ppa';
			}

			// Pagination
			$rowsPerPage = intval($array['rows']);
			$page        = intval($array['page']);
			$offset      = ($page - 1) * $rowsPerPage;

			// SQL Main - Updated to match actual database structure
			// Column names must be uppercase to match DataTables expectations
			$sqlMain = "SELECT a.no_ppa AS NO_PPA, 
							CASE WHEN a.tanggal >= STR_TO_DATE('01102019','%d%m%Y') THEN a.no_ppa END AS CETAK,
							a.tanggal AS TANGGAL,
							g.nama AS NAMA_CUSTOMER,
							NULL AS NAMA_AGEN,
							b.nama AS NAMA_KAPAL, 
							c.nama AS NAMA_TONGKANG,
							f.nama AS JENIS_MUATAN, 
							a.berat_muatan AS BERAT_MUATAN,
							NULL AS ISIDR,
							NULL AS TRXKPJK,
							NULL AS NOMOR_PKK_TUGBOAT,
							NULL AS NOMOR_PKK_TONGKANG,
							'-' AS JENISKIRIM,
							CASE
								WHEN g.type_bayar = 1 THEN 'PRA BAYAR'
								WHEN g.type_bayar = 2 THEN 'PASCA BAYAR'
								ELSE NULL
							END AS TYPEBAYAR,
							a.tarif_usd AS TARIF_USD, 
							a.nilai_usd AS NILAI_USD, 
							a.ppn AS PPN, 
							NULL AS BY_ADMIN, 
							a.total_usd AS TOTAL_USD,
							a.status_id AS STATUS_ID,
                            CASE
                                WHEN a.status_id = 8 THEN '8 - Revisi'
                                WHEN a.status_id = 9 THEN '9 - Diajukan'
                                WHEN a.status_id = 0 THEN '0 - Diterima'
                                WHEN a.status_id = 1 THEN '1 - Approved'
                                WHEN a.status_id = 2 THEN '2 - Proses-Pasca'
                                WHEN a.status_id = 3 THEN '3 - Proses-Pra'
                                WHEN a.status_id = 5 THEN '5 - Lunas'
                                ELSE CONCAT(a.status_id, ' - Unknown')
                            END AS STATUS_NAMA,
							i.nama AS PELABUHAN_ASAL,
							j.nama AS PELABUHAN_TUJUAN,
							NULL AS PELABUHAN_TUJUAN_AKHIR,
							UPPER(COALESCE(a.keterangan, '')) AS KETERANGAN
						FROM ppa a
						LEFT JOIN kapal b ON a.kapal_id=b.id
						LEFT JOIN kapal c ON a.tongkang_id=c.id
						LEFT JOIN jenis_muatan f ON a.jenis_muatan_id=f.id
						LEFT JOIN customer g ON a.customer_id=g.id
						LEFT JOIN pelabuhan i ON a.pelabuhan_id=i.id
						LEFT JOIN pelabuhan j ON a.pelabuhan_tujuan_id=j.id
						WHERE a.status_id IN (9,0,1,2,3,4,5,8)";
			
			// Build WHERE clause properly for main query
			if (!empty($whuser)) $sqlMain .= " " . trim($whuser);
			if (!empty($whtgl)) $sqlMain .= " " . trim($whtgl);
			if (!empty($wh)) $sqlMain .= " " . trim($wh);

			//Total filtered - Build WHERE clause properly
			$whereClause = "WHERE a.status_id IN (9,0,1,2,3,4,5,8)";
			if (!empty($whuser)) $whereClause .= " " . trim($whuser);
			if (!empty($whtgl)) $whereClause .= " " . trim($whtgl);
			if (!empty($wh)) $whereClause .= " " . trim($wh);
			
			$sqlTotal = "
				SELECT COUNT(no_ppa) AS jumlah 
				FROM (
					SELECT a.no_ppa, 
						b.nama AS nama_kapal, c.nama AS nama_tongkang
					FROM ppa a
					LEFT JOIN kapal b ON a.kapal_id=b.id
					LEFT JOIN kapal c ON a.tongkang_id=c.id
					$whereClause
				) AS subquery
			";
			
			// Log SQL for debugging
			log_message('debug', 'SQL Total: ' . $sqlTotal);
			
			$jumlah = $this->db->query($sqlTotal)->row_array();
			// MySQL returns lowercase column names by default
			$total = isset($jumlah['jumlah']) ? intval($jumlah['jumlah']) : (isset($jumlah['JUMLAH']) ? intval($jumlah['JUMLAH']) : 0);

			// Pagination MySQL
			$sqlPaged = "
				SELECT * FROM (
					$sqlMain
					ORDER BY $newSort 
				) AS ordered_table
				LIMIT $offset, $rowsPerPage
			";

			// Jalankan query
			$query = [];
			try {
				// Log SQL for debugging
				log_message('debug', 'SQL Paged: ' . $sqlPaged);
				
				$query['total'] = $total;
				$rows = $this->db->query($sqlPaged)->result_array();
				
				// Transform array keys to uppercase to match DataTables expectations
				// CodeIgniter MySQL driver may return lowercase keys
				$query['rows'] = array_map(function($row) {
					$uppercaseRow = [];
					foreach ($row as $key => $value) {
						$uppercaseRow[strtoupper($key)] = $value;
					}
					return $uppercaseRow;
				}, $rows);
				
				// Check for database errors
				if ($this->db->error()['code'] != 0) {
					log_message('error', 'Database error: ' . $this->db->error()['message']);
					throw new Exception('Database query error: ' . $this->db->error()['message']);
				}
			} catch (Exception $e) {
				log_message('error', 'Exception in dataMain: ' . $e->getMessage());
				$query['total'] = 0;
				$query['rows'] = [];
			}

			$this->db->close();
			return $query;
		}
	
		function dataKoreksi($array) {
			$this->load->database();

			$whuser = $this->buildUserFilterClause('a');

			$allowedColumns = ['no_ppa','tanggal','type_bayar','trxkpjk','status_id','nama_kapal','nama_tongkang','jenis_muatan','berat_muatan','tarif_usd','ppn','total_usd','nama_customer'];
			$sort  = in_array($array['sort'], $allowedColumns) ? $array['sort'] : 'tanggal';
			$order = strtoupper($array['order']) == 'DESC' ? 'DESC' : 'ASC';

			// Pagination
			$rowsPerPage = intval($array['rows']);
			$page        = intval($array['page']);
			$offset      = ($page - 1) * $rowsPerPage;

			// SQL Main
			$sqlMain = "select * from (SELECT a.no_ppa, a.type_bayar, a.tanggal,
							A.ID AS ID, a.no_ppa AS cetak,
							g.nama AS nama_customer, h.nama AS nama_agen,
							b.nama AS nama_kapal, c.nama AS nama_tongkang,
							f.nama AS jenis_muatan, a.berat_muatan,
							a.isidr, a.trxkpjk,
							CASE 
								WHEN a.isidr=3 THEN 'DOMESTIK - IDR'
								WHEN a.isidr=2 THEN 'EKSPOR - USD'
								ELSE '-'
							END AS JENISKIRIM,
							case
								when a.type_bayar = '1' then 'PRA BAYAR'
								when a.type_bayar = '2' then 'PASCA BAYAR'
							end as TYPEBAYAR,
							a.tarif_usd, a.nilai_usd, a.ppn, a.by_admin, a.total_usd,
							a.status_id,
							i.nama AS pelabuhan_asal,
							j.nama AS pelabuhan_tujuan,
							a.pelabuhan_tujuan_akhir,
							UPPER(a.keterangan) AS keterangan
						FROM ppa a
						LEFT JOIN kapal b ON a.kapal_id=b.id
						LEFT JOIN kapal c ON a.tongkang_id=c.id
						LEFT JOIN jenis_muatan f ON a.jenis_muatan_id=f.id
						LEFT JOIN customer g ON a.customer_id=g.id
						LEFT JOIN mst_agen h ON a.agen_id=h.id
						LEFT JOIN pelabuhan i ON a.pelabuhan_id=i.id
						LEFT JOIN pelabuhan j ON a.pelabuhan_tujuan_id=j.id
						WHERE a.status_id IN (8) $whuser
						) 
						";

			//Total filtered 
			$sqlTotal = "
				SELECT COUNT(no_ppa) AS jumlah 
					FROM ppa a
					WHERE a.status_id IN (8) $whuser
			";
			$jumlah = $this->db->query($sqlTotal)->row_array();
			$total = isset($jumlah['JUMLAH']) ? $jumlah['JUMLAH'] : 0;

			// Pagination Oracle
			$sqlPaged = "
				SELECT * FROM (
					SELECT inner_table.*, ROWNUM rnum FROM (
						SELECT * FROM (
							$sqlMain
							ORDER BY $sort $order
						) ordered_table
					) inner_table
					WHERE ROWNUM <= " . ($offset + $rowsPerPage) . "
				) WHERE rnum > $offset
			";

			// Jalankan query
			$query = [];
			try {
				$query['total'] = $total;
				$query['rows']  = $this->db->query($sqlPaged)->result_array();
			} catch (Exception $e) {
				$query['total'] = 0;
				$query['rows'] = [];
			}

			$this->db->close();
			return $query;
		}

		function getPpaByNo($no){
			$sql = "select a.tanggal, a.no_ppa, a.sifat_kunjungan, b.nama as nama_customer, a.alamat_npwp, A.npwp,
					c.nama as nama_kapal, a.gt_kapal, d.nama as nama_tongkang, a.gt_tongkang, 
					e.nama as asal, f.nama as tujuan, a.pelabuhan_tujuan_akhir as akhir,
					g.nama as muatan, TO_CHAR(a.jam_lewat,'dd/mm/yyyy  hh24:mi') AS JAM_LEWAT, 
					a.berat_muatan, a.isidr, a.tarif_usd, a.trxkpjk, a.by_admin,
					a.nilai_usd, a.ppn_persen, a.ppn, a.total_usd, 
					h.nama as nama_agen, h.alamat as alamat_agen,
					a.agen_id, a.customer_id, a.type_bayar,
					i.no_nota as no_nota, a.status, a.status_id,
					i.No_faktur,
					to_char(a.tanggal,'dd') as tgl, to_char(a.tanggal,'mm') as bln, to_char(a.tanggal,'yyyy') as thn,
                    J.NAMA AS PEJABAT, J.JABATAN,
                    a.nomor_pkk_tugboat, a.nomor_pkk_tongkang
					from ppa a
					left join customer b on a.customer_id=b.id
					left join kapal c on a.kapal_id=c.id 
					left join kapal d on a.tongkang_id=d.id
					left join pelabuhan e on a.pelabuhan_id=e.id
					left join pelabuhan f on a.pelabuhan_tujuan_id=f.id
					left join jenis_muatan g on a.jenis_muatan_id=g.id
					left join mst_agen h on a.agen_id=h.id
					left join ppa_detail i on a.id=i.id
                    LEFT JOIN SYS_TTD_NOTA J ON A.TANGGAL BETWEEN J.TGL_AWAL AND J.TGL_AKHIR
					";       
			$this->load->database();
			$sql = "select * from ($sql) where NO_PPA='$no'";
			$query = $this->db->query($sql)->row_array();
			$this->db->close();
			return $query;
		} 

		function getReportData($tw = '', $tk = '', $filterField = '', $filterQuery = '') {
			$this->load->database();

			// Filter keyword
			$wh = '';
			$keyword = strtoupper($filterQuery);
			if ($keyword != '') {
				if ($filterField == 'ALL') {
					$wh = " AND (
						UPPER(a.no_ppa) LIKE '%$keyword%' OR
						UPPER(b.nama) LIKE '%$keyword%' OR
						UPPER(c.nama) LIKE '%$keyword%'
					)";
				} else {
					$tblField = $filterField == 'NO_PPA' ? "a.no_ppa" : ($filterField == 'NAMA_KAPAL' ? "b.nama" : "c.nama");
					$wh = " AND UPPER($tblField) LIKE '%$keyword%'";
				}
			}

			// Filter tanggal
			$whtgl = '';
			if ($tw && $tk) {
				$whtgl = " AND a.tanggal BETWEEN '$tw' AND '$tk'";
			} elseif ($tw) {
				$whtgl = " AND a.tanggal >= '$tw'";
			} elseif ($tk) {
				$whtgl = " AND a.tanggal <= '$tk'";
			}

			// User role filter
			$whuser = '';
			$whuser = $this->buildUserFilterClause('a');

			// Query utama
			$sql = "
    SELECT 
        a.no_ppa AS NO_PPA, 
        a.type_bayar AS TYPE_BAYAR, 
        a.tanggal AS TANGGAL,

        g.nama AS NAMA_CUSTOMER,
        h.nama AS NAMA_AGEN,

        b.nama AS NAMA_KAPAL,
        c.nama AS NAMA_TONGKANG,

        f.nama AS JENIS_MUATAN,
        a.berat_muatan AS BERAT_MUATAN,

        a.isidr AS ISIDR,
        a.trxkpjk AS TRXKPJK,

        CASE 
            WHEN a.isidr=3 THEN 'DOMESTIK'
            WHEN a.isidr=2 THEN 'EXPORT'
            ELSE '-'
        END AS JENISKIRIM,

        CASE
            WHEN a.type_bayar='1' THEN 'PRA'
            WHEN a.type_bayar='2' THEN 'PASCA'
        END AS TYPEBAYAR,

        a.tarif_usd AS TARIF_USD,
        a.nilai_usd AS NILAI_USD,
        a.ppn AS PPN,
        a.by_admin AS BY_ADMIN,
        a.total_usd AS TOTAL_USD,

        i.nama AS PELABUHAN_ASAL,
        j.nama AS PELABUHAN_TUJUAN,
        a.pelabuhan_tujuan_akhir AS PELABUHAN_TUJUAN_AKHIR,

        UPPER(a.keterangan) AS KETERANGAN

    FROM ppa a
    LEFT JOIN kapal b ON a.kapal_id = b.id
    LEFT JOIN kapal c ON a.tongkang_id = c.id
    LEFT JOIN jenis_muatan f ON a.jenis_muatan_id = f.id
    LEFT JOIN customer g ON a.customer_id = g.id
    LEFT JOIN mst_agen h ON a.agen_id = h.id
    LEFT JOIN pelabuhan i ON a.pelabuhan_id = i.id
    LEFT JOIN pelabuhan j ON a.pelabuhan_tujuan_id = j.id

    WHERE a.status_id IN (9,0,1,2,3,4,5,8)
    $whtgl
    $wh
    $whuser

    ORDER BY a.tanggal, a.no_ppa
";


			// Eksekusi query dan kembalikan hasil
			//return $this->db->query($sql)->result();
			return $this->db->query($sql)->result_array();
			$this->db->close();

		}

		function getById($id){
            $this->load->database();
            $sql = "SELECT
					to_char(a.jam_lewat,'HH24:MI') as JAM_LEWAT_INPUT,
					to_char(a.jam_lewat,'DD/MM/YYYY') as TANGGAL_LEWAT_INPUT,
					to_char(a.tanggal_draft, 'DD/MM/YYYY') as TANGGAL_DRAFT_EDIT,
					A.*
					from ppa A
					where A.ID='$id'";
            $sql = "SELECT * FROM (
                    SELECT 
                        a.ID, 
                        a.NO_PPA, 
                        a.STATUS_ID,
                        to_char(a.TANGGAL, 'DD/MM/YYYY') as TANGGAL,
                        to_char(a.tanggal,'dd/mm/yy')||' - '||a.NO_PPA ||'  ['||a.STATUS_ID||']  '||'- Agen: '||c.nama||' - ' ||' Kapal: '||b.nama ||' - Tongkang: '||c.nama as TEXT_PPA,
                        a.KAPAL_ID, a.GT_KAPAL, b.NAMA AS KAPAL_NAMA, b.nama as PKK_KAPAL_NAMA,
                        a.tongkang_id, a.GT_TONGKANG, c.nama as TONGKANG_NAMA,
                        a.AGEN_ID, d.nama as AGEN_NAMA,
                        a.pelabuhan_id,e.nama as PELABUHAN_NAMA,
                        a.pelabuhan_tujuan_id,
                        f.nama as PELABUHAN_TUJUAN_NAMA,
                        a.ISIDR, 
                        case when a.isidr = 3 then 'DOMESTIK - IDR' when a.isidr=2 then 'EKSPOR - USD' end as ISIDR_NAMA,
                        a.sifat_kunjungan,
                        case when a.sifat_kunjungan = 'I' then 'INSIDENTIL' when a.sifat_kunjungan = 'R' then 'RUTIN' end as SIFAT_KUNJUNGAN_NAMA,
                        to_char(a.jam_lewat, 'DD/MM/YYYY') as TANGGAL_LEWAT_INPUT, to_char(a.jam_lewat, 'hh24:mi') as JAM_LEWAT_INPUT,
                        a.surveyor_id, 
                        g.nama as SURVEYOR_NAMA,
                        to_char(a.TANGGAL, 'DD/MM/YYYY') as TANGGAL_DRAFT,
                        to_char(a.tanggal_draft, 'DD/MM/YYYY') as TANGGAL_DRAFT_EDIT,
                        a.PELABUHAN_TUJUAN_AKHIR,
                        A.MEAN_OF_MEANS,
                        a.CUSTOMER_ID, h.nama as CUSTOMER_NAMA,
                        a.TYPE_BAYAR, a.NPWP, a.ALAMAT_NPWP, h.KODE, 
                        a.JENIS_MUATAN_ID, i.nama as JENIS_MUATAN_NAMA,
                        a.BERAT_MUATAN,  A.TARIF_USD, a.trxkpjk,  A.NILAI_USD, A.PPN_PERSEN, A.PPN, A.TOTAL_USD,
                        a.KETERANGAN,
                        a.NOMOR_PKK_TUGBOAT,
                        a.NOMOR_PKK_TONGKANG
                    FROM ppa a
                    LEFT JOIN kapal b ON a.KAPAL_ID = b.ID
                    LEFT JOIN kapal c ON a.TONGKANG_ID = c.ID
                    left join mst_agen d on a.agen_id=d.id
                    left join pelabuhan e on a.pelabuhan_id=e.id
                    left join pelabuhan f on a.pelabuhan_tujuan_id=f.id
                    left join surveyor g on a.surveyor_id=g.id
                    left join customer h on a.customer_id=h.id
                    left join jenis_muatan i on a.jenis_muatan_id=i.id
                    where A.ID='$id'
                    )";
            $query['result'] = $this->db->query($sql)->row_array();
            $this->db->close();
            return $query;
        } 

        function ppaList($q = null) {
            $modul = $this->session->userdata('modul');
            $levelid = $this->session->userdata('levelid');

            $modulAllowed = in_array($modul, [ 1, 2, 7, 8]);
            $levelAllowed = in_array($levelid, [4, 2]);
            
            if($modulAllowed){
                if($levelAllowed){
                    $where = "WHERE A.STATUS_ID IN ('0', '1')";
                }else if($levelid =  '1'){
                    $where = "WHERE A.STATUS_ID IN ('9')";
                }
            }else{
                $where = "WHERE A.STATUS_ID IN ()";
            }
            
            
            $sql = "SELECT * FROM (
                    SELECT 
                        a.ID, 
                        a.NO_PPA, 
                        to_char(a.TANGGAL, 'DD/MM/YYYY') as TANGGAL,
                        to_char(a.tanggal,'dd/mm/yy')||' - '||a.NO_PPA ||'  ['||a.STATUS_ID||']  '||'- Agen: '||c.nama||' - ' ||' Kapal: '||b.nama ||' - Tongkang: '||c.nama as TEXT_PPA,
                        a.KAPAL_ID, a.GT_KAPAL, b.NAMA AS KAPAL_NAMA,
                        a.tongkang_id, a.GT_TONGKANG, c.nama as TONGKANG_NAMA,
                        a.AGEN_ID, d.nama as AGEN_NAMA,
                        a.pelabuhan_id,e.nama as PELABUHAN_NAMA,
                        a.pelabuhan_tujuan_id,
                        f.nama as PELABUHAN_TUJUAN_NAMA,
                        a.ISIDR, 
                        case when a.isidr = 3 then 'DOMESTIK - IDR' when a.isidr=2 then 'EKSPOR - USD' end as ISIDR_NAMA,
                        a.sifat_kunjungan,
                        case when a.sifat_kunjungan = 'I' then 'INSIDENTIL' when a.sifat_kunjungan = 'R' then 'RUTIN' end as SIFAT_KUNJUNGAN_NAMA,
                        to_char(a.jam_lewat, 'DD/MM/YYYY') as TANGGAL_LEWAT_INPUT, to_char(a.jam_lewat, 'hh24:mi') as JAM_LEWAT_INPUT,
                        a.surveyor_id, 
                        g.nama as SURVEYOR_NAMA,
                        to_char(a.TANGGAL, 'DD/MM/YYYY') as TANGGAL_DRAFT,
                        a.PELABUHAN_TUJUAN_AKHIR,
                        A.MEAN_OF_MEANS,
                        a.CUSTOMER_ID, h.nama as CUSTOMER_NAMA,
                        a.TYPE_BAYAR, a.NPWP, a.ALAMAT_NPWP, h.KODE, 
                        a.JENIS_MUATAN_ID, i.nama as JENIS_MUATAN_NAMA,
                        a.BERAT_MUATAN,  A.TARIF_USD, a.trxkpjk,  A.NILAI_USD, A.PPN_PERSEN, A.PPN, A.TOTAL_USD,
                        a.KETERANGAN,
                        a.NOMOR_PKK_TUGBOAT,
                        a.NOMOR_PKK_TONGKANG
                    FROM ppa a
                    LEFT JOIN kapal b ON a.KAPAL_ID = b.ID
                    LEFT JOIN kapal c ON a.TONGKANG_ID = c.ID
                    left join mst_agen d on a.agen_id=d.id
                    left join pelabuhan e on a.pelabuhan_id=e.id
                    left join pelabuhan f on a.pelabuhan_tujuan_id=f.id
                    left join surveyor g on a.surveyor_id=g.id
                    left join customer h on a.customer_id=h.id
                    left join jenis_muatan i on a.jenis_muatan_id=i.id
                    --WHERE a.STATUS_ID IN ('9','0','1')
                    $where 
                    ORDER BY 
                    CASE a.STATUS_ID
                        WHEN 9 THEN 1
                        WHEN 0 THEN 2
                        WHEN 1 THEN 3
                        ELSE 4
                    END,
                    a.tanggal, a.NO_PPA ASC
                    )
                    WHERE ROWNUM <= 50
                ";
            $query = $this->db->query($sql);
            return $query->result();
        }

		/**
		 * Build WHERE clause restricting rows to the logged-in agen/customer scope.
		 *
		 * @param string $alias
		 * @return string
		 */
		private function buildUserFilterClause($alias = 'a') {
			$column = $this->session->userdata('filter_user_column');
			$id = $this->session->userdata('filter_user_id');
			$role = $this->session->userdata('group');

			if (!$column || !$id) {
				return in_array($role, ['101', '102', '103'], true) ? ' AND 1=0' : '';
			}

			if (!in_array($column, ['agen_id', 'customer_id'], true)) {
				return '';
			}

			$alias = trim($alias) !== '' ? $alias : 'a';
			$id = intval($id);

			return " AND {$alias}.{$column} = {$id}";
		}

	}
?>