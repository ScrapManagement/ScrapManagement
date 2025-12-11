<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Purple Admin</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{asset('dash')}}/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="{{asset('dash')}}/vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{asset('dash')}}/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{asset('dash')}}/images/favicon.ico" />
  </head>
  <body>
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth">
          <div class="row flex-grow">
            <div class="col-lg-4 mx-auto">
              <div class="auth-form-light text-left p-5">
                <div class="brand-logo">
                  <img src="{{asset('dash')}}/images/logo.svg">
                </div>
                <h4>Hello! let's get started</h4>
                <h6 class="font-weight-light">Sign in to continue.</h6>
                <form class="pt-3"  method="POST" action="{{ route("login.check") }}">
                    @csrf
                  <div class="form-group">
                      @error('email') <p style="color: red" >{{ $message }}</p> @enderror
                     <label for="exampleInputEmail1">Email</label>
                    <input type="email" name="email" class="form-control form-control-lg" id="exampleInputEmail1" placeholder="Email">
                  </div>
                  <div class="form-group">
                      @error('password') <p style="color: red" >{{ $message }}</p> @enderror
                     <label for="exampleInputPassword1">password</label>
                    <input type="password" name="password" class="form-control form-control-lg" id="exampleInputPassword1" placeholder="Password">
                  </div>
                <div class="mt-3 text-center">

      <button type="submit" class="btn btn-gradient-primary btn-lg font-weight-medium auth-form-btn">Submit</button>
</div>



                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="{{asset('dash')}}/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{asset('dash')}}/js/off-canvas.js"></script>
    <script src="{{asset('dash')}}/js/hoverable-collapse.js"></script>
    <script src="{{asset('dash')}}/js/misc.js"></script>
    <!-- endinject -->
  </body>
</html>
