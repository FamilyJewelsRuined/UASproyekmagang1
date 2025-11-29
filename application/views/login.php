<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PPA Ambapers</title>
    <link rel="icon" href="<?php echo base_url('templates/img/logo.gif');?>">

    <script type="text/javascript" src="<?php echo base_url('templates/js/jquery-1.8.0.min.js')?>"></script>
    <link href="<?php echo base_url('templates/css/login.css');?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Barlow&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Div Ambapers</title>
  </head>
  <body>
    <div class="login-box">
 
        <h2>IT Div Ambapers</h2>
        <h3 style="font-size:18px" >Permohonan Penggunaan Alur</h3>
      
            <form accept-charset="UTF-8" id="loginform" method="post">  
            <label style="display:none;color:#0060AC;cursor:pointer;" id="text_loading" 
                onclick="$(this).fadeOut('fast');">Please wait...</label><br/>
            <label style="display:none;color:rgb(255, 64, 64);cursor:pointer;" id="text_info" 
                onclick="$(this).fadeOut('fast');">Username dan Password Tidak Sesuai!</label><br/><br/> 
            <div class="user-box">
                <input autofocus="autofocus" size="30" type="text" name="username" id="nama" required="true">
                <label>Username<span style="font-size: 10px; color: #0aa7ea;"> Aplikasi PPA Online</span></label>
            </div>
            <div class="user-box">
                <input size="30" type="password" class="input" name="password" id="psw" required="true">
                <label>Password<span style="font-size: 10px; color: #0aa7ea;"> Aplikasi PPA Online</span></label>
            </div>
            <div style="text-align: center" >
                <a>
                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <input type="button" class="tombol" onclick="login()" id="signin-btn" value="L O G I N" >
                </a>
          </div>
        </form>
      </div>
  </body>
  </html>
<script>
    $(document).keyup(function(t){
        var nama = $('#nama').val();
        var psw = $('#psw').val();
        if(t.which == 13){
            if(nama == ''){
                $('#nama').focus();
            }else if(psw == ''){
                $('#psw').focus();
            }else{
                login();
            }
        }
    });
    function login(){
        $.ajax({
            type:'POST',
            url:'<?php echo base_url("home/login");?>',
            dataType:'json',
            cache:false,
            data:$('#loginform').serialize(),
            beforeSend:function(){
                $('#text_info').fadeOut('fast');
                $('#text_loading').show('fast');  
            },
            success:function(respon){
                $('#text_loading').fadeOut('fast');
                // jQuery automatically parses JSON boolean
                if(respon === true){
                    // Login successful, redirect to home
                    window.location.href = "<?php echo base_url('home');?>";
                }else{
                    // Login failed
                    $('#text_info').show('fast');
                    $('#nama').focus();
                }
            },
            error:function(){
                $('#text_loading').fadeOut('fast');
                $('#text_info').text('Terjadi kesalahan saat login. Silakan coba lagi.');
                $('#text_info').show('fast');
            }
        });
        return false;  
    }
</script>
