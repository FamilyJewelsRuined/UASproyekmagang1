<main class="content">
  <div class="d-flex align-items-center gap-2 page-title">
    <i class="fa-solid fa-dashboard fa-xl"></i>
    <span>Dashboard</span>
  </div>
  <hr class="hr-title">
  
  <div id="contentWrapper" style="position: relative;">
    <div class="dashboard">

      <!-- Kurs Transaksi 
      <div class="dashItem">
        <h2 class="dashHead" align="center"><i class="fa-regular fa-bell"></i> Kurs Transaksi</h2>
        <div class="dashWidget dashautoHeight">
          <div class="notif-dash">
            <div style="color:black;font-size:14px;" align="center">Tanggal: <?=date('d-m-Y')?></div>
            <div style="color:black;font-size:22px; font-weight: bold;" align="center"><?= number_format(isset($kurs) ? $kurs : 0,2,",",".") ?></div>
          </div>
        </div>
      </div>-->



      <!-- Notifikasi Password -->
  <?php 
      /*$passama = '';
      if (isset($this->session) && method_exists($this->session, 'userdata')) {
          $passama = $this->session->userdata('passama');
      }
      if ($passama == 'sama') { */?>  
      <div class="dashItem">
        <h2 class="dashHead" align="center"><i class="fa-regular fa-bell"></i> Notifikasi Password</h2>
        <a style="text-decoration:none" href="#" onclick="document.getElementById('menu-chpw').click(); return false;">
          <div class="dashWidget dashautoHeight">
            <div class="notif-dash">
              <div style="color:black;font-size:14px;" align="center">Password anda masih default!</div>
              <div style="color:#999898ff;font-size:12px; font-style:italic" align="center">[Klik untuk Ganti Password!]</div>
            </div>
          </div>
        </a>
      </div>
      <?/*php } */?>

    </div>


    <?php 

$userGroup = '';
if (isset($this->session) && method_exists($this->session, 'userdata')) {
    $userGroup = $this->session->userdata('group');
}
if ($userGroup == '102' && isset($checkinv) && !empty($checkinv)) {

?>
<div class="dashboard">
  <div class="dashItem" style="width: 100%; max-width: 1030px;">
    <h2 class="dashHead" align="center"><i class="fa-regular fa-bell" style="color:#dc3545;"></i>Invoice Reminder</h2>
    <div class="dashWidget dashautoHeight" style="background: #e6f9ff; border: 1px solid #343b4a; border-top: none; padding: 15px; margin-bottom: 20px; height: auto; overflow: visible;">
      <div class="notif-dash">
        <div style="color: #ff7315; font-size:16px; font-weight: bold; margin-bottom: 15px;" align="center">
          <i class="fa-solid fa-exclamation-triangle"></i> Ada <?php echo count($checkinv) ?> invoice belum lunas
        </div>
        <div align="center">
          <?php foreach($checkinv as $invoice): 
            echo 'Nomor Invoice: 
                  <span style="color:#ff7315; font-size:14px; font-weight: bold;">'.$invoice['NO_INVOICE'].
                  '</span>  Tanggal: '.$invoice['TANGGAL_INVOICE'].' Total:  
                  <span style="color:#ff7315; font-size:16px; font-weight: bold;">'.number_format($invoice['TOTAL_USD'], 0, ',', '.').'
                  </span> Sisa:  <span style="color:#ff7315; font-size:16px; font-weight: bold;">'.number_format($invoice['SISA'], 0, ',', '.').'</span><br>';
          endforeach; ?>
        </div>

        <div style="margin-top: 15px; text-align: center;">
          <a href="#"
            onclick="document.getElementById('menu-invoice').click(); return false;"
            class="btn btn-outline-danger btn-sm" style="text-decoration: none;">
            <i class="fa-solid fa-list"></i> Lihat Detail Invoice
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php 
}  
?>


    <div class="dashboard">
      <div class="dashItem" style="width: 100%; width:100%; max-width: 1030px;">
        <h2 class="dashHead" align="center"><i class="fa-solid fa-chart-line"></i>Grafik Permohonan PPA per Tahun</h2>
        <div class="dashWidget" style="padding: 20px; height: 320px;">
          <canvas id="ppaYearlyChart" style="width:100%; height:100%;"></canvas>
        </div>
      </div>
    </div>

  </div>
</main>


  <script>
    // Data grafik PPA per tahun
    var ppaYearlyData = <?php echo json_encode($ppa_yearly); ?>;

    // Normalisasi: buat rentang tahun berurutan dan isi 0 jika tidak ada data
    var yearToCount = {};
    var minYear = Number.POSITIVE_INFINITY;
    var maxYear = Number.NEGATIVE_INFINITY;
    ppaYearlyData.forEach(function(item){
      var y = parseInt(item.TAHUN || item.tahun, 10);
      var c = parseInt(item.JUMLAH_PPA || item.jumlah_ppa || item.JUMLAH || item.jumlah, 10) || 0;
      if (!isNaN(y)) {
        yearToCount[y] = (yearToCount[y] || 0) + c;
        if (y < minYear) minYear = y;
        if (y > maxYear) maxYear = y;
      }
    });
   if (!isFinite(minYear) || !isFinite(maxYear)) {
      var now = new Date().getFullYear();
      minYear = now-4; maxYear = now; //kalau tidak ada data, tampilkan grafik 5 tahun terakhir
    }
    var years = [];
    var counts = [];
    for (var y=minYear; y<=maxYear; y++) {
      years.push(y);
      counts.push(yearToCount[y] || 0);
    }

    // menghindari error canvas already in use dengan menghancurkan chart lama jika sudah ada 
    if (window.ppaYearlyChartInstance) {
      try { window.ppaYearlyChartInstance.destroy(); } catch(e) {}
    }

    // garis grafiknya
    var ctx = document.getElementById('ppaYearlyChart').getContext('2d');
    window.ppaYearlyChartInstance = new Chart(ctx, {
      type: 'line',
      data: {
        labels: years,
        datasets: [{
          label: 'Jumlah Transaksi PPA',
          data: counts,
          borderColor: 'rgba(88, 112, 181, 1)',
          backgroundColor: 'rgba(88, 112, 181, 0.15)',
          pointBackgroundColor: 'rgba(88, 112, 181, 1)',
          pointBorderColor: '#ffffff',
          pointRadius: 4,
          pointHoverRadius: 5,
          borderWidth: 2,
          tension: 0.3,
          fill: false
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        interaction: { mode: 'index', intersect: false },
        scales: {
          y: {
            beginAtZero: true,
            title: { display: true, text: 'Jumlah PPA' },
            ticks: { precision: 0 }
          },
          x: {
            title: { display: true, text: 'Tahun' }
          }
        },
        animation: { duration: 800, easing: 'easeInOutQuart' }
      }
    });
  </script>