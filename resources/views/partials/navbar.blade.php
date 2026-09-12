<nav class="topbar">

    <div>

        <h2>Panel Principal</h2>

        <small>Constructora ADASHA</small>

    </div>

    <div class="user">

        <!-- NOTIFICACIONES -->
        <i class="bi bi-bell-fill"></i>

        <!-- FOTO DEL USUARIO -->
        @if(Auth::user()->google_id)
            <img
                src="{{ Auth::user()->avatar ?? 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' }}"
                alt="Foto de perfil"
            >
        @else
            <img
                src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
                alt="Foto de perfil"
            >
        @endif

        <!-- NOMBRE Y ROL -->
        <div class="user-info">

            <span class="user-name">
                {{ Auth::user()->name }}
            </span>

            <small class="user-role">
                {{ Auth::user()->role === 'admin' ? 'Administrador' : 'Usuario' }}
            </small>

        </div>

    </div>

</nav>