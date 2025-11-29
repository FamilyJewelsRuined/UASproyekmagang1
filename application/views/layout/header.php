<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PPA Ambapers</title>
  <link rel="icon" href="<?php echo base_url('templates/img/logo.gif');?>">


  <link rel="stylesheet" href="<?= base_url('templates/css/bootstrap.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('templates/css/custom.css') ?>">
<script src="<?= base_url('templates/js/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('templates/js/bootstrap.bundle.min.js') ?>"></script>



  <!-- Bootstrap -->
  <link href="<?php echo base_url('templates/css/bootstrap.min.css');?>" rel="stylesheet">

  <!-- Custom CSS -->
  <link href="<?php echo base_url('templates/css/custom.css');?>" rel="stylesheet">

  <!-- Login Style (agar modal relogin tampil sama dengan login utama) -->

  <link href="https://fonts.googleapis.com/css2?family=Barlow&display=swap" rel="stylesheet">

  <!-- jQuery & Bootstrap JS -->
  <script src="<?php echo base_url('templates/js/jquery-3.7.1.min.js')?>"></script>
  <script src="<?php echo base_url('templates/js/bootstrap.bundle.min.js')?>"></script>

  <!-- DataTables -->
  <script src="<?php echo base_url('templates/js/jquery.dataTables.min.js')?>"></script>
  <script src="<?php echo base_url('templates/js/dataTables.bootstrap5.min.js')?>"></script>
  <script src="<?php echo base_url('templates/js/dataTables.responsive.min.js')?>"></script>
  <script src="<?php echo base_url('templates/js/responsive.bootstrap5.min.js')?>"></script>
  <link href="<?php echo base_url('templates/css/dataTables.bootstrap5.min.css')?>" rel="stylesheet">
  <link href="<?php echo base_url('templates/css/responsive.bootstrap5.min.css')?>" rel="stylesheet">

  <!-- Select2 -->
  <link href="<?php echo base_url('templates/css/select2.min.css');?>" rel="stylesheet">
  <link href="<?php echo base_url('templates/css/select2-bootstrap-5-theme.min.css');?>" rel="stylesheet">
  <script src="<?php echo base_url('templates/js/select2.min.js');?>"></script>

  <!-- Datepicker -->
  <link href="<?php echo base_url('templates/css/bootstrap-datepicker.min.css')?>" rel="stylesheet">
  <script src="<?php echo base_url('templates/js/bootstrap-datepicker/bootstrap-datepicker.min.js');?>"></script>

  <!-- FontAwesome -->
  <link href="<?php echo base_url('templates/fontawesome/css/all.min.css');?>" rel="stylesheet">
  <link href="<?php echo base_url('templates/css/barlow-font.css');?>" rel="stylesheet">

  <!-- Chart.js -->
  <script src="<?php echo base_url('templates/js/chart.js');?>"></script>

  <!-- Modal Style Override -->
  <style>
/*#sessionExpiredModal * {
  font-family: "Font Awesome 6 Free" !important;
  font-weight: 900; /* solid style icons */



/* === Windows-like relogin modal === */
#sessionExpiredModal .modal-content {
  background: #0c1622 !important;
  border-radius: 16px;
  border: none;
  box-shadow: 0 0 40px rgba(0, 0, 0, 0.7);
  color: white;
  text-align: center;
  padding: 40px 30px;
}

/* Avatar */
#sessionExpiredModal .login-avatar {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  background-color: #2c3a4a;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 10px;
}

#sessionExpiredModal .login-avatar i {
  font-size: 50px;
  color: #cfd8e3;
}

/* Username */
#sessionExpiredModal #session-user {
  font-size: 24px;
  color: #fff;
  font-weight: 400;
  margin-bottom: 25px;
}

/* Password container */
#sessionExpiredModal .password-box {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 20px;
}

#sessionExpiredModal .password-box input {
  width: 280px;
  padding: 10px 45px 10px 15px;
  font-size: 16px;
  border-radius: 5px;
  border: none;
  outline: none;
  background-color: #1b2735;
  color: #fff;
  box-shadow: inset 0 0 3px #000;
  transition: 0.3s;
}


/* Arrow button */
#sessionExpiredModal .password-box button {
  position: absolute;
  right: 6px;
  background-color: transparent;
  border: none;
  color: #0078d7;
  font-size: 22px;
  cursor: pointer;
  transition: 0.3s;
}



/* Info text */
#sessionExpiredModal .info-text {
  font-size: 13px;
  color: #ccc;
  margin-top: 5px;
}

/* Prevent Select2, dropdowns, or other popups from appearing above modals */
.select2-container,
.select2-dropdown,
.select2-container--open,
.ui-autocomplete,
.dropdown-menu {
  z-index: 999 !important;
}

</style>

<!-- bagian pop up modal password only -->
<!-- Fallback: ensure base_url exists even for AJAX-loaded partials -->
<script>

    //mengecek apakah variabel global base_url sudah ada di browser.
    //Kalau belum ada, maka dibuat variabel window.base_url dengan nilai dari PHP base_url().
    if (typeof window.base_url === 'undefined') {
      window.base_url = '<?php echo rtrim(base_url(), "/"); ?>/'; //rtrim hapus garis miring di akhir (cuman untuk kerapian wjwkwkkwkwkwkwkwkwkkwkwkwkwkwkkwkwkwkwkwkkw)
    }
    //sama kyk sebelumnya, cumankali ini untuk variabel CI_BASE_URL.
    //dipakai khusus untuk request AJAX (karena lebih aman menggunakan format bawaan CodeIgniter).
    if (typeof window.CI_BASE_URL === 'undefined') {
      window.CI_BASE_URL = '<?php echo base_url(); ?>';
    }

    // mengambil username dari server (PHP session) dan menyimpannya di browser.
    //Kalau window.SESSION_USERNAME belum ada, atau kosong → isi dengan nama user dari session PHP.
    if (typeof window.SESSION_USERNAME === 'undefined' || !window.SESSION_USERNAME) {
      window.SESSION_USERNAME = <?php 
        $username = '';
        if (isset($this->session) && method_exists($this->session, 'userdata')) {
          $username = $this->session->userdata('username');
        }
        echo json_encode($username ? $username : '');
      ?> || '';
    }

  </script>


</head>

<body>





<!-- Modal Relogin -->
<div class="modal" id="sessionExpiredModal" tabindex="-1" aria-labelledby="sessionExpiredModalLabel" aria-hidden="true" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
    <div class="modal-header border-0 justify-content-center">
        <h5 class="modal-title text-white mb-0" id="sessionExpiredModalLabel" style="font-size:14px">
        Sesi anda telah habis. Mohon isi ulang password untuk melanjutkan.
        </h5>
      </div>
      <div class="modal-body p-4"></div>
      <div class="modal-footer justify-content-center" style="display:none;"></div>
    </div>
  </div>
</div>



<script>
//Variabel Global
var CI_BASE_URL = '<?php echo base_url(); ?>'; 
var base_url    = '<?php echo rtrim(base_url(), "/"); ?>/'; //dipakai untuk menghapus tanda “/” terakhir, lalu ditambah lagi satu “/” supaya konsisten (tidak double slash “//” di tengah).
var CI_LOGIN_URL = '<?php echo base_url('home'); ?>'; 

//Ketika butuh menampilkan form login → dia panggil RELOGIN_FORM_URL.
var RELOGIN_FORM_URL = '<?php echo base_url('home/relogin_form'); ?>';

window.openDocModal = function(type, cetakId) {
  const baseMap = {
    draft: "<?= base_url('ppa/draft/') ?>/",
    skab:  "<?= base_url('ppa/skab/') ?>/",
    slip:  "<?= base_url('ppa/slip/') ?>/"
  };

  const url = baseMap[type] + cetakId;
  $('#draftFrame').attr({
    src: url,
    
  });
  const draftModalEl = document.getElementById('draftModal');
  if (draftModalEl) {
    const draftModal = new bootstrap.Modal(draftModalEl);
    draftModal.show();
  }

  
};




//Menutup modal pop-up. Menunjukkan pesan di console bahwa session berhasil dipulihkan.
window.reRenderContent = function() {
    var sessionModal = bootstrap.Modal.getInstance(document.getElementById('sessionExpiredModal'));
    if (sessionModal) {
        sessionModal.hide();
    }
    
    // cek halaman apa yg user buka sebelum session habis
    if (window.INTENDED_DESTINATION_URL) {
        console.log('sesi kesimpan, ntar lagi ke redirect:', window.INTENDED_DESTINATION_URL);
        // redirect ke halaman 
        addTab(window.INTENDED_DESTINATION_URL, window.INTENDED_DESTINATION_TITLE);
        // hapus intended destination yg disimpan krn sdh selesai
        window.INTENDED_DESTINATION_URL = null;
        window.INTENDED_DESTINATION_TITLE = null;
    } else {
        console.log('sesi sdh di pulihkan');
    }
};

// --- FUNGSI UNTUK KONTEN AJAX ---
function initDynamicContent() {
    // contoh: $('.datatable').DataTable();
}

// mencatat halaman terakhir yang sedang dibuka.
function updateCurrentContentURL(url) {

    //otomatis memuat ulang halaman terakhir yang user buka sebelum session habis.
    window.CURRENT_CONTENT_URL = url;
    console.log('Current content URL updated to:', url);
}

// Fungsi addTab untuk memuat konten secara dinamis
function addTab(url, title) {
  //mensimpan destinasi tujuan sebelum melakukan request
  window.INTENDED_DESTINATION_URL = url;
  window.INTENDED_DESTINATION_TITLE = title || '';

  $('#mainContent').load(url, function (response, status, xhr) {
    if (status === 'error') {
        if(xhr.status!=401){    
          $('#mainContent').html(
            `<div class="alert alert-danger">Gagal memuat: ${xhr.status} ${xhr.statusText}</div>`
          );
          return;
        }
        return;
    }
    if (typeof addclass === 'function') {
        addclass();
    }
      
    $('#mainContent').find("script").each(function() {
      $.globalEval(this.text || this.textContent || this.innerHTML || '');    
    });
    
    const sidebar = document.getElementById('sidebar');
        if (sidebar && sidebar.classList.contains('show')) {
            sidebar.classList.remove('show');
        }
  });

  return false; 
}

// --- SETUP ---
$(document).ready(function(){
    var dashboardUrl = '<?php echo site_url("dashboard"); ?>';
    window.CURRENT_CONTENT_URL = dashboardUrl;
    addTab(dashboardUrl, 'Dashboard');
		
    //Kalau server mengirim kode 401 (Unauthorized / session habis),
//tampilkan modal dengan tulisan “Loading login form..
    function handle401Error(xhr) {
        if (!$('#sessionExpiredModal').hasClass('show')) {
            var sessionModal = new bootstrap.Modal(document.getElementById('sessionExpiredModal'));
            var modalBody = $('#sessionExpiredModal .modal-body');
            var modalHeader = $('#sessionExpiredModal .modal-header h5');
            
           // modalHeader.text('Sesi anda telah habis. mohon isi ulang password untuk melanjutkan');
            modalBody.html('<div class="text-center text-white"><i class="fas fa-spinner fa-spin me-2"></i> Loading login form...</div>');
            
            //Ambil isi tampilan relogin.php (form login kecil) dari server, lalu masukkan ke dalam modal.
            $.ajax({
                url: RELOGIN_FORM_URL,
                type: 'GET',
                dataType: 'html',
                success: function(htmlContent) {
    modalBody.html(htmlContent);

    // Menampilkan nama user (atau nomor akun) di dalam modal pop-up relogin
    const modalName = document.querySelector('#sessionExpiredModal #session-user');
    const headerName = document.querySelector('.topbar .ms-auto span');
    if (modalName && headerName) { //Kalau kedua elemen ditemukan, maka isi teks di modal (#session-user) akan diisi sama seperti teks di header.
        modalName.textContent = headerName.textContent.trim();
    } else if (modalName) {
        modalName.textContent = 'Tidak Dikenal';
    }
                    // Submit form login modal
                    //Begitu form sudah dimuat, JavaScript menambahkan fungsi ke tombol dan form login:
                      // Deklarasi fungsi terpisah untuk menangani submit form
                    function handleLoginFormSubmit(e) {
                        e.preventDefault();
                        var form = $(this);
                        var formData = form.serialize();

                        $('#text_info_modal', form).fadeOut('fast');
                        $('#text_loading', form).show('fast');

                        $.ajax({
                            url: CI_BASE_URL + 'home/login',
                            type: 'POST',
                            data: formData,
                            dataType: 'json',
                            success: function(respon) {
                                $('#text_loading', form).fadeOut('fast');
                                if (respon.success) {
                                    window.reRenderContent(); // akan redirect ke intended destination jika ada
                                } else {
                                    $('#text_info_modal', form)
                                    .text(respon.message || 'Login gagal. Periksa kembali password Anda.')
                                    .show('fast');
                                }
                            },
                            error: function() {
                                $('#text_info_modal', form).fadeOut('fast');
                                $('#text_info_modal', form)
                                .text('Username dan Password Tidak Sesuai!')
                                .show('fast');
                            }
                        });
                        return false;
                    }
                    // Tombol login
                    // Deklarasi fungsi untuk menangani klik tombol login
            function loginTombolHeader() {
                $('#loginform_modal').submit();
            }

            // Deklarasi fungsi untuk menangani tombol Enter
            function tekanEnterSumbitLogin(e) {
                if (e.which === 13) {
                    $('#loginform_modal').submit();
                }
            }

            function kembaliTombolHeader() {
                                  location.reload();
                                }
                       
            // Panggil fungsi tersebut di dalam event listener
            $('#loginform_modal').on('submit', handleLoginFormSubmit);                    
            $('#signin-btn-modal').on('click', loginTombolHeader);
            $('#loginform_modal').on('keyup', tekanEnterSumbitLogin);
            $('#kembali-btn-modal').on('click', kembaliTombolHeader);          

                },
                error: function() {
                    modalBody.html('<div class="text-center text-danger">Failed to load login form. Please refresh manually.</div>');//perbaiki ini
                }
            });
            sessionModal.show();
        }
    }

//Kalau permintaan AJAX apa pun (dari mana saja) mendapat respons 401 Unauthorized
//langsung panggil fungsi handle401Error() untuk menampilkan modal login
//Jadi tidak perlu menulis ulang di setiap halaman
    $.ajaxSetup({
        statusCode: {
            401: handle401Error
        }
    });

    $(document).ajaxError(function (_e, xhr) { 
        if (xhr && xhr.status === 401) {
            handle401Error(xhr);
        }
    });
});
</script>
<script>
// === saat modal relogin dimuat oleh AJAX ===
$(document).on('shown.bs.modal', '#sessionExpiredModal', function () {
  const modalName = document.querySelector('#sessionExpiredModal #session-user');
  const headerName = document.querySelector('.topbar .ms-auto span');

  if (modalName && headerName) {
    modalName.textContent = headerName.textContent.trim();
  } else if (modalName) {
    modalName.textContent = 'Tidak Dikenal';
  }
});


$(document).on('click', function () {
  // Hide all Bootstrap 5 modals
  document.querySelectorAll('.modal').forEach(function(modalElement) {
    const modalInstance = bootstrap.Modal.getInstance(modalElement);
    if (modalInstance) {
      modalInstance.hide();
    }
  });
});

</script>

</body>
</html>
