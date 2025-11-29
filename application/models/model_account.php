<?php
	class model_account extends MY_Model {
		//var $tb = 'TRX_PPA';
		
		function model_account(){
			parent::__construct();
		}
		function getAccountAgen(){  
			$user = $this->session->userdata('id');
			$this->load->database();
			$sql = "select nama, NPWP, alamat, telepon, contact, ALAMAT_BJM
					from mst_agen
					where id='$user'";  
			$query = $this->db->query($sql)->row_array();
			$this->db->close();
			return $query;
		}

		function getAccountCustomer(){    
			$user = $this->session->userdata('id');
			$this->load->database();
			$sql =  "select 
					NAMA, NPWP, ALAMAT, 
					CASE 
						WHEN TYPE_BAYAR = 1 THEN 'PRA BAYAR'
						WHEN TYPE_BAYAR = 2 THEN 'PASCA BAYAR'
						ELSE 'LAINNYA'
					END AS TYPEBAYAR, 
					CONTACT, TELPON AS TELEPON, ALAMAT_CONTACT
					from mst_customer
					where id=$user      
					";  
			$query = $this->db->query($sql)->row_array();
			$this->db->close();
			return $query;
		}
		


	}
?>