<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permohonan extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();   // ← WAJIB! ini yang hilang
        $this->load->library('session'); // Required for accessing session data
        $this->load->model('model_permohonan', 'permohonan');
        $this->load->helper(['url', 'form']);
    }

    // =============================
    //       HALAMAN FORM
    // =============================
    public function index()
{
    // ambil data kurs terbaru dari database
    $query = $this->db->query("SELECT kurs_pajak FROM kurs ORDER BY tanggal DESC LIMIT 1");
    $row = $query->row();

    $data['trxkpjk'] = $row ? $row->kurs_pajak : 0;

    // belum ada data edit → kosongkan
    $data['trxPpa'] = ['result' => []];

    // variabel dari sistem lama (wajib ada supaya view tidak error)
    $data['modul'] = '';
    $data['levelid'] = '';
    $data['group'] = '';

    $this->load->view('permohonan/view_permohonan', $data);
}


    // =============================
    //       DROPDOWN AJAX
    // =============================
    public function kapalSearch()
    {
        $this->output->set_content_type('application/json');
        $q = $this->input->get('q');
        if (empty($q)) {
            $this->output->set_output(json_encode([]));
            return;
        }
        $result = $this->permohonan->kapalSearch($q);
        $this->output->set_output(json_encode($result));
    }

    public function kapalGetById()
    {
        $id = $this->input->get('id');
        echo json_encode($this->permohonan->kapalGetById($id));
    }

    public function tongkangSearch()
    {
        $this->output->set_content_type('application/json');
        $q = $this->input->get('q');
        if (empty($q)) {
            $this->output->set_output(json_encode([]));
            return;
        }
        $result = $this->permohonan->tongkangSearch($q);
        $this->output->set_output(json_encode($result));
    }

    public function tongkangGetById()
    {
        $id = $this->input->get('id');
        echo json_encode($this->permohonan->tongkangGetById($id));
    }

    public function pelabuhanSearch()
    {
        $q = $this->input->get('q');
        echo json_encode($this->permohonan->pelabuhanSearch($q));
    }

    public function pelabuhanGetById()
    {
        $id = $this->input->get('id');
        echo json_encode($this->permohonan->pelabuhanGetById($id));
    }

    public function customerSearch()
    {
        $this->output->set_content_type('application/json');
        $q = $this->input->get('q');
        if (empty($q)) {
            $this->output->set_output(json_encode([]));
            return;
        }
        $result = $this->permohonan->customerSearch($q);
        $this->output->set_output(json_encode($result));
    }

    public function surveyorSearch()
    {
        $this->output->set_content_type('application/json');
        $q = $this->input->get('q');
        if (empty($q)) {
            $this->output->set_output(json_encode([]));
            return;
        }
        $result = $this->permohonan->surveyorSearch($q);
        $this->output->set_output(json_encode($result));
    }


    public function customerGetById()
    {
        $id = $this->input->get('id');
        echo json_encode($this->permohonan->customerGetById($id));
    }

    public function muatanSearch()
    {
        $this->output->set_content_type('application/json');
        $q = $this->input->get('q');
        if (empty($q)) {
            $this->output->set_output(json_encode([]));
            return;
        }
        $result = $this->permohonan->muatanSearch($q);
        $this->output->set_output(json_encode($result));
    }

    public function muatanGetById()
    {
        $id = $this->input->get('id');
        echo json_encode($this->permohonan->muatanGetById($id));
    }

    // =============================
    //       SIMPAN PERMOHONAN
    // =============================
    public function simpanPpa($statusId = null)
    {
        // Validasi pelabuhan
        if ($this->input->post('PELABUHAN_ID') == $this->input->post('PELABUHAN_TUJUAN_ID')) {
            echo json_encode(['success'=>false,'msg'=>'Pelabuhan asal dan tujuan tidak boleh sama!']);
            return;
        }

        // Upload file (DRAFT, SLIP, SKAB)
        $uploaded = [];
        $fileFields = ['DRAFT', 'SLIP', 'SKAB'];

        foreach ($fileFields as $field) {
            if (!empty($_FILES[$field]['name'])) {
                $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
                if ($ext != 'pdf') {
                    echo json_encode(['success'=>false,'msg'=>"File $field harus PDF"]);
                    return;
                }

                $filename = time() . "_" . $field . "." . $ext;
                $dest = FCPATH.'uploads/ppa/' . $filename;

                if (!move_uploaded_file($_FILES[$field]['tmp_name'], $dest)) {
                    echo json_encode(['success'=>false,'msg'=>"Gagal upload $field"]);
                    return;
                }

                $uploaded[strtolower($field)] = $filename;
            }
        }

        // Format tanggal
        $tanggal = date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('TANGGAL'))));

        // DATA UTAMA

// generate nomor PPA jika kosong
$noPpa = $this->input->post('NO_PPA');
if (empty($noPpa)) {
    // bebas mau format apa, ini contoh simpel:
    $noPpa = 'PPA-' . date('Ymd-His');
}

        // Auto-set agen_id if current user is an agen (role 101)
        $agenId = null;
        if ($this->session && $this->session->userdata('group')) {
            $userRole = $this->session->userdata('group');
            $filterUserColumn = $this->session->userdata('filter_user_column');
            $filterUserId = $this->session->userdata('filter_user_id');
            
            if ($userRole === '101' && $filterUserColumn === 'agen_id' && $filterUserId) {
                $agenId = intval($filterUserId);
            }
        }

        $data = [
            'no_ppa' => $noPpa,
            'tanggal'            => $tanggal,
            'kapal_id'           => $this->input->post('KAPAL_ID'),
            'gt_kapal'           => $this->input->post('GT_KAPAL'),
            'tongkang_id'        => $this->input->post('TONGKANG_ID'),
            'gt_tongkang'        => $this->input->post('GT_TONGKANG'),
            'pelabuhan_id'       => $this->input->post('PELABUHAN_ID'),
            'pelabuhan_tujuan_id'=> $this->input->post('PELABUHAN_TUJUAN_ID'),
            'customer_id'        => $this->input->post('CUSTOMER_ID'),
            'agen_id'            => $agenId, // Auto-set from session if user is agen
            'jenis_muatan_id'    => $this->input->post('JENIS_MUATAN_ID'),
            'berat_muatan'       => str_replace(",", "", $this->input->post('BERAT_MUATAN')),
            'tarif_usd'          => str_replace(",", "", $this->input->post('TARIF_USD')),
            'nilai_usd'          => str_replace(",", "", $this->input->post('NILAI_USD')),
            'ppn_persen'         => $this->input->post('PPN_PERSEN'),
            'ppn'                => str_replace(",", "", $this->input->post('PPN')),
            'total_usd'          => str_replace(",", "", $this->input->post('TOTAL_USD')),
            'keterangan'         => strtoupper($this->input->post('KETERANGAN')),
            'status_id'          => $statusId !== null ? (int) $statusId : 1,
            'created_at'         => date('Y-m-d H:i:s')
        ];

        // tambahkan file
        foreach ($uploaded as $key => $file) {
            $data[$key] = $file;
        }

        // INSERT dengan error handling eksplisit supaya mudah ditrace
        $oldDebugState = $this->db->db_debug;
        $this->db->db_debug = false;
        $inserted = $this->db->insert('ppa', $data);
        $dbError = $this->db->error();
        $this->db->db_debug = $oldDebugState;

        if (!$inserted) {
            log_message('error', 'Gagal simpan PPA: '.$dbError['message']);
            echo json_encode(['success'=>false,'msg'=>'DB error: '.$dbError['message']]);
            return;
        }

        $id = $this->db->insert_id();
        echo json_encode(['success'=>true,'msg'=>'Berhasil','id'=>$id]);
    }

}
