<div class="login-box">
  <div class="login-avatar">
    <i class="fa-regular fa-user"></i>
  </div>
  <h2 id="session-user" style="font-size:16px;"></h2>

  <form id="loginform_modal" method="post">
  <div class="password-box">
    <input type="password" id="password_modal" name="password" placeholder="Password" required>
    <button type="submit" id="signin-btn-modal">
      <i class="fa-solid fa-arrow-right"></i>
    </button>
  </div>

  <div id="text_loading" style="display:none; color:#ccc;"></div>
  <div id="text_info_modal" style="display:none; color:#ff6b6b; margin-top:8px;"></div>
</form>


<div class="mt-3 text-center">
  <button type="button" id="kembali-btn-modal" class="btn btn-outline-light btn-sm">
    <i class="fa-solid fa-arrow-left me-1"></i> Logout
  </button>
</div>


</div>


<script>
// Definisikan fungsi di luar agar bisa dipanggil ulang
function tekanTombolLOGIN(e) {
  e.preventDefault();
  var password = $('#password_modal').val().trim();
  if (!password) {
    return;
  }

  var username = window.SESSION_USERNAME || localStorage.getItem('SESSION_USERNAME') || '<?php echo $this->session->userdata("username"); ?>';
  if (!username) {
    alert('Sesi login tidak valid — silakan refresh halaman.');
    return;
  }

  console.log('relogin payload', { username: username, password: '[REDACTED]' });

  $.ajax({
    url: CI_BASE_URL + 'home/relogin',
    type: 'POST',
    dataType: 'json',
    data: { username: username, password: password },
    success: function(response) {
      if (response.success) {
        if (window.reRenderContent) {
          window.reRenderContent(); // tanpa reload
        } else {
          var modal = bootstrap.Modal.getInstance(document.getElementById('sessionExpiredModal'));
          if (modal) modal.hide();
        }
      } 
    },
    error: function(xhr, status, err) {
      console.error('relogin error', status, err, xhr.responseText);
      alert('Gagal terhubung ke server.');
    }
  });
}

// Klik tombol login
$('#signin-btn-modal').on('click', tekanTombolLOGIN);

// Tekan Enter pada input password
$('#password_modal').on('keypress', function(e) {
  if (e.key === 'Enter') {
    tekanTombolLOGIN(e);
  }
});
</script>


