<?php require_once('../config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
 <?php require_once('inc/header.php') ?>
<body class="hold-transition ">
  <script>
    start_loader()
  </script>
  <style>
    html, body{
      height:calc(100%) !important;
      width:calc(100%) !important;
    }
    body{
      background-image: url("<?php echo validate_image($_settings->info('cover')) ?>");
      background-size:cover;
      background-repeat:no-repeat;
    }
    .login-title{
      text-shadow: 2px 2px black
    }
    #login{
      flex-direction:column !important
    }
    #logo-img{
        height:150px;
        width:150px;
        object-fit:scale-down;
        object-position:center center;
        border-radius:100%;
    }
    #login .col-7,#login .col-5{
      width: 100% !important;
      max-width:unset !important
    }
  </style>
  <div class="h-100 d-flex align-items-center w-100" id="login">
    <div class="col-7 h-100 d-flex align-items-center justify-content-center">
      <div class="w-100">
        <center><img src="<?= validate_image($_settings->info('logo')) ?>" alt="" id="logo-img"></center>
        <h1 class="text-center py-5 login-title"><b><?php echo $_settings->info('name') ?> - Admin</b></h1>
      </div>
      
    </div>
    <div class="col-5 h-100 bg-gradient">
      <div class="d-flex w-100 h-100 justify-content-center align-items-center">
        <div class="card col-sm-12 col-md-6 col-lg-3 card-outline card-primary rounded-0 shadow">
          <div class="card-header rounded-0">
            <h4 class="text-purle text-center"><b>Login</b></h4>
          </div>
          <div class="card-body rounded-0">
            <form id="login-frm" action="" method="post">
              <div class="input-group mb-3">
                <input type="text" class="form-control" autofocus name="username" placeholder="Username">
                <div class="input-group-append">
                  <div class="input-group-text">
                    <span class="fas fa-user"></span>
                  </div>
                </div>
              </div>
              <div class="input-group mb-3">
                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                <div class="input-group-append">
                  <div class="input-group-text" style="cursor: pointer;" id="togglePassword">
                    <span class="fas fa-eye"></span>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-8">
                  <a href="<?php echo base_url ?>">Go to Website</a>
                  <br>
                  <a href="<?php echo base_url.'login.php' ?>" class="text-primary"><b>Login as Client</b></a>
                </div>
                <!-- /.col -->
                <div class="col-4">
                  <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                </div>
                <!-- /.col -->
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

<!-- jQuery -->
<script src="<?php echo base_url ?>plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url ?>dist/js/adminlte.min.js"></script>
<script>
    var _base_url_ = '<?php echo base_url ?>';
</script>
<script src="<?php echo base_url ?>dist/js/script.js"></script>

<script>
  $(document).ready(function(){
    end_loader();
    
    // Toggle password visibility
    $('#togglePassword').click(function(){
      var passwordField = $('#password');
      var icon = $(this).find('span');
      if(passwordField.attr('type') === 'password'){
        passwordField.attr('type', 'text');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
      } else {
        passwordField.attr('type', 'password');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
      }
    });
    
    $('#login-frm').submit(function(e){
        e.preventDefault()
        var _this = $(this)
        $('.pop-msg').remove()
        var el = $('<div>')
            el.addClass("pop-msg alert")
            el.hide()
        start_loader()
        $.ajax({
            url:_base_url_+'classes/Login.php?f=login',
            method:'POST',
            data:$(this).serialize(),
            success:function(resp){
                if(resp){
                    if(typeof resp == 'string')
                        var resp = JSON.parse(resp)
                    if(resp.status == 'success'){
                        location.replace(_base_url_+'admin');
                    }else if(resp.status == 'incorrect'){
                        el.addClass('alert-danger')
                        el.text("Incorrect username or password.")
                        _this.prepend(el)
                    }else{
                        el.addClass('alert-danger')
                        el.text("An error occurred.")
                        _this.prepend(el)
                    }
                    el.show('slow')
                    $('html,body').animate({scrollTop:0},'fast')
                    end_loader()
                }
            }
        })
    })
  })
</script>
</body>
</html>