<main class="content">
    <div class="d-flex align-items-center gap-2 page-title">
        <i class="fa-solid fa-file-invoice fa-xl"></i>
        <span>Daftar Invoice Pasca Bayar</span>
        <?php if($this->session->userdata('group')=='101'){ ?>
            <span class="badge bg-primary ms-2">Agen</span>
        <?php }elseif($this->session->userdata('group')=='102' || $this->session->userdata('group')=='103'){ ?>
            <span class="badge bg-success ms-2">Customer</span>
        <?php } ?>
    </div>
    <hr style="margin-bottom: 1px">

    <div class="filter-bar bg-white shadow-sm p-3">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-2">
                <select class="form-select" id="filterField">
                    <option value="NO_INVOICE">Nomor Invoice</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <input type="text" class="form-control" id="filterQuery" placeholder="Cari...">
            </div>
            <div class="col-12 col-md-4">
                <div class="d-flex align-items-center gap-2">
                    <input type="date" class="form-control" id="tw">
                    sd
                    <input type="date" class="form-control" id="tk">
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-sm p-2">
        <table id="invoice" class="table table-striped table-bordered nowrap w-100">
            <thead class="table-light">
                <tr>
                    <th>Nomor Invoice</th>
                    <th>Pilihan Cetak</th>
                    <th>Tgl. Invoice</th>
                    <th>Jml. PPA</th>
                    <th>Tonase</th>
                    <th>Curr Tagihan</th>
                    <th>Biaya Alur</th>
                    <th>PPn</th>
                    <th>Adm.</th>
                    <th>Total Tagihan</th>
                    <th>Total Bayar</th>
                    <th>Sisa Invoice</th>
                    <th>Awal Periode</th>
                    <th>Akhir Periode</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</main>

<script>
(function(){
    function fmtNumber(decimals){
        return $.fn.dataTable.render.number('.', ',', decimals || 0, '').display;
    }

    const tableElement = $('#invoice');
    let table;

    if ( $.fn.DataTable.isDataTable( tableElement ) ) {
        table = tableElement.DataTable();
        table.ajax.reload(null, false);
    } else {
        table = tableElement.DataTable({
            processing: true,
            serverSide: true,
            pageLength: 15,
            responsive: true,
            dom: 't<"bottom-info"ip>',
            deferRender: true,
            language: { url: "<?= base_url('templates/js/en-GB.json'); ?>" },
            ajax: {
                url: "<?= base_url('invoice/get_data'); ?>",
                type: 'POST',
                data: function(d){
                    const colIndex = d.order[0]?.column ?? 2;
                    const colName  = d.columns[colIndex]?.data ?? 'TANGGAL_INVOICE';
                    d.filterField = $('#filterField').val() || 'NO_INVOICE';
                    d.filterQuery = $('#filterQuery').val() || '';
                    d.tw = $('#tw').val() || '';
                    d.tk = $('#tk').val() || '';
                    d.orderColumn = colName;
                    d.orderDir = d.order[0]?.dir || 'desc';
                    d.role = '<?= $this->session->userdata("group"); ?>';
                    d.user_id = '<?= $this->session->userdata("id_user"); ?>';
                }
            },
            columns: [
                { data: 'NO_INVOICE' },
                { 
                    data: null, 
                    className: 'text-center', 
                    render: function(data, type, row){
                        let links = [];
                        if(row.POSTING==3 || row.POSTING==1) {
                            links.push(`<a href="<?= base_url('invoice/vinv'); ?>?no_invoice=${row.NO_INVOICE}" target="_blank">Invoice</a>`);
                        }
                        if(row.POSTING==3 || row.POSTING==2) {
                            links.push(`<a href="<?= base_url('invoice/vfaktur'); ?>?no_invoice=${row.NO_INVOICE}" target="_blank">Faktur</a>`);
                        }
                        return links.join(' | ') || 'file tidak tersedia';
                    }
                },
                { data: 'TANGGAL_INVOICE' },
                { data: 'JUMLAH_PPA', className:'text-center' },
                { data: 'BERAT_MUATAN', className:'text-end', render: fmtNumber(3) },
                { data: 'CUR', className:'text-center' },
                { data: 'NILAI_USD', className:'text-end', render: fmtNumber(0) },
                { data: 'PPN', className:'text-end', render: fmtNumber(0) },
                { data: 'BY_ADMIN', className:'text-end', render: fmtNumber(0) },
                { data: 'TOTAL_USD', className:'text-end', render: fmtNumber(0) },
                { data: 'PAY', className:'text-end', render: fmtNumber(0) },
                { data: 'SISA', className:'text-end', render: fmtNumber(0) },
                { data: 'TANGGAL_AWAL' },
                { data: 'TANGGAL_AKHIR' }
            ],
            order: [[2, 'desc']],
            initComplete: function(){
                try { table.columns.adjust(); } catch(e) {}
            }
        });
    }

    $('#filterField, #filterQuery, #tw, #tk').on('change keyup', function(){
        table.draw(false);
    });
})();
</script>
