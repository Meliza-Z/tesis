@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar Cuenta por Cobrar</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('cuenta_cobrar.update', $cuenta_cobrar) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Cliente</label>
            <input type="text" class="form-control" 
                value="{{ $cuenta_cobrar->cliente->nombre ?? 'No encontrado' }}" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Monto Adeudado</label>
            <input type="text" class="form-control" 
                value="${{ number_format($cuenta_cobrar->monto_adeudado, 2) }}" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Saldo Pendiente</label>
            <input type="text" class="form-control" 
                value="${{ number_format($cuenta_cobrar->saldo_pendiente, 2) }}" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Fecha de Vencimiento</label>
            <input type="date" name="fecha_vencimiento" class="form-control" 
                value="{{ old('fecha_vencimiento', $cuenta_cobrar->fecha_vencimiento) }}" required>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('cuenta_cobrar.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
