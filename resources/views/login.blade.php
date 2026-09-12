```php
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
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

<div class="container-fluid">

    <div class="row vh-100">

        <!-- LADO IZQUIERDO -->
        <div class="col-lg-7 left d-flex align-items-center justify-content-center">

            <div class="text-center px-5">

                <h1>CONSTRUCTORA</h1>

                <h2>ADASHA</h2>

                <p>
                    Sistema Web para el Cálculo Automatizado de Materiales
                    de Construcción mediante análisis de planos arquitectónicos.
                </p>

            </div>

        </div>


        <!-- LADO DERECHO -->
        <div class="col-lg-5 right d-flex align-items-center justify-content-center">

            <div class="card login-card shadow">

                <!-- ENCABEZADO -->
                <div class="text-center mb-4">

                    <img
                        src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
                        width="90"
                        height="90"
                        alt="Usuario">

                    <h3 class="mt-3">
                        Bienvenido
                    </h3>

                    <p class="text-muted">
                        Inicie sesión para continuar
                    </p>

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
                    <div class="mb-3">

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
                    <div class="mb-4">

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

                        </div>

                    </div>


                    <!-- BOTÓN INGRESAR -->
                    <div class="d-grid">

                        <button
                            type="submit"
                            class="btn btn-primary btn-lg">

                            <i class="bi bi-box-arrow-in-right me-2"></i>

                            Ingresar

                        </button>

                    </div>

                </form>


                <!-- SEPARADOR -->
                <div class="d-flex align-items-center my-4">

                    <hr class="flex-grow-1">

                    <span class="mx-3 text-muted">
                        O
                    </span>

                    <hr class="flex-grow-1">

                </div>


                <!-- BOTÓN GOOGLE -->
                <div class="d-grid">

                    <a
                        href="{{ route('google.login') }}"
                        class="btn btn-outline-dark btn-lg">

                        <i class="bi bi-google me-2"></i>

                        Continuar con Google

                    </a>

                </div>


            </div>

        </div>

    </div>

</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
```
