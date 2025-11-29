<?php
class model_trx_proses extends CI_Model {

    public $table = 'ppa';

    public function __construct(){
        parent::__construct();
    }

    function dataMainUntukProses($array) {
        $this->load->database();

        // Keyword filter
        $wh = '';
        if (!empty($array['q'])) {
            $keyword = strtoupper($array['q']);
            if ($array['field'] == 'ALL') {
                $wh = "AND (
                    UPPER(a.no_ppa) LIKE '%$keyword%' OR
                    UPPER(b.nama) LIKE '%$keyword%' OR
                    UPPER(c.nama) LIKE '%$keyword%'
                )";
            } else {
                $tblField = $array['field'] == 'NO_PPA'
                    ? "a.no_ppa"
                    : ($array['field'] == 'NAMA_KAPAL' ? "b.nama" : "c.nama");
                $wh = " AND UPPER($tblField) LIKE '%$keyword%'";
            }
        }

        // Filter tanggal
        $whtgl = '';
        $tglawal  = $array['tw'];
        $tglakhir = $array['tk'];
        if ($tglawal != '' && $tglakhir != '') {
            $whtgl = " AND a.tanggal BETWEEN '$tglawal' AND '$tglakhir'";
        } elseif ($tglawal != '') {
            $whtgl = " AND a.tanggal >= '$tglawal'";
        } elseif ($tglakhir != '') {
            $whtgl = " AND a.tanggal <= '$tglakhir'";
        }

        // user filter
        $whuser = $this->buildUserFilterClause('a');

        // Sorting
        $allowed = [
            'NO_PPA','TANGGAL','TYPE_BAYAR','TRXKPJK','STATUS_ID','NAMA_KAPAL',
            'NAMA_TONGKANG','JENIS_MUATAN','BERAT_MUATAN','TARIF_USD','PPN',
            'TOTAL_USD','NAMA_CUSTOMER','NAMA_AGEN'
        ];

        $sort  = in_array($array['sort'], $allowed) ? $array['sort'] : 'TANGGAL';
        $order = strtoupper($array['order']) == 'DESC' ? 'DESC' : 'ASC';

        if($sort == 'TANGGAL' && $order=='DESC'){
            $newSort = "TANGGAL DESC, NO_PPA DESC";
        } elseif($sort == 'TANGGAL' && $order=='ASC'){
            $newSort = "TANGGAL ASC, NO_PPA ASC";
        } elseif($sort == 'NO_PPA'){
            $newSort = "NO_PPA $order";
        } else {
            $newSort = "$sort $order, TANGGAL, NO_PPA";
        }

        // pagination
        $rowsPerPage = intval($array['rows']);
        $page        = intval($array['page']);
        $offset      = ($page - 1) * $rowsPerPage;

        // SQL main
        $sqlMain = "
            SELECT
    a.no_ppa NO_PPA,
    a.type_bayar TYPEBAYAR,
    a.tanggal TANGGAL,
    a.no_ppa CETAK,

    b.nama NAMA_KAPAL,
    c.nama NAMA_TONGKANG,
    f.nama JENIS_MUATAN,
    a.berat_muatan BERAT_MUATAN,

    g.nama NAMA_CUSTOMER,
    h.nama NAMA_AGEN,

    a.tarif_usd TARIF_USD,
    a.nilai_usd NILAI_USD,
    a.ppn PPN,
    a.by_admin BY_ADMIN,
    a.total_usd TOTAL_USD,

    CASE 
        WHEN a.isidr=3 THEN 'DOMESTIK'
        WHEN a.isidr=2 THEN 'EXPORT'
        ELSE '-'
    END JENISKIRIM,

    CASE
        WHEN a.type_bayar = '1' THEN 'PRA'
        WHEN a.type_bayar = '2' THEN 'PASCA'
    END TYPEBAYAR,

    a.status_id STATUS_ID,

    i.nama PELABUHAN_ASAL,
    j.nama PELABUHAN_TUJUAN,
    a.pelabuhan_tujuan_akhir PELABUHAN_TUJUAN_AKHIR,

    UPPER(a.keterangan) KETERANGAN,

    a.id ID
            FROM ppa a
            LEFT JOIN kapal b ON a.kapal_id=b.id
            LEFT JOIN kapal c ON a.tongkang_id=c.id
            LEFT JOIN jenis_muatan f ON a.jenis_muatan_id=f.id
            LEFT JOIN customer g ON a.customer_id=g.id
            LEFT JOIN mst_agen h ON a.agen_id=h.id
            LEFT JOIN pelabuhan i ON a.pelabuhan_id=i.id
            LEFT JOIN pelabuhan j ON a.pelabuhan_tujuan_id=j.id
            WHERE a.status_id IN (9,0,1)
            $whuser $whtgl $wh
        ";

        // total
        $sqlTotal = "
            SELECT COUNT(no_ppa) AS jumlah FROM (
                SELECT a.no_ppa
                FROM ppa a
                LEFT JOIN kapal b ON a.kapal_id=b.id
                LEFT JOIN kapal c ON a.tongkang_id=c.id
                WHERE a.status_id IN (9,0,1)
                $whuser $whtgl $wh
            ) x
        ";

        $jumlah = $this->db->query($sqlTotal)->row_array();
        $total = $jumlah['jumlah'] ?? 0;

        // final query
        $sqlPaged = "
            $sqlMain
            ORDER BY $newSort
            LIMIT $offset, $rowsPerPage
        ";

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

    function dataUntukProses($array) {
        $this->load->database();

        $user = $this->session->userdata('id_user');
        $whuser = $this->buildUserFilterClause('a');

        $filterStatus = ($user == '100')
            ? "a.status_id IN (9,0,1)"
            : "a.status_id IN (9,0,1,8)";

        $sqlMain = "
            SELECT 
                a.no_ppa                AS NO_PPA,
                a.type_bayar            AS TYPE_BAYAR_RAW,
                a.tanggal               AS TANGGAL,
                a.id                    AS ID,
                a.no_ppa                AS CETAK,
                g.nama                  AS NAMA_CUSTOMER,
                h.nama                  AS NAMA_AGEN,
                b.nama                  AS NAMA_KAPAL,
                c.nama                  AS NAMA_TONGKANG,
                f.nama                  AS JENIS_MUATAN,
                a.berat_muatan          AS BERAT_MUATAN,
                a.isidr                 AS ISIDR,
                a.trxkpjk               AS TRXKPJK,
                CASE 
                    WHEN a.isidr = 3 THEN 'DOMESTIK'
                    WHEN a.isidr = 2 THEN 'EXPORT'
                    ELSE '-'
                END                     AS JENISKIRIM,
                CASE
                    WHEN a.type_bayar = '1' THEN 'PRA'
                    WHEN a.type_bayar = '2' THEN 'PASCA'
                    ELSE '-'
                END                     AS TYPEBAYAR,
                a.tarif_usd             AS TARIF_USD,
                a.nilai_usd             AS NILAI_USD,
                a.ppn                   AS PPN,
                a.by_admin              AS BY_ADMIN,
                a.total_usd             AS TOTAL_USD,
                a.status_id             AS STATUS_ID,
                i.nama                  AS PELABUHAN_ASAL,
                j.nama                  AS PELABUHAN_TUJUAN,
                a.pelabuhan_tujuan_akhir AS PELABUHAN_TUJUAN_AKHIR,
                UPPER(a.keterangan)     AS KETERANGAN
            FROM ppa a
            LEFT JOIN kapal b ON a.kapal_id=b.id
            LEFT JOIN kapal c ON a.tongkang_id=c.id
            LEFT JOIN jenis_muatan f ON a.jenis_muatan_id=f.id
            LEFT JOIN customer g ON a.customer_id=g.id
            LEFT JOIN mst_agen h ON a.agen_id=h.id
            LEFT JOIN pelabuhan i ON a.pelabuhan_id=i.id
            LEFT JOIN pelabuhan j ON a.pelabuhan_tujuan_id=j.id
            WHERE $filterStatus $whuser
        ";

        $sqlMainWrapped = "SELECT * FROM ($sqlMain) t";

        $allowed = [
            'NO_PPA','TANGGAL','TYPE_BAYAR_RAW','TRXKPJK','STATUS_ID','NAMA_KAPAL',
            'NAMA_TONGKANG','JENIS_MUATAN','BERAT_MUATAN','TARIF_USD','PPN',
            'TOTAL_USD','NAMA_CUSTOMER'
        ];

        $sort  = in_array(strtoupper($array['sort']), $allowed) ? strtoupper($array['sort']) : 'TANGGAL';
        $order = strtoupper($array['order']) == 'DESC' ? 'DESC' : 'ASC';

        if ($sort === 'STATUS_ID') {
            if ($order == 'ASC') {
                $statusOrder = "
                    CASE t.status_id
                        WHEN 1 THEN 1
                        WHEN 0 THEN 2
                        WHEN 9 THEN 3
                        ELSE 4
                    END
                ";
            } else {
                $statusOrder = "
                    CASE t.status_id
                        WHEN 9 THEN 1
                        WHEN 0 THEN 2
                        WHEN 1 THEN 3
                        ELSE 4
                    END
                ";
            }
            $orderBy = "ORDER BY $statusOrder, t.no_ppa DESC";
        } else {
            $column = strtolower($sort);
            $orderBy = "ORDER BY t.$column $order, t.no_ppa DESC";
        }

        $rowsPerPage = max(1, intval($array['rows']));
        $page        = max(1, intval($array['page']));
        $offset      = ($page - 1) * $rowsPerPage;

        $sqlCount = "SELECT COUNT(1) AS jumlah FROM ($sqlMain) z";

        $sqlPaged = "
            $sqlMainWrapped
            $orderBy
            LIMIT $offset, $rowsPerPage
        ";

        $query = ['total' => 0, 'rows' => []];
        try {
            $totalRow = $this->db->query($sqlCount)->row_array();
            $query['total'] = isset($totalRow['jumlah']) ? intval($totalRow['jumlah']) : 0;
            $query['rows']  = $this->db->query($sqlPaged)->result_array();
        } catch (Exception $e) {
            log_message('error', 'dataUntukProses error: ' . $e->getMessage());
        }

        $this->db->close();
        return $query;
    }

    function getStatus($id) {
        $this->load->database();
        $this->db->select('status_id');
        $this->db->from('ppa');
        $this->db->where('id', $id);
        $row = $this->db->get()->row();
        return $row ? $row->status_id : null;
    }

    function updateStatus($id, $newStatus) {
        $this->load->database();
        return $this->db->where('id', $id)->update('ppa', ['status_id' => $newStatus]);
    }

    /**
     * Build WHERE clause restricting rows to the linked agen/customer.
     *
     * @param string $alias Table alias to prepend (defaults to 'a')
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
