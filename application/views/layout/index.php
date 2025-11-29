<body>
<?php $this->load->view('layout/header'); ?>


	<nav class="navbar navbar-expand-lg topbar fixed-top px-3">
		<button class="btn btn-outline-light me-2 d-lg-none" id="btnSidebar">
			<i class="fa-solid fa-bars"></i>
		</button>

		<span class="navbar-brand mb-0 h1 brand"></span>
	</span>
	<div class="ms-auto d-none d-sm-block">
		<i class="fa-regular fa-circle-user me-2"></i>
		<span><?php 
			$fullname = 'Guest';
			if (isset($this->session) && method_exists($this->session, 'userdata')) {
				// Try fullname first, then nama, then username as fallback
				$fullname = $this->session->userdata('fullname');
				if (!$fullname) {
					$fullname = $this->session->userdata('nama');
				}
				if (!$fullname) {
					$fullname = $this->session->userdata('username');
				}
				if (!$fullname) {
					$fullname = 'Guest';
				}
			}
			echo $fullname;
		?></span>
	</div>
	</nav>

	<?php $this->load->view('layout/menu');?>

	
	<div id="mainContent" class="container-fluid mt-5 pt-3">
    <?php $this->load->view('home/index'); ?>
</div>

	
	</div>
	
</body>
</html>

<script>
document.getElementById('btnSidebar').addEventListener('click', function(){
    document.getElementById('sidebar').classList.toggle('show');
});

function updateBrandText() {
  const brand = document.querySelector('.topbar .brand');
  if (window.innerWidth <= 750) {
    brand.textContent = 'PPA Online';
  } else {
    brand.textContent = 'MONITORING PERMOHONAN PENGGUNAAN ALUR';
  }
}
updateBrandText();
window.addEventListener('resize', updateBrandText);


</script>
