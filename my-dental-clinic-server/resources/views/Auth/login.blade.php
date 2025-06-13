

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Toastr -->

    <!-- AdminLTE -->
    <!-- Link các tệp CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/lineicons.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/materialdesignicons.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/fullcalendar.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" 
      integrity="sha512-vKMx8UnXk60zUwyUnUPM3HbQo8QfmNx7+ltw8Pm5zLusl1XIfwcxo8DbWCqMGKaWeNxWA8yrx5v3SaVpMvR3CA==" 
      crossorigin="anonymous" referrerpolicy="no-referrer" />


</head>
<body>
<section class="signin-section">
        <div class="container-fluid">
          
          <div class="row g-0 auth-row">
            <div class="col-lg-6">
              <div class="auth-cover-wrapper bg-primary-100">
                <div class="auth-cover">
                  <div class="title text-center">
                    <h1 class="text-primary mb-10">Welcome Back</h1>
                    <p class="text-medium">
                      Sign in to your Existing account to continue
                    </p>
                  </div>
                  <div class="cover-image">
                    <img src="assets/images/auth/signin-image.svg" alt="" />
                  </div>
                  <div class="shape-image">
                    <img src="assets/images/auth/shape.svg" alt="" />
                  </div>
                </div>
              </div>
            </div>
            <!-- end col -->
            <div class="col-lg-6">
              <div class="signin-wrapper">
                <div class="form-wrapper">
                  <h6 class="mb-15">Sign In Form</h6>
                  <p class="text-sm mb-25">
                    Start creating the best possible user experience for you
                    customers.
                  </p>
                  <form action="{{route('Auth.login')}}" method="POST">
                    @csrf
                    <div class="row">
                      <div class="col-12">
                        <div class="input-style-1">
                          <label>Email</label>
                          <input name="email" type="email" placeholder="Email" />
                        </div>
                      </div>
                      <!-- end col -->
                      <div class="col-12">
                        <div class="input-style-1">
                          <label>Password</label>
                          <input name="password" type="password" placeholder="Password" />
                        </div>
                      </div>
                      <!-- end col -->
                      <div class="col-xxl-6 col-lg-12 col-md-6">
                        <div class="form-check checkbox-style mb-30">
                          <input class="form-check-input" type="checkbox" value="" id="checkbox-remember" />
                          <label class="form-check-label" for="checkbox-remember">
                            Remember me next time</label>
                        </div>
                      </div>
                      <!-- end col -->
                      <div class="col-xxl-6 col-lg-12 col-md-6">
                        <div class="text-start text-md-end text-lg-start text-xxl-end mb-30">
                          <a href="reset-password.html" class="hover-underline">
                            Forgot Password?
                          </a>
                        </div>
                      </div>
                      <!-- end col -->
                      <div class="col-12">
                        <div class="button-group d-flex justify-content-center flex-wrap">
                          <button class="main-btn primary-btn btn-hover w-100 text-center">
                            Sign In
                          </button>
                        </div>
                      </div>
                    </div>
                    <!-- end row -->
                  </form>
                </div>
              </div>
            </div>
            <!-- end col -->
          </div>
          <!-- end row -->
        </div>
      </section>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" 
        integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" 
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toastr.error("{{ session('error') }}");
        });
    </script>
	@endif
	@if(session('success'))
	<script>
		 document.addEventListener('DOMContentLoaded', function() {
		toastr.success("{{ session('success') }}");});
	</script>
	@endif

   

      </body>