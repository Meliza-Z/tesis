@extends('layouts.auth')

@section('content')
<div class="text-center mb-4">
    <h5>Crear Cuenta</h5>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <small>{{ $error }}</small><br>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf
    
    <div class="input-group">
        <span class="input-group-text">
            <i class="fas fa-user"></i>
        </span>
        <input type="text" 
               class="form-control @error('name') is-invalid @enderror" 
               name="name" 
               value="{{ old('name') }}" 
               placeholder="Nombre completo" 
               required>
    </div>

    <div class="input-group">
        <span class="input-group-text">
            <i class="fas fa-envelope"></i>
        </span>
        <input type="email" 
               class="form-control @error('email') is-invalid @enderror" 
               name="email" 
               value="{{ old('email') }}" 
               placeholder="Email" 
               required>
    </div>

    <div class="input-group">
        <span class="input-group-text">
            <i class="fas fa-lock"></i>
        </span>
        <input type="password" 
               class="form-control @error('password') is-invalid @enderror" 
               name="password" 
               placeholder="Contraseña (mín. 8 caracteres)" 
               required>
    </div>

    <div class="input-group">
        <span class="input-group-text">
            <i class="fas fa-lock"></i>
        </span>
        <input type="password" 
               class="form-control" 
               name="password_confirmation" 
               placeholder="Confirmar contraseña" 
               required>
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-user-plus me-2"></i>Crear Cuenta
        </button>
    </div>
</form>

<div class="text-center mt-3">
    <a href="{{ route('login') }}" class="text-decoration-none">
        ¿Ya tienes cuenta? Inicia sesión aquí
    </a>
</div>
@endsection