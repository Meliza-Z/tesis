@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-file-invoice-dollar me-2 text-primary"></i>
                        Crédito {{ $credito->codigo }} — {{ $credito->cliente->nombre ?? 'N/D' }}
                    </h4>
                    <p class="text-muted mb-0">
                        Monto: ${{ number_format($credito->monto_total, 2) }} · Pagado:
                        ${{ number_format($credito->total_pagado_centavos / 100, 2) }} · Saldo:
                        ${{ number_format($credito->saldo_pendiente, 2) }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('cuenta_cobrar.index') }}" class="btn btn-secondary"><i
                            class="fas fa-arrow-left me-2"></i>Volver</a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light"><strong><i class="fas fa-list me-2"></i>Ítems del Crédito</strong></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio Unit.</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($credito->detalles as $d)
                                <tr>
                                    <td>{{ $d->producto->nombre ?? 'N/D' }}</td>
                                    <td class="text-center">{{ $d->cantidad }}</td>
                                    <td class="text-end">${{ number_format($d->precio_unitario, 2) }}</td>
                                    <td class="text-end">${{ number_format($d->subtotal, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Sin ítems</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total</th>
                                <th class="text-end">${{ number_format($credito->monto_total, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-light"><strong><i class="fas fa-money-bill me-2"></i>Registrar Pago</strong></div>
            <div class="card-body">
                @include('pagos.form', [
                    'route' => route('pagos.store'),
                    'method' => 'POST',
                    'pago' => $pago,
                    'creditos' => $creditos,
                ])
            </div>
        </div>


    </div>
@endsection
