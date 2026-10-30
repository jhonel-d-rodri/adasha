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
                <i class="bi bi-pencil-fill"></i>
                Editar Usuario
            </h2>

            <p class="text-muted mb-0">
                Modificar información y permisos del usuario
            </p>
        </div>

    </div>


    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>

    @endif


    <div class="card shadow-sm border-0">

        <div class="card-body p-4">

            <form action="{{ route('usuarios.update', $usuario) }}"
                  method="POST">

                @csrf
                @method('PUT')


                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">
                            Nombre completo
                        </label>

                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name', $usuario->name) }}"
                               required>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">
                            Correo electrónico
                        </label>

                        <input type="email"
                               name="email"
                               class="form-control"
                               value="{{ old('email', $usuario->email) }}"
                               required>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">
                            Rol
                        </label>

                        <select name="role"
                                class="form-select"
                                required>

                            <option value="usuario"
                                {{ old('role', $usuario->role) == 'usuario' ? 'selected' : '' }}>

                                Usuario

                            </option>

                            <option value="admin"
                                {{ old('role', $usuario->role) == 'admin' ? 'selected' : '' }}>

                                Administrador

                            </option>

                        </select>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-bold">
                            Asignar nueva contraseña
                        </label>

                        <input type="password"
                               name="password"
                               class="form-control"
                               placeholder="Mínimo 8 caracteres; vacío para conservar">

                        <small class="text-muted d-block mt-1">
                            Para usuarios de Google, asigne una contraseña aquí si también necesitan ingresar con correo y contraseña.
                        </small>

                    </div>


                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-bold">
                            Confirmar la nueva contraseña
                        </label>

                        <input type="password"
                               name="password_confirmation"
                               class="form-control">

                    </div>

                </div>


                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('usuarios.index') }}"
                       class="btn btn-secondary">

                        Cancelar

                    </a>

                    <button type="submit"
                            class="btn btn-success">

                        <i class="bi bi-save-fill"></i>
                        Actualizar Usuario

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection
