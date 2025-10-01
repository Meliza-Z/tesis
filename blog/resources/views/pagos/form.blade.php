@php
    // Estas variables deben ser pasadas a la vista, por ejemplo,
    // desde PagoController@create:
    // return view('pagos.create', compact('creditos'))->with(['route' => route('pagos.store'), 'method' => 'POST', 'pago' => new \App\Models\Pago()]);
    // y desde PagoController@edit:
    // return view('pagos.edit', compact('pago', 'creditos'))->with(['route' => route('pagos.update', $pago), 'method' => 'PUT']);

    // Si $pago no está definido (ej. en la vista create), lo inicializamos
    $pago = $pago ?? new \App\Models\Pago();
@endphp

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-{{ isset($pago->id) ? 'edit' : 'plus' }} me-2 text-primary"></i>
            {{ isset($pago->id) ? 'Editar Pago' : 'Registrar Nuevo Pago' }}
        </h1>
        <a href="{{ route('cuenta_cobrar.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i>
            Volver a Pagos
        </a>
    </div>
    <p class="text-muted mb-4">
        {{ isset($pago->id) ? 'Modifica los detalles del pago existente.' : 'Completa el formulario para registrar un nuevo pago.' }}
    </p>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-money-check-alt me-2"></i>
                Datos del Pago
            </h6>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ $route }}" method="POST">
                @csrf
                @if ($method === 'PUT')
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="credito_id" class="form-label fw-bold">Crédito <span
                                    class="text-danger">*</span></label>
                            <select name="credito_id" id="credito_id" class="form-control form-control-sm" required>
                                <option value="">Seleccione un crédito</option>
                                @foreach ($creditos as $credito)
                                    <option value="{{ $credito->id }}"
                                        {{ old('credito_id', $pago->credito_id) == $credito->id ? 'selected' : '' }}>
                                        Crédito #{{ $credito->id }} -
                                        {{ is_string($credito->cliente) ? $credito->cliente : optional($credito->cliente)->nombre ?? 'Cliente desconocido' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('credito_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha_pago" class="form-label fw-bold">Fecha de Pago <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="fecha_pago" id="fecha_pago" class="form-control form-control-sm"
                                value="{{ old('fecha_pago', $pago->fecha_pago ?? now()->toDateString()) }}" required>
                            @error('fecha_pago')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="monto_pago" class="form-label fw-bold">Monto del Pago <span
                                    class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" name="monto_pago" id="monto_pago"
                                    class="form-control" value="{{ old('monto_pago', $pago->monto_pago) }}" required
                                    min="0.01">
                            </div>
                            @error('monto_pago')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="metodo_pago" class="form-label fw-bold">Método de Pago</label>
                            <select name="metodo_pago" id="metodo_pago" class="form-control form-control-sm">
                                <option value="">Seleccione un método</option>
                                <option value="Efectivo"
                                    {{ old('metodo_pago', $pago->metodo_pago) == 'Efectivo' ? 'selected' : '' }}>
                                    Efectivo</option>
                                <option value="Transferencia"
                                    {{ old('metodo_pago', $pago->metodo_pago) == 'Transferencia' ? 'selected' : '' }}>
                                    Transferencia</option>
                                <option value="Tarjeta"
                                    {{ old('metodo_pago', $pago->metodo_pago) == 'Tarjeta' ? 'selected' : '' }}>Tarjeta
                                </option>
                                {{-- Puedes añadir más opciones aquí --}}
                            </select>
                            @error('metodo_pago')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>



                <hr class="my-4">

                <button type="submit" class="btn btn-success me-2">
                    <i class="fas fa-save me-1"></i> Guardar Pago
                </button>
                <a href="{{ route('cuenta_cobrar.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times-circle me-1"></i> Cancelar
                </a>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script></script>
@endpush
