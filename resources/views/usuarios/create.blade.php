@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">

    <div class="d-flex align-items-center mb-4">

        <a href="{{ route('usuarios.index') }}"
           class="btn btn-secondary me-3">

            <i class="bi bi-arrow-left"></i>

        </a>

        <div>
            <h2 class="fw-bold mb-0">
                <i class="bi bi-person-plus-fill"></i>
                Nuevo Usuario
            </h2>

            <p class="text-muted mb-0">
                Registrar un nuevo usuario o administrador
            </p>
        </div>

    </div>


    @if($errors->any())

        <div class="alert alert-danger">

            <strong>Corrige los siguientes errores:</strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif


    <div class="card shadow-sm border-0">

        <div class="card-body p-4">

            <form action="{{ route('usuarios.store') }}"
                  method="POST">

                @csrf

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">
                            Nombre completo
                        </label>

                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name') }}"
                               placeholder="Ingrese el nombre"
                               required>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">
                            Correo electrónico
                        </label>

                        <input type="email"
                               name="email"
                               class="form-control"
                               value="{{ old('email') }}"
                               placeholder="ejemplo@gmail.com"
                               required>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">
                            Contraseña
                        </label>

                        <input type="password"
                               name="password"
                               class="form-control"
                               placeholder="Mínimo 6 caracteres"
                               required>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">
                            Confirmar contraseña
                        </label>

                        <input type="password"
                               name="password_confirmation"
                               class="form-control"
                               placeholder="Repita la contraseña"
                               required>

                    </div>


                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-bold">
                            Tipo de usuario
                        </label>

                        <select name="role"
                                class="form-select"
                                required>

                            <option value="">
                                Seleccione un rol
                            </option>

                            <option value="usuario"
                                {{ old('role') == 'usuario' ? 'selected' : '' }}>

                                Usuario

                            </option>

                            <option value="admin"
                                {{ old('role') == 'admin' ? 'selected' : '' }}>

                                Administrador

                            </option>

                        </select>

                    </div>

                </div>


                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('usuarios.index') }}"
                       class="btn btn-secondary">

                        Cancelar

                    </a>

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="bi bi-save-fill"></i>
                        Guardar Usuario

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection