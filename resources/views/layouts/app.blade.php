<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title', 'HLA Wifi Shop')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @yield('extra_css')

    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-icon.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicon.ico') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/templatemo.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    <link
        rel="stylesheet"href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;700;900&display=swap">
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
</head>

<body>

    @include('partials.header')

    @yield('content')

    @include('partials.footer')

    {{-- MODAL LOGIN --}}
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                @include('auth.login')
            </div>
        </div>
    </div>

    {{-- MODAL REGISTER --}}
    <div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                @include('auth.register')
            </div>
        </div>
    </div>



    <script src="{{ asset('assets/js/jquery-1.11.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-migrate-1.2.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/templatemo.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    @yield('extra_js')

</body>

</html>
