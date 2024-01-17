<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>
        <meta name="description" content="@yield('description')" />
        @if(isset($canonical))
            <link rel="canonical" href="{{ $canonical }}" />
        @endif
     

        <!-- Bootstrap CSS -->
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @stack('head')

        <link rel="stylesheet" href="{{ asset('assets/plugin/pagination/pagination.css') }}" />
        <link href="{{ asset('assets/css/game-smartz.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/css/redesign-game-smartz.css') }}" rel="stylesheet">
        <script src="{{ asset('assets/js/modernizr-webp.js') }}"></script>
        <script src="{{ asset('assets/js/libs/lazysizes.min.js') }}"></script>

        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-36148085-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', 'UA-36148085-1');
        </script>        
    </head>
    <body>
        @section('header')
            @include('layouts.header')
        @show

        @yield('content')

        @section('footer')
            @include('layouts.footer')
        @show

        @stack('foot')
    </body>
</html>
