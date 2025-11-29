<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {
    
    public function __construct(){
        parent::__construct();
		
    }
	function index(){
		// Initialize data array
		$data = array();
		$data['kurs'] = 0;
		$data['checkinv'] = array();
		$data['revisi'] = 0;
		$data['ppa_yearly'] = array();
		
		// Get user from session
		$user = $this->session->userdata('id_user');
		$group = $this->session->userdata('group');
		
		// Try to load database, but handle errors gracefully
		try {
			$this->load->database();
			
			// Check if database connection is successful
			if (!$this->db->conn_id) {
				throw new Exception('Database connection failed');
			}
			
			/* Get kurs data - using MySQL date format
			$tanggal = date('Y-m-d'); // MySQL date format (YYYY-MM-DD)
			$sql = "SELECT COALESCE(NULLIF(kurs_pajak, 0), kurs_estimasi, 0) AS TRXKPJK 
					FROM kurs 
					WHERE tanggal = ?";
			$query_kurs = $this->db->query($sql, array($tanggal));
			if ($query_kurs && $query_kurs->num_rows() > 0) {
				$kurs_result = $query_kurs->row_array();
				$data['kurs'] = isset($kurs_result['TRXKPJK']) ? $kurs_result['TRXKPJK'] : 0;
			}*/
			
			// Get invoice data for group 101
			if ($group == '101' && $user) {
				$sql = "SELECT 
							tanggal AS tanggal_invoice,
							invoice_no,
							COALESCE(total, 0) AS total_usd,
							COALESCE(total, 0) AS sisa
						FROM ppa_invoice
						WHERE COALESCE(total, 0) > 0
						ORDER BY tanggal DESC
						LIMIT 10";
				$query_inv = $this->db->query($sql);
				if ($query_inv) {
					$invoiceRows = $query_inv->result_array();
					// Normalize keys to match existing dashboard view expectations
					$data['checkinv'] = array_map(function($row) {
						return array(
							'TANGGAL_INVOICE' => $row['tanggal_invoice'],
							'NO_INVOICE'      => $row['invoice_no'],
							'TOTAL_USD'       => $row['total_usd'],
							'SISA'            => $row['sisa'],
							'ISIDR'           => isset($row['isidr']) ? $row['isidr'] : null,
						);
					}, $invoiceRows);
				}
			}
			
			/* Get revision count for group 101
			if ($group == '101' && $user) {
				$sql = "SELECT COUNT(id) as jumlah 
						FROM ppa 
						WHERE agen_id = ? AND status_id = 8";
				$query_rev = $this->db->query($sql, array($user));
				if ($query_rev && $query_rev->num_rows() > 0) {
					$rev_result = $query_rev->row_array();
					$data['revisi'] = isset($rev_result['jumlah']) ? $rev_result['jumlah'] : 0;
				}
			}*/
			
			// Get yearly PPA data
        $whuser = '';
			$params = array();
			if ($group == '101' && $user) {
				$whuser = " AND agen_id = ?";
				$params[] = $user;
			} elseif (($group == '102' || $group == '103') && $user) {
				$whuser = " AND customer_id = ?";
				$params[] = $user;
			}
			
			// Get yearly PPA data using MySQL YEAR() function
        $sql_ppa_yearly = "SELECT 
				YEAR(tanggal) as tahun,
            COUNT(*) as jumlah_ppa
            FROM ppa 
				WHERE status_id IN (9,0,1,2,3,4,5) $whuser
				GROUP BY YEAR(tanggal)
            ORDER BY tahun";
			
			if (!empty($params)) {
				$query_ppa_yearly = $this->db->query($sql_ppa_yearly, $params);
			} else {
				$query_ppa_yearly = $this->db->query($sql_ppa_yearly);
			}
			
			if ($query_ppa_yearly) {
				$data['ppa_yearly'] = $query_ppa_yearly->result_array();
			}
			
		} catch (Exception $e) {
			// Log error but don't stop execution
			log_message('error', 'Dashboard error: ' . $e->getMessage());
			// Set default values if database fails
		}
		
		$this->load->view('dashboard', $data);
	}
}