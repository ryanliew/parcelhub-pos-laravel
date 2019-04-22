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
    <link href="{{ asset('css/app.css?v=1.5') }}" rel="stylesheet">

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="/img/logo.jpg"/>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    @component('components.left_navigation')
                    @endcomponent
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            
                        @else
                            @if(auth()->user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('vendors.page') }}">
                                        Admin panel
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    @if(auth()->user()->isAdmin())
                                        <a class="dropdown-item" href="{{ route('vendors.page') }}">
                                            Admin panel
                                        </a>
                                    @endif
                                    <a class="dropdown-item" href="{{ route('impersonate.page') }}">
                                        Login as other user
                                    </a>

                                    <a class="dropdown-item" href="{{ route('logout') }}?user={{ auth()->id() }}&allowed_users={{ Cookie::get('allowed_users') }}">
                                        {{ __('Logout') }}
                                    </a>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        @if(auth()->check())
        
            <branch-selector :branches="{{ json_encode(Auth::user()->branches()->with('terminals')->orderBy('branches.name')->get()) }}" :default="{{ auth()->user()->current_branch }}" :terminal="{{ auth()->user()->current_terminal }}" :userId="{{ auth()->user()->id }}" :users="{{ json_encode(Cookie::get('allowed_users')) }}"></branch-selector>
            
        @endif
        <main class="py-4 main-content inset-shadow">
            @yield('content')

            <flash></flash>
        </main>
    </div>

    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('js/app.js?v=2.37') }}"></script>

    @yield('js')
</body>
</html>
