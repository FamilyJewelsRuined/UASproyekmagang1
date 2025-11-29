<aside class="sidebar" id="sidebar">
    <a href="javascript:void(0)" onclick="addTab('<?php echo site_url('dashboard')?>')"><i class="fa-solid fa-gauge"></i>  Dashboard</a>
   
    <a href="javascript:void(0)" onclick="addTab('<?php echo base_url('ppa')?>')"><i class="fa-solid fa-check-circle"></i> Tabeldata PPA</a>
    
<!-- tampilkan form pembuatan ppa hanya ke user id 101 -->
    <?php 
        $userGroup = '';
        if (isset($this->session) && method_exists($this->session, 'userdata')) {
            $userGroup = $this->session->userdata('group');
        }
        if($userGroup == '101'): 
    ?>
     <a href="javascript:void(0)" onclick="addTab('<?php echo base_url('permohonan')?>')"><i class="fa-solid fa-check-circle"></i> Form PPA</a>
    <?php endif; ?>


    <!-- tampilkan proses ppa hanya ke user id 100 -->
    <?php 
        $userGroup = '';
        if (isset($this->session) && method_exists($this->session, 'userdata')) {
            $userGroup = $this->session->userdata('group');
        }
        if($userGroup == '100'): 
    ?>
    <a href="javascript:void(0)" onclick="addTab('<?php echo base_url('view_ppa_approve')?>')"><i class="fa-solid fa-check-circle"></i> Proses PPA</a>
    <?php endif; ?>

    <?php
        if (isset($data) && is_array($data) && isset($this->auth) && method_exists($this->auth, 'get_child_menu')) {
            foreach($data as $r){        
                // Mengambil submenu anak
                $child = $this->auth->get_child_menu($r['ID']);  
                 $xUrl = $r['URL'] == '#' ? "" : "onclick=\"addTab('".base_url($r['URL'])."','$r[LABEL]')\"";
                // Membuat elemen <a> dengan href yang sesuai dan menambahkan class jika ada sub-menu
                echo '<a class="main" href="'.($r['URL'] == '#' ? '#' : 'javascript:void(0)').'" id="menu-'.$r['URL'].'" ' . $xUrl . '>
                    <i class="'.$r['CLASS'].'"></i>'.$r['LABEL'].'
                </a>';

                // Jika ada submenu, kita tambahkan submenu tersebut
                if ($child['count'] > 0) {
                    echo "<div class='sub-menu' id='submenu-".$r['ID']."' style='display:none; padding-left: 0px;'>";

                    foreach($child['data'] as $rc){
                        // Mengambil child lebih dalam jika ada
                        $gchild = $this->auth->get_child_menu($rc['ID']); 
                        echo "<a href='javascript:void(0)' onclick='addTab(\"".base_url($rc['URL'])."\", \"".$rc['LABEL']."\")'>
                                &nbsp;<i class='".$rc['CLASS']."'></i> ".$rc['LABEL']."</a>";

                        // Menampilkan grandchild jika ada
                        if ($gchild['count'] > 0) {
                            echo "<div class='grandchild-menu' style='padding-left: 20px;'>";
                            foreach ($gchild['data'] as $rg) {
                                echo "<a href='javascript:void(0)' onclick='addTab(\"".base_url($rg['URL'])."\", \"".$rg['LABEL']."\")'>
                                        <i class='".$rg['CLASS']."'></i>".$rg['LABEL']."</a>";
                            }
                            echo "</div>";
                        }
                    }
                    echo "</div>";
                }
            }
        }
    ?>

    <hr class="text-secondary my-2">
    <a href="<?php echo base_url("home/logout")."?".date("Ymdhis")?>">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>
</aside>                  
        
<script>
     $(document).ready(function() {  
    // Handle click on main menu item (menu utama dengan atau tanpa submenu)
    $('.maind').on('click', function(e) {
        e.preventDefault(); 

        var submenu = $(this).next('.sub-menu');  
        var isActive = $(this).hasClass('active'); // Mengecek jika menu utama aktif

        // Menghapus class active dari semua menu utama dan menutup semua submenu
        // Hanya menu utama yang diklik yang akan mendapatkan class active
        if (!submenu.is(':visible')) {
            $('.main').removeClass('active');
            $('.sub-menu').slideUp(300);
        }

        // Jika menu utama memiliki submenu, toggle submenu
        if (submenu.length > 0) {
            if (submenu.is(':visible')) {
                // Jika submenu terbuka, cek apakah ada grandchild yang aktif
                var grandchildActive = submenu.find('.grandchild-menu a.active').length > 0;
                if (!grandchildActive) {
                    submenu.stop(true, true).slideUp(300); // Menutup submenu jika tidak ada grandchild aktif
                    $(this).removeClass('active'); // Menghapus class active jika submenu tertutup dan tidak ada grandchild aktif
                }
            } else {
                // Jika submenu tertutup, beri class active pada menu yang diklik dan slide down submenu
                $(this).addClass('active');
                submenu.stop(true, true).slideDown(300);
            }
        } else {  
            // Jika menu utama tanpa submenu, hanya beri class active
            $(this).addClass('active');
        }
    });

    // Handle click on submenu items (submenu dengan grandchild)
    $('.sub-menu a').on('click', function(e) {
        e.stopPropagation();

        // Remove active class from all sub-menu items and add to the clicked item
        $('.sub-menu a').removeClass('active');
        $(this).addClass('active');

        // Toggle grandchild menu visibility if exists
        var grandchildMenu = $(this).next('.grandchild-menu');
        if (grandchildMenu.length > 0) {
            grandchildMenu.stop(true, true).slideToggle(300);
        }
    });
});

 function addclass (){  
        // This function is called after content is loaded to reinitialize menu handlers
        // Remove existing event handlers to prevent duplicates
        $('.main').off('click.menu');
        $('.sub-menu a').off('click.submenu');
        
        // Handle click on main menu item (menu utama dengan atau tanpa submenu)
        $('.main').on('click.menu', function(e) {
            e.preventDefault(); 

            var submenu = $(this).next('.sub-menu');  
            var isActive = $(this).hasClass('active'); // Mengecek jika menu utama aktif

            // Menghapus class active dari semua menu utama dan menutup semua submenu
            // Hanya menu utama yang diklik yang akan mendapatkan class active
            if (!submenu.is(':visible')) {
                $('.main').removeClass('active');
                $('.sub-menu').slideUp(300);
            }

            // Jika menu utama memiliki submenu, toggle submenu
            if (submenu.length > 0) {
                if (submenu.is(':visible')) {
                    // Jika submenu terbuka, cek apakah ada grandchild yang aktif
                    var grandchildActive = submenu.find('.grandchild-menu a.active').length > 0;
                    if (!grandchildActive) {
                        submenu.stop(true, true).slideUp(300); // Menutup submenu jika tidak ada grandchild aktif
                        $(this).removeClass('active'); // Menghapus class active jika submenu tertutup dan tidak ada grandchild aktif
                    }
                } else {
                    // Jika submenu tertutup, beri class active pada menu yang diklik dan slide down submenu
                    $(this).addClass('active');
                    submenu.stop(true, true).slideDown(300);
                }
            } else {  
                // Jika menu utama tanpa submenu, hanya beri class active
                $(this).addClass('active');
            }
        });

        // Handle click on submenu items (submenu dengan grandchild)
        $('.sub-menu a').on('click.submenu', function(e) {
            e.stopPropagation();

            // Remove active class from all sub-menu items and add to the clicked item
            $('.sub-menu a').removeClass('active');
            $(this).addClass('active');

            // Toggle grandchild menu visibility if exists
            var grandchildMenu = $(this).next('.grandchild-menu');
            if (grandchildMenu.length > 0) {
                grandchildMenu.stop(true, true).slideToggle(300);
            }
        });
 }
   

function addTab(url, title) {

  //mensimpan destinasi tujuan sebelum melakukan request
  window.INTENDED_DESTINATION_URL = url;
  
  window.INTENDED_DESTINATION_TITLE = title || '';
  //

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
    addclass();
      
    $('#mainContent').find("script").each(function() {
      $.globalEval(this.text || this.textContent || this.innerHTML || '');    
    });
    
    const sidebar = document.getElementById('sidebar');
        if (sidebar.classList.contains('show')) {
            sidebar.classList.remove('show');
        }
  });

  return false; 
}     
     

</script>
