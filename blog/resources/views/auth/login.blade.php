@extends('layouts.auth')

@section('content')
<div class="text-center mb-4">
    <h5>Iniciar Sesión</h5>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <small>{{ $error }}</small><br>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf
    
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
               placeholder="Contraseña" 
               required>
    </div>

    <div class="d-grid">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
        </button>
    </div>
</form>

<div class="text-center mt-3">
    <a href="{{ route('register') }}" class="text-decoration-none">
        ¿No tienes cuenta? Regístrate aquí
    </a>
</div>
@endsection