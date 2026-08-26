<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Work Home Safety Solution')</title>
    <link rel="icon" href="{{ url('public/frontend/images/Work_home_sefty_solution-header.png') }}">
    @include('partials.seo-meta')  
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Work+Sans:300,400,500,700&display=swap" rel="stylesheet"> 
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> 
    <!-- Bootstrap Grid -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap-grid.min.css"> 
    <!-- Vegas -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/vegas/2.5.4/vegas.min.css"> 
    <!-- YTPlayer -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery.mb.ytplayer@3.3.9/dist/css/jquery.mb.YTPlayer.min.css"> 
    <!-- Main CSS -->
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"> 
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">  
    <link rel="stylesheet" href="{{ url('public/frontend/css/index.css') }}">

    @stack('styles')
</head>

<body>
 
    @include('frontend.elements.header-menu')

    @yield('content')

    @include('frontend.elements.footer') 

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> 
    <!-- Countdown -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.countdown/2.2.0/jquery.countdown.min.js"></script> 
    <!-- Particles -->
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script> 
    <!-- Vegas -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vegas/2.5.4/vegas.min.js"></script> 
    <!-- YTPlayer -->
    <script src="https://cdn.jsdelivr.net/npm/jquery.mb.ytplayer@3.3.9/dist/jquery.mb.YTPlayer.min.js"></script> 
    <!-- Main JS -->
    <script src="{{ url('public/frontend/js/comming-soon.js') }}"></script> 
    @stack('scripts')

</body>

</html>