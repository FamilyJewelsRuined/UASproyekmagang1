<style type="">
    @media (max-width: 850px) {
        .filterbar850 {
            display: none;
        }
        .filterbar850min{
            display: inline;
        }
    }
    @media (min-width: 850px) {
        .filterbar850 {
            display: inline;
        }
        .filterbar850min{
            display: none;
        }
    }

.sidebar,
.navbar,
#sidebar {
  position: fixed; 
  z-index: 2000;
}

.main-content,
.content {
  position: relative;
  z-index: 100;       
}

</style>

<!-- Modal Draft, Skab & Slip -->
<div>
<div class="modal fade" id="draftModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl" style="margin-top: 65px;">
    <div class="modal-content" style="width:80%; margin-left: 230px;">
    <div style="background-color: #343b4a; color: #2981b9;" class="modal-header d-flex justify-content-between align-items-center">
        <h style=" font-size: 22px;">я тебя люблю</h>
        <h5 class="modal-title m-0"></h5>
        <div>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
      </div>
      <div class="modal-body p-0" style="height:75vh;">
        <iframe id="draftFrame" style="border:none; width:100%; height:100%; transition:transform 0.2s ease;"></iframe>
      </div>
    </div>
  </div>
</div>


<main class="content">
  <div class="d-flex align-items-center page-title">
    <i class="fa-solid fa-ship fa-xl"></i>
    <span>Daftar Permohonan PPA</span>
  </div>
  <hr class="hr-title">
  
  <div id="contentWrapper" style="position: relative;">
    <!-- Spinner overlay -->
    <div id="ppaSpinner" class="loading-cust">
      <div class="spinner-border" style="color:#ff7315;" role="status" style="width: 2.5rem; height: 2.5rem;">
        <span class="visually-hidden">Loading . . . </span>
      </div>
      <div style="margin-top:10px;">Loading data, please wait...</div>
    </div>
  <!-- Filter Bar -->
  <div class="filter-bar bg-white shadow-sm" style="padding: 8px 8px 0px 8px">
    <div class="row g-2 align-items-center">
      <div class="col-12 col-md-2 filterbar850">
        <select class="form-select" id="filterField">
          <option value="ALL">All</option>
          <option value="NO_PPA">Nomor PPA</option>
          <option value="NAMA_KAPAL">Nama Kapal</option>
          <option value="NAMA_TONGKANG">Nama Tongkang</option>
        </select>
      </div>     
      <div class="col-12 col-md-3 filterbar850">
        <input type="text" class="form-control" id="filterQuery" placeholder="Ketik kata kunci...">
      </div>
      <div class="col-12 col-md-5 filterbar850min">
        <input type="text" class="form-control" id="filterQuery" placeholder="Cari Nomor PPA, Kapal, Tongkang">
      </div>
      <div class="col-12 col-md-4 filterbar850">
        <div class="d-flex align-items-center gap-2">
          <input type="date" class="form-control" id="tw">
          sd
          <input type="date" class="form-control" id="tk">
        </div>
      </div>
      <div class="col-12 col-md-3 text-md-end filterbar850">
        <button class="btn btn-outline-secondary me-2" id="btnExcel"><i class="fa-regular fa-file-excel me-1"></i> Excel</button>
        <button class="btn btn-outline-secondary" id="btnPrint"><i class="fa-solid fa-print me-1"></i> Print</button>
      </div> 
    </div>
  </div>

  <!-- Table -->
  <div class="bg-white shadow-sm p-2">
    <table id="ppa" class="table table-striped table-bordered nowrap w-100">
      <thead class="table-light">
        <tr>
          <th>Nomor PPA</th>
          <th>Tanggal</th>
          <th>Status</th>
          <th>Pilihan Cetak</th>
          <th>Nama Kapal</th>
          <th>Nama Tongkang</th>
          <th>Jns. Muatan</th>
          <th>Tonase</th>
          <th>Tarif - Kurs</th>
          <th>Biaya Alur</th>
          <th>PPn&nbsp;&nbsp;&nbsp;&nbsp;</th>
          <th>Adm.&nbsp;&nbsp;&nbsp;&nbsp;</th>
          <th>Total Bayar&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    <?php 
          $CI =& get_instance(); 
          ?>
          <?php if($CI->session->userdata('group')=='101'){ ?>
              <th>Customer&nbsp;&nbsp;&nbsp;&nbsp; </th>
          <?php } else if(in_array($CI->session->userdata('group'), array('102','103'))){ ?>
              <th>Agen&nbsp;&nbsp;&nbsp;&nbsp; </th>
          <?php } else { ?>  
              <th>Customer&nbsp;&nbsp;&nbsp;&nbsp; </th>
              <th>Agen&nbsp;&nbsp;&nbsp;&nbsp; </th>
          <?php } ?>
          <th>Type Bayar&nbsp;&nbsp;&nbsp;&nbsp;</th>
          <th>Jenis Pengiriman&nbsp;&nbsp;&nbsp;&nbsp;</th>
          <th>Pelabuhan Asal&nbsp;&nbsp;&nbsp;&nbsp;</th>
          <th>Plbhn. Tujuan → Tujuan Akhir&nbsp;&nbsp;&nbsp;&nbsp;</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>
</main>


<div class="toast-container position-fixed end-0 p-3"
     style="top: 60px; z-index: 1055;">
  <div id="liveToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toast-msg">
        <!-- Pesan dari JS -->
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto"
        data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>


<!-- Load jQuery and DataTables if not already loaded -->
<script src="<?= base_url('templates/js/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('templates/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('templates/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('templates/js/dataTables.bootstrap5.min.js') ?>"></script>

<script>          
  
function showToast(message, type = "info") {
    const toastEl = document.getElementById('liveToast');

    $("#liveToast")
        .removeClass("bg-success bg-danger bg-warning bg-info bg-primary bg-secondary bg-dark bg-light")
        .addClass(`bg-${type}`);

    $("#toast-msg").html(message);

    new bootstrap.Toast(toastEl, { autohide: true, delay: 4000 }).show();
}

(function () {
  let ppaDataTable = null;

  function debounce(fn, wait) {
    let t;
    return function () {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(this, arguments), wait);
    };
  }

  function initPPADataTable() {
    const $tbl = $('#ppa');

    if ($.fn.DataTable.isDataTable('#ppa')) return;

    ppaDataTable = $tbl.DataTable({
      processing: true,
      serverSide: true,
      pageLength: 15,
      scrollX: false,   
      scrollCollapse: true,
      dom: 't<"bottom-info"ip>',  
      language: { url: "<?= base_url('templates/js/en-GB.json'); ?>",
        info: "_START_ to _END_ of _TOTAL_ ",  
      },  
      pagingType: "numbers",
            
      ajax: {
        url: "<?= base_url('ppa/getData'); ?>",
        type: "POST",
        data: function (d) {
          const colIndex = d.order[0]?.column ?? 1; 
          const colName  = d.columns[colIndex]?.data ?? 'TANGGAL';
          return {
            draw: d.draw,
            start: d.start,
            length: d.length,
            tw: $('#tw').val() || '',
            tk: $('#tk').val() || '',
            filterField: $('#filterField').val() || 'ALL',
            filterQuery: $('#filterQuery').val() || '',
            orderColumn: colName,
            orderDir: d.order[0]?.dir || 'desc'
          };
        }
      },

      columns: [
        { data: "NO_PPA" },
        { data: "TANGGAL" },
        { data: "STATUS_NAMA"},
        { data: "CETAK",
          className: "text-center",
          render: function (data, type, row) {
            if (!row.CETAK) return '<span class="text-muted">PPA Manual</span>';

            let links = [];

            // PPA: tampilkan jika status_id bukan 8 atau 9
            if (row.STATUS_ID != 8 && row.STATUS_ID != 9) {
              links.push(`<a href="<?= base_url('ppa/printPpa') ?>/${row.CETAK}" target="_blank">PPA</a>`);
            }

            if (row.STATUS_ID == 5 && row.TYPEBAYAR === 'PRA') {
              links.push(`<a href="<?= base_url('ppa/printNota') ?>/${row.CETAK}" target="_blank">NOTA</a>`);
              links.push(`<a href="<?= base_url('ppa/Faktur') ?>/${row.CETAK}" target="_blank">FAKTUR</a>`);
            }

            // Add DRAFT, SKAB, SLIP links
            links.push(`<a href="javascript:void(0)" onclick="openDocModal('draft', '${row.CETAK}')">DRAFT</a>`);
            links.push(`<a href="javascript:void(0)" onclick="openDocModal('skab', '${row.CETAK}')">SKAB</a>`);
            links.push(`<a href="javascript:void(0)" onclick="openDocModal('slip', '${row.CETAK}')">SLIP</a>`);

            return links.join(' | ');
          }
        },
        { data: "NAMA_KAPAL" },
        { data: "NAMA_TONGKANG" },
        { data: "JENIS_MUATAN" },
        { data: "BERAT_MUATAN", className: "text-end", render: $.fn.dataTable.render.number('.', ',', 3, '') },
        //{ data: "TARIF_USD",    className: "text-end", render: $.fn.dataTable.render.number('.', ',', 2, '') },
        { data: "TARIF_USD",
          className: "text-end",
          render: function (data, type, row) {
            let tarif   = parseFloat(row.TARIF_USD) || 0;
            let trxkpjk = parseFloat(row.TRXKPJK) || 0;

            // formatter bawaan datatable
            let format0 = $.fn.dataTable.render.number('.', ',', 0, '').display;
            let format2 = $.fn.dataTable.render.number('.', ',', 2, '').display;

            if (row.ISIDR == 3) {
              return format0(tarif);
            } else {
              return format2(tarif) + " - " + format0(trxkpjk);
            }
          }
        },
        { data: "NILAI_USD",    className: "text-end", render: $.fn.dataTable.render.number('.', ',', 0, '') },
        { data: "PPN",          className: "text-end", render: $.fn.dataTable.render.number('.', ',', 0, '') },
        { data: "BY_ADMIN",     className: "text-end", render: $.fn.dataTable.render.number('.', ',', 0, '') },
        { data: "TOTAL_USD",    className: "text-end", render: $.fn.dataTable.render.number('.', ',', 0, '') },
        <?php if($CI->session->userdata('group')=='101'){ ?>
          { data: "NAMA_CUSTOMER" },
        <?php } else if(in_array($CI->session->userdata('group'), array('102','103'))){ ?> 
          { data: "NAMA_AGEN" },
        <?php } else { ?>
          { data: "NAMA_CUSTOMER" },
          { data: "NAMA_AGEN" },
        <?php } ?>

        { data: "TYPEBAYAR" },
        { data: "JENISKIRIM" },
        { data: "PELABUHAN_ASAL" },
        {
          data: null,
          render: (data, type, row) =>
              row.PELABUHAN_TUJUAN + (row.PELABUHAN_TUJUAN_AKHIR ? ' → ' + row.PELABUHAN_TUJUAN_AKHIR : ''),
        },
        
      ],
      order: [[1, 'desc']], 
      fixedHeader: { header: true, footer: true },

    });

    $tbl.on('processing.dt', function (e, settings, processing) {
      $('#ppaSpinner').toggle(processing); // tampilkan jika processing=true
    });
    

    $('#filterField, #filterQuery, #tw, #tk').off('.ppa')
      .on('change.ppa keyup.ppa', debounce(() => {
        if (ppaDataTable) ppaDataTable.ajax.reload(null, true);
      }, 300));
  }
  $(function () {

    initPPADataTable();
  });

  window.initPPADataTable = initPPADataTable;
})();


function postToController(url, params) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    form.target = '_blank'; // buka di tab baru

    for (const key in params) {
        if (params.hasOwnProperty(key)) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = params[key];
            form.appendChild(input);
        }
    }

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function openInNewTab(url, params) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    form.target = '_blank';

    for (const key in params) {
        if (params.hasOwnProperty(key)) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = params[key];
            form.appendChild(input);
        }
    }

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}


$('#btnExcel').off('click').on('click', function(e) {
    e.preventDefault();
    const tw = $('#tw').val();
    const tk = $('#tk').val();

    if (!tw || !tk) {
        showToast(`<i class="fa-solid fa-circle-exclamation"></i> Tanggal Awal dan Tanggal Akhir harus diisi!`, "warning");        
        return;
    }
    const params = {
        tw: $('#tw').val(),
        tk: $('#tk').val(),
        filterField: $('#filterField').val(),
        filterQuery: $('#filterQuery').val()
    };
    openInNewTab('<?= base_url("ppa/exportExcel") ?>', params);
});

$('#btnPrint').off('click').on('click', function(e) {
    e.preventDefault();
    
    const tw = $('#tw').val();
    const tk = $('#tk').val();

    if (!tw || !tk) {
        showToast(`<i class="fa-solid fa-circle-exclamation"></i> Tanggal Awal dan Tanggal Akhir harus diisi!`, "warning");        
        return;
    }
    const params = {
        tw: $('#tw').val(),
        tk: $('#tk').val(),
        filterField: $('#filterField').val(),
        filterQuery: $('#filterQuery').val()
    };
    openInNewTab('<?= base_url("ppa/printData") ?>', params);
});




</script>
