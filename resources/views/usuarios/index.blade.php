@extends('layouts.app')

@section('content')

<div class="container-fluid users-page">

    <div class="app-page-header d-flex justify-content-between align-items-center mb-4">

        <div>
            <span class="app-eyebrow"><i class="bi bi-shield-check"></i> Administración</span>
            <h2 class="page-title mb-1">
                <i class="bi bi-people-fill"></i>
                Gestión de Usuarios
            </h2>

            <p class="text-muted mb-0">
                Administración de usuarios y administradores del sistema
            </p>
        </div>

        <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus-fill"></i>
            Nuevo Usuario
        </a>

    </div>


    {{-- Mensaje de éxito --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif


    {{-- Mensaje de error --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill"></i>
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif


    <div class="card shadow-sm border-0">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                Lista de usuarios
            </h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-dark">

                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Correo electrónico</th>
                            <th>Rol</th>
                            <th>Fecha de registro</th>
                            <th class="text-center">Acciones</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($usuarios as $usuario)

                            <tr>

                                <td>
                                    {{ $usuario->id }}
                                </td>

                                <td>
                                    <i class="bi bi-person-circle"></i>
                                    {{ $usuario->name }}
                                </td>

                                <td>
                                    {{ $usuario->email }}
                                </td>

                                <td>

                                    @if($usuario->role === 'admin')

                                        <span class="badge bg-primary">
                                            <i class="bi bi-shield-fill-check"></i>
                                            Administrador
                                        </span>

                                    @else

                                        <span class="badge bg-primary">
                                            <i class="bi bi-person-fill"></i>
                                            Usuario
                                        </span>

                                    @endif

                                </td>

                                <td>
                                    {{ $usuario->created_at?->format('d/m/Y') }}
                                </td>

                                <td class="text-center">

                                    <a href="{{ route('usuarios.edit', $usuario) }}"
                                       class="btn btn-warning btn-sm">

                                        <i class="bi bi-pencil-fill"></i>
                                        Editar

                                    </a>


                                    <form action="{{ route('usuarios.destroy', $usuario) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('¿Está seguro de eliminar este usuario?');">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-danger btn-sm">

                                            <i class="bi bi-trash-fill"></i>
                                            Eliminar

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6"
                                    class="text-center py-5">

                                    <i class="bi bi-people"
                                       style="font-size: 50px;"></i>

                                    <h5 class="mt-3">
                                        No hay usuarios registrados
                                    </h5>

                                    <p class="text-muted">
                                        Agrega el primer usuario del sistema.
                                    </p>

                                    <a href="{{ route('usuarios.create') }}"
                                       class="btn btn-primary">

                                        <i class="bi bi-person-plus-fill"></i>
                                        Agregar usuario

                                    </a>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection
