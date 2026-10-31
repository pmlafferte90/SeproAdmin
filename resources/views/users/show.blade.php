@extends('layouts.app')

@section('title')
Detalles del Usuario
@endsection


@section('content')
<div class="container mt-5">
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">Detalles del Usuario</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>Nombre:</strong> {{ $user->usuario }}
            </div>
            <div class="mb-3">
                <strong>Rol:</strong> {{ $user->rol }}
            </div>
            <div class="mb-3">
                <strong>Empresa:</strong> {{ $user->empresa }} <!-- Asumiendo que 'empresa' es una propiedad del usuario -->
            </div>
            <div class="mb-3">
                <strong>Fecha de Creación:</strong> {{ $user->fecha_creacion->format('d-m-Y') }} <!-- Formato de fecha -->
            </div>

            <hr>

            <h5 class="mt-4">Cambiar Contraseña</h5>
            <form action="{{ route('users.changePassword', $user->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="password" class="form-label">Nueva Contraseña:</label>
                    <input type="password" class="form-control" name="password" id="password" required>
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña:</label>
                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
                </div>
                <button type="submit" class="btn btn-success">Cambiar Contraseña</button>
            </form>

        </div>
    </div>
</div>
@endsection
