<?php 
class ppa extends CI_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->urlaccess = get_class($this);

        $this->load->library('session');
        $this->load->model('model_trx_ppa');
    }

    function index(){
        $this->load->view('view_ppa'); 
    }

    function getData() {
        error_reporting(E_ALL & ~E_NOTICE);

        try {
            // Ambil parameter dasar
            $draw   = intval($this->input->post("draw"));
            $start  = intval($this->input->post("start"));
            $length = intval($this->input->post("length"));

            
            // Sorting langsung dari JS
            $sort     = $this->input->post("orderColumn") ? $this->input->post("orderColumn") : 'TANGGAL';
            $orderDir = $this->input->post("orderDir") ? $this->input->post("orderDir") : 'desc';

            // Filter tanggal & field filter
            $tw    = $this->input->post("tw") ? $this->input->post("tw") : '';
            $tk    = $this->input->post("tk") ? $this->input->post("tk") : '';
            
            $search = $this->input->post("filterQuery") ? $this->input->post("filterQuery") : '';
            $field  = $this->input->post("filterField") ? $this->input->post("filterField") : 'ALL';
            // Hitung page untuk model
            $page = intval($start / $length) + 1;

            // Parameter ke model
            $params = array(
                'q'     => $search,
                'field' => $field,
                'tw'    => $tw,
                'tk'    => $tk,
                'sort'  => $sort,
                'order' => $orderDir,
                'rows'  => $length,
                'page'  => $page
            );

            // Panggil model
            $result = $this->model_trx_ppa->dataMain($params);
            
            // Check if result is valid
            if (!isset($result['total']) || !isset($result['rows'])) {
                log_message('error', 'PPA getData: Invalid result from model');
                echo json_encode(array(
                    "draw"            => $draw,
                    "recordsTotal"    => 0,
                    "recordsFiltered" => 0,
                    "data"            => array(),
                    "error"           => "Invalid result from model"
                ));
                return;
            }

            // Kirim hasil ke DataTables
            echo json_encode(array(
                "draw"            => $draw,
                "recordsTotal"    => $result['total'],
                "recordsFiltered" => $result['total'],
                "data"            => $result['rows']
            ));
        } catch (Exception $e) {
            log_message('error', 'PPA getData Error: ' . $e->getMessage());
            echo json_encode(array(
                "draw"            => intval($this->input->post("draw")),
                "recordsTotal"    => 0,
                "recordsFiltered" => 0,
                "data"            => array(),
                "error"           => $e->getMessage()
            ));
        }
    }

    function printNota($no){
        $data = $this->model_trx_ppa->getPpaByNo($no); 
        $this->load->view('print/view_ppa_nota',$data); 
    }

    function printPpa($no){
        $data = $this->model_trx_ppa->getPpaByNo($no); 
        $this->load->view('print/view_ppa_form',$data); 
    }

    function Faktur($no){
        $data['no_ppa']=$no;
        $this->load->view('view_ppa_faktur',$data);
    }

    function draft($v){
        $data['no_ppa']=$v;
        $this->load->view('view_ppa_draft',$data);
    }

    function skab($v){
        $data['no_ppa']=$v;
        $this->load->view('view_ppa_skab',$data);
    }
    function slip($v){
        $data['no_ppa']=$v;
        $this->load->view('view_ppa_slip',$data);
    } 

    public function exportExcel() {
        $this->setAccessRole = false;
        $tw = $this->input->post('tw');
        $tk = $this->input->post('tk');
        $filterField = $this->input->post('filterField');
        $filterQuery = $this->input->post('filterQuery');

        $data['ppa'] = $this->model_trx_ppa->getReportData($tw, $tk, $filterField, $filterQuery);
        $data['excel']=1;

         // periode
         $data['tw']= $tw; //mainnya disini nanti
         $data['tk']= $tk;
        
        $this->load->view('print/view_ppa_excel', $data);
    }

    public function printData() {
        $tw = $this->input->post('tw');
        $tk = $this->input->post('tk');
        $filterField = $this->input->post('filterField');
        $filterQuery = $this->input->post('filterQuery');

        $data['ppa'] = $this->model_trx_ppa->getReportData($tw, $tk, $filterField, $filterQuery);
        $data['excel']=0;

        // periode
        $data['tw']= $tw; //mainnya disini nanti
        $data['tk']= $tk;
        $this->load->view('print/view_ppa_excel', $data);
    }

    public function viewDraft($id)
    {
        $ppa = $this->db->get_where('ppa', ['id' => $id])->row();
    
        if (!$ppa) {
            show_error("PPA not found");
            return;
        }
    
        $data = [
            'no_ppa'     => $ppa->no_ppa,
            'draft_file' => $ppa->draft    // << THIS IS THE IMPORTANT PART
        ];
    
        $this->load->view('view_ppa_draft', $data);
    }
    

}