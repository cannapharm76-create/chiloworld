{{-- Shared shell for every website page (header, footer, cookie notice, global CSS/JS). Page markup lives in resources/views/front/*.blade.php --}}
@php
    $brandWorld = in_array(request()->path(), \App\Http\Controllers\HomeController::BRANDS, true);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CHILO EXOTICS®')</title>
    <meta name="description" content="@yield('description')">
    <meta name="robots" content="@yield('robots', 'index,follow')">
    <meta name="theme-color" content="#0d1a16">
    <meta name="apple-mobile-web-app-title" content="Chilo Exotics">
    <meta property="og:site_name" content="CHILO EXOTICS">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'CHILO EXOTICS®')">
    <meta property="og:description" content="@yield('description')">
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <link rel="icon" type="image/svg+xml" href="{{ asset('public/front/chilo/favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('public/front/chilo/favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Electrolize&family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">

    {{-- page stylesheets (compiled site CSS + page scoped styles) --}}
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('public/front/chilo/chilo.css') }}">
    <link rel="stylesheet" href="{{ asset('public/front/chilo/chilo-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('public/front/app.css') }}">
</head>
<body class="{{ $brandWorld ? 'theme-brand' : 'theme-core' }}">
<div id="__next">
    @unless (View::hasSection('no_header'))
        @include('front.layout.header')
    @endunless

    @yield('content')

    @include('front.chilo.footer')

    @include('front.layout.cookie')

    @if (session('status'))
        <div class="cv-flash" role="status">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="cv-flash cv-flash--error" role="alert">{{ $errors->first() }}</div>
    @endif
</div>
<script>window.SITE = {root: "{{ url('/') }}/", assets: "{{ asset('public/front') }}/"};</script>
<script src="{{ asset('public/front/app.js') }}"></script>
@stack('scripts')
</body>
</html>
