

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- AdminLTE -->
    <!-- Link các tệp CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/lineicons.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/materialdesignicons.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/fullcalendar.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">

</head>
<body>

    <!-- Include Sidebar -->
    @include('partials.side-bar')

    <!-- Content Wrapper -->
    <main class="main-wrapper">
    @include('partials.header')
         <section class="section">
            <div class="container-fluid">
                @yield('content')
            </div>
        </section>
    </div>
    </main>

<!-- Scripts -->
 <!-- Link các tệp JavaScript -->
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/Chart.min.js') }}"></script>
<script src="{{ asset('assets/js/dynamic-pie-chart.js') }}"></script>
<script src="{{ asset('assets/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/fullcalendar.js') }}"></script>
<script src="{{ asset('assets/js/jvectormap.min.js') }}"></script>
<script src="{{ asset('assets/js/world-merc.js') }}"></script>
<script src="{{ asset('assets/js/polyfill.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>

</body>
</html>
