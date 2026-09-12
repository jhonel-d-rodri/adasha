<aside class="sidebar">
    <div class="logo">
        <i class="bi bi-building-fill"></i>
        <h3>CONSTRUCTORA<br>ADASHA</h3>
    </div>

```
<ul>

    {{-- DASHBOARD --}}
    <li>
        <a href="{{ route('dashboard') }}">
            <i class="bi bi-speedometer2"></i>
            Dashboard
        </a>
    </li>

    {{-- PROYECTOS --}}
    <li>
        <a href="{{ route('proyectos.index') }}">
            <i class="bi bi-folder-fill"></i>
            Proyectos
        </a>
    </li>

    {{-- CÁLCULO DE MATERIALES --}}
    <li>
        <a href="{{ route('planos.index') }}">
            <i class="bi bi-box-seam-fill"></i>
            Cálculo de Materiales
        </a>
    </li>

    {{-- REPORTES --}}
    <li>
        <a href="{{ route('reportes.index') }}">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            Reportes
        </a>
    </li>


    {{-- ================================================= --}}
    {{-- OPCIONES EXCLUSIVAS DEL ADMINISTRADOR --}}
    {{-- ================================================= --}}

    @if(auth()->user()->isAdministrador())

        {{-- GESTIÓN DE USUARIOS --}}
        <li>
            <a href="{{ route('usuarios.index') }}">
                <i class="bi bi-people-fill"></i>
                Gestión de Usuarios
            </a>
        </li>

        {{-- HISTORIAL DE CLIENTES --}}
        <li>
            <a href="{{ route('proyectos.index') }}">
                <i class="bi bi-person-lines-fill"></i>
                Historial de Clientes
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
```

</aside>
