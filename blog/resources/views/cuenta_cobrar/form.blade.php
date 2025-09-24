{{-- Variables disponibles:
    - $clientes (en create y edit)
    - $cuenta (solo en edit)
--}}

<div class="mb-3">
    <label for="cliente_id" class="form-label">Cliente</label>
    <select name="cliente_id" id="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" required>
        <option value="">-- Seleccione un cliente --</option>
        @foreach($clientes as $cliente)
            <option value="{{ $cliente->id }}"
                {{ (old('cliente_id') ?? ($cuenta->cliente_id ?? '')) == $cliente->id ? 'selected' : '' }}>
                {{ $cliente->nombre }}
            </option>
        @endforeach
    </select>
    @error('cliente_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="monto_adeudado" class="form-label">Monto Adeudado</label>
    <input type="number" step="0.01" min="0" name="monto_adeudado" id="monto_adeudado" class="form-control @error('monto_adeudado') is-invalid @enderror" 
        value="{{ old('monto_adeudado') ?? ($cuenta->monto_adeudado ?? '') }}" required>
    @error('monto_adeudado')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="saldo_pendiente" class="form-label">Saldo Pendiente</label>
    <input type="number" step="0.01" min="0" name="saldo_pendiente" id="saldo_pendiente" class="form-control @error('saldo_pendiente') is-invalid @enderror" 
        value="{{ old('saldo_pendiente') ?? ($cuenta->saldo_pendiente ?? '') }}" required>
    @error('saldo_pendiente')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
    <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control @error('fecha_vencimiento') is-invalid @enderror"
        value="{{ old('fecha_vencimiento') ?? (isset($cuenta) ? $cuenta->fecha_vencimiento->format('Y-m-d') : '') }}" required>
    @error('fecha_vencimiento')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="estado" class="form-label">Estado</label>
    <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror" required>
        <option value="">-- Seleccione un estado --</option>
        <option value="al_dia" {{ (old('estado') ?? ($cuenta->estado ?? '')) == 'al_dia' ? 'selected' : '' }}>Al día</option>
        <option value="mora" {{ (old('estado') ?? ($cuenta->estado ?? '')) == 'mora' ? 'selected' : '' }}>Mora</option>
    </select>
    @error('estado')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
