<?php
	class model_permohonan extends CI_Model {
		//var $tb = 'ppa';
		
		function __construct(){
			parent::__construct();

		}
		function getAccountAgen(){  
			$user = $this->session->userdata('id_user');
			$this->load->database();
			$sql = "select nama, NPWP, alamat, telepon, contact, ALAMAT_BJM
					from mst_agen
					where id='$user'";  
			$query = $this->db->query($sql)->row_array();
			$this->db->close();
			return $query;
		}
	
		public function kapalSearch($q)
{
    $this->db->select('id, nama, grt');
    $this->db->from('kapal');
    $this->db->like('nama', $q);
    $this->db->order_by('nama', 'ASC');

    $query = $this->db->get();
    $result = [];

    foreach ($query->result() as $row) {
        $result[] = [
            'ID'   => $row->id,
            'NAMA' => $row->nama,
            'GT_KAPAL' => $row->grt
        ];
    }

    return $result;
}

	function tongkangSearch($q = null){
		$this->db->select('id, nama, grt');
		$this->db->from('kapal');

		if (!empty($q)) {
			$this->db->like('nama', $q);
		}
		// Catatan: Jika ada kolom kategori untuk membedakan tongkang, uncomment baris berikut:
		// $this->db->where('kategori', 'B');
		$this->db->order_by('nama', 'ASC');

		$query = $this->db->get();
		$result = [];

		foreach ($query->result() as $row) {
			$result[] = [
				'ID'   => $row->id,
				'NAMA' => $row->nama,
				'GT_TONGKANG' => $row->grt
			];
		}

		return $result;
	}

		function kapalGetById($id = null){
			$this->db->select('ID, NAMA, ISI_KOTOR');
			$this->db->from('kapal');

			if (!empty($id)) {
				$this->db->where('ID', $id); 
			}
			return $this->db->get()->row_array();
		}

		function pelabuhanSearch($q = null){
			$this->db->select('ID, NAMA');
			$this->db->from('pelabuhan');

			if (!empty($q)) {
				$this->db->like('UPPER(nama)', strtoupper($q));
			}
			$this->db->order_by('NAMA', 'ASC');

			return $this->db->get()->result();
		}

		function pelabuhanGetById($id = null){
			$this->db->select('ID, NAMA');
			$this->db->from('pelabuhan');

			if (!empty($id)) {
				$this->db->where('ID', $id); 
			}
			return $this->db->get()->row_array();
		}

		function surveyorSearch($q = null){
			$this->db->select('ID, NAMA');
			$this->db->from('surveyor');

			if (!empty($q)) {
				$this->db->like('UPPER(nama)', strtoupper($q));
			}
			$this->db->order_by('NAMA', 'ASC');

			return $this->db->get()->result();
		}

		function surveyorGetById($id = null){
			$this->db->select('ID, NAMA');
			$this->db->from('surveyor');

			if (!empty($id)) {
				$this->db->where('ID', $id); 
			}
			return $this->db->get()->row_array();
		}

		function customerSearch($q = null)
		{
			$this->db->select('id AS ID, nama AS NAMA, kode AS KODE, type_bayar AS TYPE_BAYAR, npwp AS NPWP, alamat AS ALAMAT_NPWP, pajak AS PPN_PERSEN');
			$this->db->from('customer');
		
			if (!empty($q)) {
				$this->db->like('nama', $q);
			}
		
			$this->db->order_by('nama', 'ASC');
		
			return $this->db->get()->result();
		}
		
		function customerGetById($id = null)
		{
			$this->db->select('ID, KODE, TYPE_BAYAR, NAMA, PAJAK AS PPN_PERSEN, NPWP, ALAMAT AS ALAMAT_NPWP');
			$this->db->from('customer');
		
			if (!empty($id)) {
				$this->db->where('ID', $id);
			}
		
			return $this->db->get()->row_array();
		}
			
//can i like make it not hardcoded?
	function muatanSearch($q = null){
		$this->db->select('id, nama, tarif, tarif_idr');
		$this->db->from('jenis_muatan');

		if (!empty($q)) {
			$this->db->like('nama', $q);
		}
		$this->db->order_by('nama', 'ASC');

		$query = $this->db->get();
		$result = [];

		foreach ($query->result() as $row) {
			$result[] = [
				'ID'   => $row->id,
				'NAMA' => $row->nama,
				'TARIF' => isset($row->tarif) ? floatval($row->tarif) : 0,
				'TARIF_IDR' => isset($row->tarif_idr) ? floatval($row->tarif_idr) : 0
			];
		}

		return $result;
	}
/*
function tongkangSearch($q = null){
		$this->db->select('id, nama, grt');
		$this->db->from('kapal');

		if (!empty($q)) {
			$this->db->like('nama', $q);
		}
		// Catatan: Jika ada kolom kategori untuk membedakan tongkang, uncomment baris berikut:
		// $this->db->where('kategori', 'B');
		$this->db->order_by('nama', 'ASC');

		$query = $this->db->get();
		$result = [];

		foreach ($query->result() as $row) {
			$result[] = [
				'ID'   => $row->id,
				'NAMA' => $row->nama,
				'GT_TONGKANG' => $row->grt
			];
		}

		return $result;
	}
*/


		function muatanGetById($id = null){
			$this->db->select('ID, NAMA, TARIF, TARIF_IDR');
			$this->db->from('jenis_muatan');

			if (!empty($id)) {
				$this->db->where('ID', $id); 
			}
			return $this->db->get()->row_array();
		}	


	}
?>