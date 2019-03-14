<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('page')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet"> 

    <link rel="icon" href="/img/favicon.png">

    <!-- Styles -->
    @yield('styles')
    <link href="{{ asset('css/app.css?v=1.4') }}" rel="stylesheet">

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="/img/logo.jpg"/>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#topNavbar" aria-controls="topNavbar" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="topNavbar">
                    <!-- Left Side Of Navbar -->
                    @component('components.left_navigation')
                    @endcomponent
                    

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @if(auth()->check() && auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('invoices.page') }}">
                                    User panel
                                </a>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('home') }}">
                                    User panel
                                </a>
                                 <a class="dropdown-item" href="{{ route('impersonate.page') }}">
                                    Login as other user
                                </a>
                                <a class="dropdown-item" href="{{ route('logout') }}?user={{ auth()->id() }}&allowed_users={{ Cookie::get('allowed_users') }}">
                                    {{ __('Logout') }}
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="branch-selector inset-shadow">
            <div class="container">
                <nav class="navbar navbar-expand-md navbar-light navbar-admin">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                <div class="collapse navbar-collapse" id="adminNavbar">
                    <ul class="navbar-nav mr-auto">
                        @if(auth()->user()->is_admin)
                            <li class="nav-item"><a class="nav-link" href="{{ route('branches.page') }}">Branches</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('terminals.page') }}">Terminals</a></li>
                        @endif
                        <li class="nav-item"><a class="nav-link" href="{{ route('vendors.page') }}">Vendors</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('zones.page') }}">Zones</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('taxes.page') }}">Taxes</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('users.page') }}">Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('permissions.page') }}">Permissions</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('product-types.page') }}">SKU types</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('products.page') }}">SKU</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('reports.page') }}">Reports</a></li>
                        <li class="nav-item dropdown">
                            <a id="settings-dropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Settings <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-left" aria-labelledby="invoice-dropdown">
                                @if(auth()->user()->is_admin)
                                <a class="dropdown-item" href="{{ route('settings') }}">
                                    Global settings
                                </a>
                                <a class="dropdown-item" href="{{ route('branch-knowledge.page') }}">
                                    Branch settings
                                </a>
                                @endif
                            </div>
                        </li>
                    </ul>
                </div>
                <span class="badge badge-danger">You are in Admin panel</span>
            </div>
        </div>

        <main class="py-4 main-content inset-shadow">
            @yield('content')

            <flash></flash>
        </main>
    </div>

    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('js/app.js?v=2.33') }}"></script>
    @yield('js')
</body>
</html>
