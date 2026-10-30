<nav class="topbar">

    <div class="d-flex align-items-center gap-3">

        <button id="menu-toggle" class="d-lg-none" type="button" aria-label="Abrir menú" aria-controls="main-sidebar" aria-expanded="false">
            <i class="bi bi-list"></i>
        </button>

        <div class="topbar-title">

        <h2>@yield('page-title', match (true) {
            request()->routeIs('proyectos.historial') => 'Historial de clientes',
            request()->routeIs('proyectos.*') => 'Proyectos',
            request()->routeIs('planos.*', 'calculos.*') => 'Cálculo de materiales',
            request()->routeIs('reportes.*') => 'Reportes',
            request()->routeIs('usuarios.*') => 'Gestión de usuarios',
            request()->routeIs('materials.*') => 'Inventario de materiales',
            default => 'Panel de control',
        })</h2>

        <small>Constructora ADASHA</small>

        </div>

    </div>

    <div class="user">

        <!-- FOTO DEL USUARIO -->
        <img
            src="{{ Auth::user()->avatar ?: asset('images/avatar-default.svg') }}"
            alt="Foto de perfil de {{ Auth::user()->name }}"
            width="38" height="38"
            referrerpolicy="no-referrer"
            onerror="this.onerror = null; this.src = '{{ asset('images/avatar-default.svg') }}';"
        >

        <!-- NOMBRE Y ROL -->
        <div class="user-info">

            <span class="user-name">
                {{ Auth::user()->name }}
            </span>

            <small class="user-role">
                {{ Auth::user()->isAdministrador() ? 'Administrador' : 'Usuario' }}
            </small>

        </div>

    </div>

</nav>
