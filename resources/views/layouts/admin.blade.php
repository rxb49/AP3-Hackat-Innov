@php use App\Utils\SessionHelpers; @endphp
    <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hackat'innov @yield('title', '')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

    <link href="/css/main.css" rel="stylesheet"/>
    <link rel="shortcut icon" href="/img/logo.png">

    @yield('custom-css', '')

    <!-- La balise style présente ici permet d'éviter au plus tôt le « flash » de contenu lié à VueJS -->
    <style>
        [v-cloak] {
            display: none !important;
        }
    </style>
</head>

<body>

<div class="sticky-top header">
    <header class="d-flex justify-content-center py-3">
        <ul class="nav nav-pills w-100 d-flex">
            <li class="nav-item"><a href="{{ route("home") }}" class="nav-link white-link @if (Route::is('home')) {{'active-link'}} @endif" aria-current="page">Home</a></li>
            <li class="nav-item"><a href="{{ route("about") }}" class="nav-link white-link @if (Route::is('about')) {{'active-link'}} @endif">About</a></li>
            <li class="flex-grow-1"></li>
        </ul>
    </header>
</div>

<!-- Contenu de la page, sera remplacé par le contenu de la page appelée (section('content')) -->
@yield('content', 'Default content')

</body>
</html>
