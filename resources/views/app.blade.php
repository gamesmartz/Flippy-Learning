<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">    
        

        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <meta name="description" content="Study for class while playing the best PC games. Game Smarts" />
        <link rel="canonical" href="{{ config('app.url', 'https://gamesmartz.com') }}" />

        <!-- Bootstrap CSS -->
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Varela+Round&display=swap" rel="stylesheet">

        <link href="{{ asset('assets/css/game-smartz.css') }}" rel="stylesheet">

        <script src="{{ asset('assets/js/modernizr-webp.js') }}"></script>


        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        <!-- Scripts -->
        @routes
        <script src="{{ mix('js/app.js') }}" defer></script>
        @inertiaHead
    </head>
    <body>
        @inertia
    </body>
</html>
