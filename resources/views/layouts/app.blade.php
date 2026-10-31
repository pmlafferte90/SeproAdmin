<!DOCTYPE html>
<html lang="es">

<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>
        @if (trim($__env->yieldContent('title')))
        @yield('title') | {{ config('app.name', 'Laravel') }}
        @else
        {{ config('app.name', 'SEPRO M.E.') }}
        @endif
    </title>
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    @stack('before-styles')
    @vite('resources/sass/app.scss')
    @stack('after-styles')
</head>

<body>

    <div id="global-loading-overlay" style="
        display: flex;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        z-index: 99998;
        justify-content: center;
        align-items: center;
    ">
        <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>

    <div class="sidebar sidebar-dark sidebar-fixed" id="sidebar">
        <div class="sidebar-brand d-none d-md-flex">
            <!-- <svg class="sidebar-brand-full" width="118" height="46" alt="CoreUI Logo">
                <use xlink:href="{{ asset('icons/brand.svg#full') }}"></use>
            </svg>
            <svg class="sidebar-brand-narrow" width="46" height="46" alt="CoreUI Logo">
                <use xlink:href="{{ asset('icons/brand.svg#signet') }}"></use>
            </svg> -->
            <h5>Menú Principal</h5>
        </div>
        @include('layouts.navigation')
        <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
    </div>
    <div class="wrapper d-flex flex-column min-vh-100 bg-light">
        <!-- Header block -->
        @include('layouts.includes.header')
        <!-- / Header block -->

        <div class="body flex-grow-1 px-3">
            <div class="container-lg">
                <!-- Errors block -->
                @include('layouts.includes.errors')
                <!-- / Errors block -->
                <div class="mb-4" id='main-content'>@yield('content')</div>
            </div>
        </div>

        <!-- Footer block -->
        @include('layouts.includes.footer')
        <!-- / Footer block -->
    </div>

    <!-- Scripts -->
    @stack('before-scripts')

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/coreui.bundle.min.js') }}"></script>
    @vite('resources/js/app.js')

    @stack('after-scripts')
    <!-- / Scripts -->
     <div id="toast-container" style="
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 99999;
        pointer-events: none;
    "></div>

    @include('componentes.confirm')

</body>

</html>