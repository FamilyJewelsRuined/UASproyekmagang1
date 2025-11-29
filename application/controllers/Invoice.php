<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class invoice extends MY_Controller {
    
    public function __construct(){
        parent::__construct();
        $this->urlaccess = get_class($this);
        $this->setAccessRole();
        $this->load->model('model_trx_invoice');
    }

    function index(){
        $this->load->view('view_invoice'); 
    }

    function getData() {
        error_reporting(E_ALL & ~E_NOTICE);

        // Ambil parameter dasar
        $draw   = intval($this->input->post("draw"));
        $start  = intval($this->input->post("start"));
        $length = intval($this->input->post("length"));

        
        // Sorting langsung dari JS
        $sort     = $this->input->post("orderColumn") ? $this->input->post("orderColumn") : 'TANGGAL_INVOICE';
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
        $result = $this->model_trx_invoice->dataMain($params);

        // Kirim hasil ke DataTables
        echo json_encode(array(
            "draw"            => $draw,
            "recordsTotal"    => $result['total'],
            "recordsFiltered" => $result['total'],
            "data"            => $result['rows']
        ));
    }    
    

public function exportExcelInvoice() {
    $this->setAccessRole = false;
    $tw = $this->input->post('tw');
    $tk = $this->input->post('tk');
    $filterField = $this->input->post('filterField');
    $filterQuery = $this->input->post('filterQuery');

    // Mengubah penamaan variabel agar sesuai dengan yang diharapkan oleh view
    $data['invoice'] = $this->model_trx_invoice->getReportData($tw, $tk, $filterField, $filterQuery);
    $data['excel'] = 1;
    $data['w'] = $tw; // Mengirim variabel 'w'
    $data['k'] = $tk; // Mengirim variabel 'k'
    
    $this->load->view('print/view_invoice_print', $data);
}

public function printDataInvoice() {
    $tw = $this->input->post('tw');
    $tk = $this->input->post('tk');
    $filterField = $this->input->post('filterField');
    $filterQuery = $this->input->post('filterQuery');

    $data['invoice'] = $this->model_trx_invoice->getReportData($tw, $tk, $filterField, $filterQuery);
    $data['excel']=0;

    // periode
    $data['tw']= $tw; //mainnya disini nanti
    $data['tk']= $tk;
    $this->load->view('print/view_invoice_print', $data);
}
}
