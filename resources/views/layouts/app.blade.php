<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Smart Emergency AI Niger — Signalez une urgence à Niamey et partout au Niger en quelques secondes">
    <title>@yield('title', 'Smart Emergency AI')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/smart-emergency.css') }}" rel="stylesheet">

    {{-- Appliquer le thème avant le rendu pour éviter le flash --}}
    <script>
        (function () {
            var t = localStorage.getItem('sea-theme') || 'light';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>

    @stack('styles')
</head>
<body>

    @php $layoutType = $layout ?? 'guest'; @endphp

    @if($layoutType === 'app')
        {{-- Layout application citoyen : sidebar + navbar --}}
        <div class="app-wrapper">
            <div class="sidebar-overlay" id="sidebarOverlay"></div>
            @include('partials.sidebar')
            <div class="app-main">
                @include('partials.navbar', ['variant' => 'app'])
                <main class="app-content">
                    @yield('content')
                </main>
            </div>
        </div>

    @elseif($layoutType === 'auth')
        {{-- Layout authentification : centré --}}
        <div class="auth-wrapper">
            @include('partials.navbar', ['variant' => 'auth'])
            <main class="auth-content">
                @yield('content')
            </main>
        </div>

    @else
        {{-- Layout page d'accueil --}}
        @include('partials.navbar', ['variant' => 'guest'])
        <main>
            @yield('content')
        </main>
        @include('partials.footer')
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/smart-emergency.js') }}"></script>
    @stack('scripts')
</body>
</html>
