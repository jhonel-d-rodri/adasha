<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Constructora ADASHA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ filemtime(public_path('css/dashboard.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/pages.css') }}">

    @stack('styles')

    <link rel="stylesheet" href="{{ asset('css/interface.css') }}?v={{ filemtime(public_path('css/interface.css')) }}">

</head>

<body class="app-shell">

<div class="wrapper">

    @include('partials.sidebar')
    <button class="sidebar-backdrop" type="button" aria-label="Cerrar menú" tabindex="-1" hidden></button>

    <main class="content">

        @include('partials.navbar')

        @yield('content')

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const sidebar = document.querySelector('.sidebar');
const menuToggle = document.getElementById('menu-toggle');
const backdrop = document.querySelector('.sidebar-backdrop');
function setMenuOpen(open) {
    sidebar?.classList.toggle('open', open);
    menuToggle?.setAttribute('aria-expanded', String(open));
    if (backdrop) backdrop.hidden = !open;
    document.body.classList.toggle('menu-open', open);
}
menuToggle?.addEventListener('click', () => setMenuOpen(!sidebar?.classList.contains('open')));
backdrop?.addEventListener('click', () => { setMenuOpen(false); menuToggle?.focus(); });
document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && sidebar?.classList.contains('open')) {
        setMenuOpen(false);
        menuToggle?.focus();
    }
});
window.matchMedia('(min-width: 992px)').addEventListener('change', event => {
    if (event.matches) setMenuOpen(false);
});
</script>

@stack('scripts')

</body>

</html>
