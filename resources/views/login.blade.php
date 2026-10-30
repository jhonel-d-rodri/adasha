<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Constructora ADASHA - Iniciar sesión</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
</head>

<body class="login-page">

<div class="container-fluid">

    <div class="login-illustration" aria-hidden="true">
        <img src="{{ asset('images/login-construccion-adasha.png') }}" alt="">
    </div>

    <div class="row vh-100">

        <!-- LADO IZQUIERDO -->
        <div class="col-lg-7 left d-flex align-items-center justify-content-center">

            <div class="content">

                <div class="login-eyebrow">
                    <i class="bi bi-shield-check"></i>
                    Sistema integral de construcción
                </div>

                <h1>Gestión de obra<br><span>con mayor precisión.</span></h1>

                <p>
                    Centraliza tus proyectos, analiza planos y calcula materiales
                    con información clara para tomar mejores decisiones.
                </p>

                <div class="features text-start">
                    <div class="feature-item">
                        <i class="bi bi-diagram-3-fill"></i>
                        <span><strong>Proyectos organizados</strong>Planos y avances siempre disponibles</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-calculator-fill"></i>
                        <span><strong>Cálculos precisos</strong>Materiales estimados con mayor control</span>
                    </div>
                </div>

            </div>

        </div>


        <!-- LADO DERECHO -->
        <div class="col-lg-5 right d-flex align-items-center justify-content-center">

            <div class="card login-card shadow" aria-labelledby="login-title">

                <!-- ENCABEZADO -->
                <div class="card-header">

                    <img class="login-company-logo"
                         src="{{ asset('images/logo-empresa-adasha.png') }}"
                         alt="Constructora ADASHA"
                         width="2172" height="724">
                    <span class="login-card-kicker">Portal administrativo</span>
                    <h3 id="login-title">Acceso al sistema</h3>
                    <p>Ingresa tus credenciales para continuar</p>

                </div>


                <!-- MENSAJE DE ERROR -->
                @if(session('error'))

                    <div class="alert alert-danger alert-dismissible fade show">

                        <i class="bi bi-exclamation-triangle-fill me-2"></i>

                        {{ session('error') }}

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                        </button>

                    </div>

                @endif


                <!-- MENSAJE DE ÉXITO -->
                @if(session('success'))

                    <div class="alert alert-success alert-dismissible fade show">

                        <i class="bi bi-check-circle-fill me-2"></i>

                        {{ session('success') }}

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                        </button>

                    </div>

                @endif


                <!-- ERRORES DE VALIDACIÓN -->
                @if($errors->any())

                    <div class="alert alert-danger">

                        <ul class="mb-0">

                            @foreach($errors->all() as $error)

                                <li>{{ $error }}</li>

                            @endforeach

                        </ul>

                    </div>

                @endif


                <!-- LOGIN TRADICIONAL -->
                <form action="{{ route('login.process') }}" method="POST">

                    @csrf

                    <!-- CORREO -->
                    <div class="form-group">

                        <label for="email" class="form-label">
                            Correo electrónico
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-envelope-fill"></i>
                            </span>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                placeholder="Ingrese su correo"
                                value="{{ old('email') }}"
                                required
                                autofocus>

                        </div>

                    </div>


                    <!-- CONTRASEÑA -->
                    <div class="form-group">

                        <label for="password" class="form-label">
                            Contraseña
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-lock-fill"></i>
                            </span>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                placeholder="Ingrese su contraseña"
                                required>

                            <button
                                type="button"
                                class="password-toggle"
                                aria-label="Mostrar contraseña"
                                aria-controls="password"
                                aria-pressed="false">
                                <i class="bi bi-eye"></i>
                            </button>

                        </div>

                    </div>


                    <!-- BOTÓN INGRESAR -->
                    <div class="form-options">
                        <span class="secure-access"><i class="bi bi-shield-lock"></i> Acceso seguro</span>
                        <a class="small fw-semibold text-decoration-none" href="{{ route('password.request') }}">
                            Recuperar contraseña
                        </a>
                    </div>
                    <div class="d-grid">

                        <button
                            type="submit"
                            class="btn btn-primary btn-lg">

                            <i class="bi bi-box-arrow-in-right me-2"></i>

                            Iniciar sesión

                        </button>

                    </div>

                </form>


                <!-- SEPARADOR -->
                <div class="divider" aria-hidden="true">

                    <span>O</span>

                </div>


                <!-- BOTÓN GOOGLE -->
                <div class="d-grid">

                    <a
                        href="{{ route('google.login') }}"
                        class="btn-google">

                        <i class="bi bi-google me-2"></i>

                        Continuar con Google

                    </a>

                </div>

                <p class="login-support"><i class="bi bi-headset"></i> Soporte de acceso: contacta al administrador</p>


            </div>

        </div>

    </div>

</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const passwordInput = document.getElementById('password');
    const passwordToggle = document.querySelector('.password-toggle');

    passwordToggle?.addEventListener('click', () => {
        const visible = passwordInput.type === 'text';
        passwordInput.type = visible ? 'password' : 'text';
        passwordToggle.setAttribute('aria-pressed', String(!visible));
        passwordToggle.setAttribute('aria-label', visible ? 'Mostrar contraseña' : 'Ocultar contraseña');
        passwordToggle.innerHTML = `<i class="bi bi-eye${visible ? '' : '-slash'}"></i>`;
    });
</script>

</body>

</html>
