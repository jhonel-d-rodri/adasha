<aside class="sidebar" id="main-sidebar" aria-label="Menú principal">
    <a class="logo sidebar-brand" href="{{ route('dashboard') }}" aria-label="Constructora ADASHA — Ir al panel principal">
        <span class="sidebar-brand-frame">
            <img class="sidebar-brand-image"
                 src="{{ asset('images/logo-empresa-adasha.png') }}"
                 alt="Constructora ADASHA"
                 width="2172" height="724">
        </span>
    </a>

<div class="sidebar-section-label">ESPACIO DE TRABAJO</div>
<ul>

    {{-- DASHBOARD --}}
    <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a href="{{ route('dashboard') }}">
            <i class="bi bi-speedometer2"></i>
            Dashboard
        </a>
    </li>

    {{-- PROYECTOS --}}
    <li class="{{ request()->routeIs('proyectos.index', 'proyectos.create', 'proyectos.edit', 'proyectos.show') ? 'active' : '' }}">
        <a href="{{ route('proyectos.index') }}">
            <i class="bi bi-folder-fill"></i>
            Proyectos
        </a>
    </li>

    {{-- CÁLCULO DE MATERIALES --}}
    <li class="{{ request()->routeIs('planos.*', 'calculos.*') ? 'active' : '' }}">
        <a href="{{ route('planos.index') }}">
            <i class="bi bi-box-seam-fill"></i>
            Cálculo de Materiales
        </a>
    </li>

    {{-- REPORTES --}}
    <li class="{{ request()->routeIs('reportes.*') ? 'active' : '' }}">
        <a href="{{ route('reportes.index') }}">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            Reportes
        </a>
    </li>


    {{-- HISTORIAL DE CLIENTES --}}
    <li class="{{ request()->routeIs('proyectos.historial') ? 'active' : '' }}">
        <a href="{{ route('proyectos.historial') }}">
            <i class="bi bi-person-lines-fill"></i>
            Historial de Clientes
        </a>
    </li>

    {{-- ================================================= --}}
    {{-- OPCIONES EXCLUSIVAS DEL ADMINISTRADOR --}}
    {{-- ================================================= --}}

    @if(auth()->user()->isAdministrador())

        {{-- GESTIÓN DE USUARIOS --}}
        <li class="{{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
            <a href="{{ route('usuarios.index') }}">
                <i class="bi bi-people-fill"></i>
                Gestión de Usuarios
            </a>
        </li>

    @endif


    {{-- CERRAR SESIÓN --}}
    <li class="logout">
        <form action="{{ route('logout') }}" method="POST">
            @csrf

            <button type="submit" class="logout-btn">
                <i class="bi bi-box-arrow-left"></i>
                Cerrar sesión
            </button>
        </form>
    </li>

</ul>

</aside>
