<!-- Toast Container (pojok kanan atas) -->
<div class="toast-container position-fixed top-c40 end-c20 p-3" style="z-index: 1055">
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



<main class="content">
  <div class="d-flex align-items-center gap-2 page-title">
    <i class="fa-solid fa-ship fa-xl"></i>
    <span>Daftar Proses Permohonan PPA</span>
  </div>
<hr style="margin: 0px; margin-bottom: 15px">
  <!-- Table -->
  <div class="bg-white shadow-sm p-2">
    <table id="ppaProses" class="table table-striped table-bordered nowrap w-100">
      <thead class="table-light">
        <tr>
          <th>Nomor PPA</th>
          <th>Status</th>
          <th>Tanggal</th>
          <th>Data Permohonan</th>
          <th>Nama Kapal</th>
          <th>Nama Tongkang</th>
          <th>Jns. Muatan</th>
          <th>Tonase</th>
          <th>Tarif - Kurs</th>
          <th>Biaya Alur</th>
          <th>PPn</th>
          <th>Adm.&nbsp;&nbsp;&nbsp;&nbsp;</th>
          <th>Total Bayar&nbsp;&nbsp;&nbsp;&nbsp;</th>
          <th>Customer&nbsp;&nbsp;&nbsp;&nbsp;</th>
          <th>Agen&nbsp;&nbsp;&nbsp;&nbsp;</th>
          <th>Type Bayar &nbsp;&nbsp;&nbsp;&nbsp;</th>
          <th>Jenis Pengiriman &nbsp;&nbsp;&nbsp;&nbsp;</th>
          <th>Pelabuhan Asal &nbsp;&nbsp;&nbsp;&nbsp;</th>
          <th>Plbhn. Tujuan → Tujuan Akhir &nbsp;&nbsp;&nbsp;&nbsp;</th>
          <th>Keterangan &nbsp;&nbsp;&nbsp;&nbsp;</th>
          <th>Link Aksi &nbsp;&nbsp;&nbsp;&nbsp;</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</main>
<!-- Modal Konfirmasi Batal -->
<div class="modal fade" id="modalBatal" tabindex="-1" aria-labelledby="modalBatalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning text-white">
        <h5 class="modal-title" id="modalBatalLabel">Konfirmasi Pembatalan Revisi Permohonan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body">
        <p>Anda yakin membatalkan revisi permohonan nomor:  <strong id="nomorPPA"></strong> ?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary" id="btnConfirmBatal">Batalkan Permohonan</button>
      </div>
    </div>
  </div>
</div>
<script>
(function () {
  let ppaProsesDataTable = null;
  function debounce(fn, wait) {
    let t;
    return function () {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(this, arguments), wait);
    };
  }

  function getTableScrollY() {
    const topbarHeight = document.querySelector('.topbar')?.offsetHeight || 0;
    const filterHeight = document.querySelector('#mainContent .filter-bar')?.offsetHeight || 0;
    const padding = 200;
    return window.innerHeight - topbarHeight - filterHeight - padding;
  }

  function initPPAProsesDataTable() {
    const $tbl = $('#ppaProses');

    if ($.fn.DataTable.isDataTable('#ppaProses')) return;

    ppaProsesDataTable = $tbl.DataTable({
      processing: true,
      serverSide: true,
      pageLength: 69,
      scrollX: true,
      scrollY: getTableScrollY() + 'px',
      scrollCollapse: true,
      responsive: false, // Disabled due to compatibility issues with AJAX-loaded content
      dom: 't<"bottom-info"ip>',
      language: { url: "<?= base_url('templates/js/en-GB.json'); ?>" },

      ajax: {
        url: "<?= base_url('ppa_proses/getDataUntukProses'); ?>",
        type: "POST",
        data: function (d) {
  const colIndex = d.order[0]?.column ?? 1;
  const colDef   = d.columns[colIndex] || {};// || {} provides a fallback empty object if d.columns[colIndex] is undefined or invalid, preventing runtime errors.
  const colName  = colDef.name || colDef.data || 'tanggal';
  return {
    draw: d.draw,
    start: d.start,
    length: d.length,
    orderColumn: colName,
    orderDir: d.order[0]?.dir || 'desc'
  };
}
      },

      columns: [
        { data: "NO_PPA" },
        { data: "STATUS_TEXT", name: "status_id" }, // add name -> DB column
        { data: "TANGGAL" },
        { data: "CETAK",
          className: "text-center",
          render: function (data, type, row) {
            if (!row.CETAK) {
              return '<span class="text-muted">PPA Manual</span>'; 
            }
            return `<a href="<?= base_url('ppa/draft') ?>/${row.CETAK}" target="_blank">DRAFT</a> | 
                    <a href="<?= base_url('ppa/skab') ?>/${row.CETAK}" target="_blank">SKAB</a> | 
                    <a href="<?= base_url('ppa/slip') ?>/${row.CETAK}" target="_blank">SLIP</a>`;
          }
        },
        { data: "NAMA_KAPAL" },
        { data: "NAMA_TONGKANG" },
        { data: "JENIS_MUATAN" },
        { data: "BERAT_MUATAN", className: "text-end", render: $.fn.dataTable.render.number('.', ',', 3, '') },
        { data: "TARIF_USD",    className: "text-end", render: $.fn.dataTable.render.number('.', ',', 2, '') },
        { data: "NILAI_USD",    className: "text-end", render: $.fn.dataTable.render.number('.', ',', 0, '') },
        { data: "PPN",          className: "text-end", render: $.fn.dataTable.render.number('.', ',', 0, '') },
        { data: "BY_ADMIN",     className: "text-end", render: $.fn.dataTable.render.number('.', ',', 0, '') },
        { data: "TOTAL_USD",    className: "text-end", render: $.fn.dataTable.render.number('.', ',', 0, '') },
        { data: "NAMA_CUSTOMER" },
        { data: "NAMA_AGEN" },
        { data: "TYPEBAYAR" },
        { data: "JENISKIRIM" },
        { data: "PELABUHAN_ASAL" },
        {
          data: null,
          render: (data, type, row) =>
              row.PELABUHAN_TUJUAN + (row.PELABUHAN_TUJUAN_AKHIR ? ' → ' + row.PELABUHAN_TUJUAN_AKHIR : ''),
        },
        { data: "KETERANGAN" },
        { data: "ID",
  render: function (data, type, row) {
    return `
${ row.showKembalikanButton ?
  `<a href="javascript:void(0)" onclick="actionKembalikan(${data}, '${row.NO_PPA}')">KEMBALIKAN</a>&nbsp;&nbsp;|&nbsp;&nbsp;` : '' }

${ row.showTerimaButton ?
  `<a href="javascript:void(0)" onclick="actionTerima(${data}, '${row.NO_PPA}')">TERIMA</a>&nbsp;&nbsp;|&nbsp;&nbsp;` : '' }
      
${ row.showApproveButton ?
  `<a href="javascript:void(0)" onclick="actionApprove(${data}, '${row.NO_PPA}')">APPROVE</a>&nbsp;&nbsp;|&nbsp;&nbsp;` : '' }
      &nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:void(0)" onclick="addTab('<?= base_url('permohonan/revisi') ?>/${data}', false)">UBAH DATA</a>
    `;
  }
}

      ],
      order: [[1, 'desc']], 
      fixedHeader: { header: true, footer: true },

      initComplete: function () {
        updateHeight(false);
      }
    });

    function updateHeight(redraw) {
      if (!ppaProsesDataTable) return;
      const container = $(ppaProsesDataTable.table().container()).find('.dataTables_scrollBody');
      if (container.length) container.css('height', getTableScrollY() + 'px');
      if (ppaProsesDataTable.fixedHeader) {
        try { ppaProsesDataTable.fixedHeader.adjust(); } catch (e) {}
      }
      if (redraw) ppaProsesDataTable.draw(false);
    }

    window.addEventListener('resize', debounce(() => updateHeight(false), 150));

  }

  $(function () {
    $.fn.dataTable.ext.errMode = 'none';
    // Ensure DataTables is fully loaded before initializing
    if (typeof $.fn.dataTable !== 'undefined' && typeof $.fn.DataTable !== 'undefined') {
      initPPAProsesDataTable();
    } else {
      // Retry after a short delay if DataTables isn't ready yet
      setTimeout(function() {
        if (typeof $.fn.dataTable !== 'undefined' && typeof $.fn.DataTable !== 'undefined') {
          initPPAProsesDataTable();
        }
      }, 100);
    }
  });

  // Expose globally (and keep legacy name for callers)
  window.initPPAProsesDataTable = initPPAProsesDataTable;
  window.initPPAPROSESDataTable = initPPAProsesDataTable;
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



function actionKembalikan(id, noPpa) {
  $.post("<?= base_url('ppa_proses/kembalikan') ?>/" + id, function(res) {
    if (res.success) {
      showToast(`<i class="fa-solid fa-circle-check"></i> ${res.msg}`, "success");
      $('#ppaProses').DataTable().ajax.reload(null, false);
    } else {
      showToast(`<i class="fa-solid fa-circle-exclamation"></i> ${res.msg}`, "error");
      $('#ppaProses').DataTable().ajax.reload(null, false);
    }
  }, "json");
}

function actionTerima(id, noPpa) {
  $.post("<?= base_url('ppa_proses/terima') ?>/" + id, function(res) {
    if (res.success) {
      showToast(`<i class="fa-solid fa-circle-check"></i> ${res.msg}`, "success");
      $('#ppaProses').DataTable().ajax.reload(null, false);
    } else {
      showToast(`<i class="fa-solid fa-circle-exclamation"></i> ${res.msg}`, "error");
      $('#ppaProses').DataTable().ajax.reload(null, false);
    }
  }, "json");
}

function actionApprove(id, noPpa) {
  $.post("<?= base_url('ppa_proses/approve') ?>/" + id, function(res) {
    if (res.success) {
      showToast(`<i class="fa-solid fa-circle-check"></i> ${res.msg}`, "success");
      $('#ppaProses').DataTable().ajax.reload(null, false);
    } else {
      showToast(`<i class="fa-solid fa-circle-exclamation"></i> ${res.msg}`, "error");
      $('#ppaProses').DataTable().ajax.reload(null, false);
    }
  }, "json");
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


window.currentBatalId = null;

function showBatalModal(id, noPpa) {
  currentBatalId = id; 
  document.getElementById('nomorPPA').textContent = noPpa;

  const modal = new bootstrap.Modal(document.getElementById('modalBatal'));
  modal.show();

  const btnConfirm = document.getElementById('btnConfirmBatal');
  if (btnConfirm) {
    btnConfirm.onclick = null; // reset listener

    btnConfirm.onclick = function () {
      if (!currentBatalId) return;

      const $btnConfirm = btnConfirm; // simpan biar bisa dipakai di ajax

      $.ajax({
        url: "<?= base_url('permohonan/prosesBatal') ?>/" + currentBatalId,
        type: "POST",
        dataType: "json",
        beforeSend: function () {
          $btnConfirm.disabled = true;
        },
        success: function (res) {
          if (res.success) {
            showToast(
              `<i class="fa-solid fa-circle-check"></i>Permohonan nomor: <strong>${noPpa}</strong><br>${res.msg}`,
              "success"
            );
            setTimeout(() => {
              if ($.fn.DataTable.isDataTable('#ppaProses')) {
                // reload tanpa mereset halaman dan tanpa mengubah paging
                $('#ppaProses').DataTable().ajax.reload(null, false);
              } else {
                // kalau belum terinisialisasi (safety), panggil init
                initPPAProsesDataTable();
              }
            }, 500);
          } else {
            showToast(
              `<i class="fa-solid fa-circle-exclamation"></i> ${res.msg}`,
              "error"
            );
            setTimeout(() => {
              if ($.fn.DataTable.isDataTable('#ppaProses')) {
                // reload tanpa mereset halaman dan tanpa mengubah paging
                $('#ppaProses').DataTable().ajax.reload(null, false);
              } else {
                // kalau belum terinisialisasi (safety), panggil init
                initPPAProsesDataTable();
              }
            }, 500);
          }
        },
        error: function (xhr, status, error) {
          showToast(
            `<i class="fa-solid fa-circle-radiation"></i> Terjadi kesalahan: ${error}`,
            "error"
          );
        },
        complete: function () {
          $btnConfirm.disabled = false;

          // tutup modal setelah selesai
          const modalEl = document.getElementById('modalBatal');
          const modal = bootstrap.Modal.getInstance(modalEl);
          modal.hide();
        }
      });
    };
  }
}


function showToast(message, type = "success") {
  const toastEl = document.getElementById("liveToast");
  const toastBody = document.getElementById("toast-msg");

  if (!toastEl || !toastBody) return;

  toastEl.classList.remove(
    "bg-success","bg-danger","bg-warning","bg-info",
    "bg-primary","bg-secondary","bg-dark","bg-light"
  );

  if (type === "success") toastEl.classList.add("bg-success");
  if (type === "error")   toastEl.classList.add("bg-danger");
  if (type === "warning") toastEl.classList.add("bg-warning");
  if (type === "info")    toastEl.classList.add("bg-info");

  toastBody.innerHTML = message;

  const bsToast = new bootstrap.Toast(toastEl, { autohide: true, delay: 5000 });
  bsToast.show();
}



</script>
