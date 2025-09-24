@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ isset($cuenta) ? 'Editar' : 'Crear' }} Cuenta por Cobrar</h2>

    <form action="{{ isset($cuenta) ? route('cuenta_cobrar.update', $cuenta) : route('cuenta_cobrar.store') }}" method="POST">
        @csrf
        @if(isset($cuenta))
            @method('PUT')
        @endif

        <div class="mb-3">
            <label class="form-label">Cliente</label>
            <select name="cliente_id" class="form-control" required>
                <option value="">Seleccione un cliente</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}"
                        {{ (isset($cuenta) && $cuenta->cliente_id == $cliente->id) || old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                        {{ $cliente->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Monto Adeudado</label>
            <input type="number" step="0.01" name="monto_adeudado" class="form-control" 
                value="{{ old('monto_adeudado', $cuenta->monto_adeudado ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Saldo Pendiente</label>
            <input type="number" step="0.01" name="saldo_pendiente" class="form-control" 
                value="{{ old('saldo_pendiente', $cuenta->saldo_pendiente ?? '') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Fecha de Vencimiento</label>
            <input type="date" name="fecha_vencimiento" class="form-control" 
                value="{{ old('fecha_vencimiento', $cuenta->fecha_vencimiento ?? now()->toDateString()) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-control" required>
                <option value="al_dia" {{ (isset($cuenta) && $cuenta->estado == 'al_dia') || old('estado') == 'al_dia' ? 'selected' : '' }}>Al día</option>
                <option value="mora" {{ (isset($cuenta) && $cuenta->estado == 'mora') || old('estado') == 'mora' ? 'selected' : '' }}>En mora</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('cuenta_cobrar.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
