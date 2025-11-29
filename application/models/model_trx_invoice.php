<?php
	class model_trx_invoice extends MY_Model {
		
		function model_trx_invoice(){
			parent::__construct();
			$this->SetTable("TRX_PPA_INVOICE");
			$this->setTypeDate(array('TANGGAL_INVOICE')); 
		}


		function dataMain($array) {
			$this->load->database();

			// Keyword filter
			$wh = '';
			if (isset($array['q']) && $array['q'] != '') {
				$keyword = strtoupper($array['q']);
				if ($array['field'] == 'ALL') {
					$wh = "AND (
						UPPER(no_invoice) LIKE '%$keyword%' OR
						UPPER(tanggal_invoice) LIKE '%$keyword%' OR
						UPPER(customer) LIKE '%$keyword%'
					)";
				} else {
					$tblField = $array['field'] == 'NO_INVOICE' ? "no_invoice" : ($array['field'] == 'TANGGAL_INVOICE' ? "tanggal_invoice" : "customer");
					$wh = " AND UPPER($tblField) LIKE '%$keyword%'";
				}
			}

			// Filter tanggal
			$whtgl = '';
			$tglawal  = $array['tw'];
			$tglakhir = $array['tk'];
			if ($tglawal != '' && $tglakhir != '') {
				$whtgl = " AND tanggal_invoice BETWEEN to_date('$tglawal','yyyy-mm-dd') AND to_date('$tglakhir','yyyy-mm-dd')";
			} elseif ($tglawal != '') {
				$whtgl = " AND tanggal_invoice >= to_date('$tglawal','yyyy-mm-dd')";
			} elseif ($tglakhir != '') {
				$whtgl = " AND tanggal_invoice <= to_date('$tglakhir','yyyy-mm-dd')";
			}

			// User role filter
			$whuser = $this->buildCustomerScopeWhere();

			
			// Sorting
			$allowedColumns = ['NO_INVOICE', 'TANGGAL_INVOICE', 'CUSTOMER'];
			$sort  = in_array($array['sort'], $allowedColumns) ? $array['sort'] : 'TANGGAL_INVOICE';
			$order = strtoupper($array['order']) == 'DESC' ? 'DESC' : 'ASC';

			if($sort == 'TANGGAL_INVOICE' && $order=='DESC'){
				$newSort = $sort.' '.$order.', NO_INVOICE DESC';
			}elseif($sort == 'TANGGAL_INVOICE' && $order=='ASC'){
				$newSort = $sort.' '.$order.', NO_INVOICE ASC';
			}elseif($sort == 'NO_INVOICE'){
				$newSort = $sort.' '.$order;
			}else{
				$newSort = $sort.' '.$order.', TANGGAL_INVOICE, NO_INVOICE';
			}

			// Pagination
			$rowsPerPage = intval($array['rows']);
			$page        = intval($array['page']);
			$offset      = ($page - 1) * $rowsPerPage;

			// SQL Main
			$sqlMain = "select * from(
                            SELECT a.*, b.nama as customer
                            from trx_ppa_invoice a
                            left join mst_customer b on a.customer_id=b.id
                        )
                        $whuser $whtgl $wh
						";

			//Total filtered 
			$sqlTotal = "
				SELECT COUNT(id) AS jumlah 
				FROM (
					SELECT a.*, b.nama as customer
                    from trx_ppa_invoice a
                    left join mst_customer b on a.customer_id=b.id
				)
                $whuser $whtgl $wh
			";
			$jumlah = $this->db->query($sqlTotal)->row_array();
			$total = isset($jumlah['JUMLAH']) ? $jumlah['JUMLAH'] : 0;

			// Pagination Oracle
			$sqlPaged = "
				SELECT * FROM (
					SELECT inner_table.*, ROWNUM rnum FROM (
						SELECT * FROM (
							$sqlMain
							ORDER BY $newSort 
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

		function getReportData($tw, $tk, $field, $q) {
			$this->load->database();
		
			$wh = '';
			if (isset($q) && $q != '') {
				$keyword = strtoupper($q);
				if ($field == 'ALL') {
					$wh = "AND (
						UPPER(no_invoice) LIKE '%$keyword%' OR
						UPPER(tanggal_invoice) LIKE '%$keyword%' OR
						UPPER(customer) LIKE '%$keyword%'
					)";
				} else {
					$tblField = $field == 'NO_INVOICE' ? "no_invoice" : ($field == 'TANGGAL_INVOICE' ? "tanggal_invoice" : "customer");
					$wh = " AND UPPER($tblField) LIKE '%$keyword%'";
				}
			}
		
			$whtgl = '';
			if ($tw != '' && $tk != '') {
				$whtgl = " AND tanggal_invoice BETWEEN to_date('$tw','yyyy-mm-dd') AND to_date('$tk','yyyy-mm-dd')";
			} elseif ($tw != '') {
				$whtgl = " AND tanggal_invoice >= to_date('$tw','yyyy-mm-dd')";
			} elseif ($tk != '') {
				$whtgl = " AND tanggal_invoice <= to_date('$tk','yyyy-mm-dd')";
			}
			
			$whuser = $this->buildCustomerScopeWhere();
		
			$sql = "SELECT a.*, b.nama as customer
					FROM trx_ppa_invoice a
					LEFT JOIN mst_customer b ON a.customer_id = b.id
					$whuser $whtgl $wh
					ORDER BY tanggal_invoice DESC, no_invoice DESC";
		
			$query = $this->db->query($sql);
			return $query->result_array();
		}
	
		private function buildCustomerScopeWhere() {
			$role = $this->session->userdata('group');
			$column = $this->session->userdata('filter_user_column');
			$id = $this->session->userdata('filter_user_id');

			if ($role === '100') {
				return ' WHERE 1=1';
			}

			if ($column === 'customer_id' && $id) {
				return ' WHERE customer_id = ' . intval($id);
			}

			return ' WHERE 1=0';
		}
}
