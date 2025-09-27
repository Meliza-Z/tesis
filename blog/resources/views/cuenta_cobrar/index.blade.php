@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-money-check-alt me-2 text-primary"></i>
                        Cuentas por Cobrar
                    </h1>
                    <p class="text-muted mb-0">Listado de créditos con progreso y acciones</p>
                </div>
                <div class="input-group" style="max-width: 320px;">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="buscarCliente" class="form-control" placeholder="Buscar cliente o código...">
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Créditos</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $creditos->count() }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-credit-card fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pendiente</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($creditos->sum('saldo'), 2) }}</div>
                            </div>
                            <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-table me-2"></i>Listado de Créditos</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tablaCuentas">
                        <thead class="table-light">
                            <tr>
                                <th><i class="fas fa-user me-1"></i>Cliente</th>
                                <th><i class="fas fa-hashtag me-1"></i>Código</th>
                                <th class="text-end"><i class="fas fa-shopping-cart me-1"></i>Monto</th>
                                <th class="text-end"><i class="fas fa-balance-scale me-1"></i>Saldo</th>
                                <th class="text-center"><i class="fas fa-calendar-alt me-1"></i>Vencimiento</th>
                                <th><i class="fas fa-chart-line me-1"></i>Progreso</th>
                                <th class="text-center"><i class="fas fa-cogs me-1"></i>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($creditos as $c)
                                @php $progress = min(100, max(0, $c->progreso)); @endphp
                                <tr>
                                    <td class="align-middle">{{ $c->cliente }}</td>
                                    <td class="align-middle"><a href="{{ route('cuenta_cobrar.credito', ['credito' => $c->id]) }}">{{ $c->codigo }}</a></td>
                                    <td class="text-end align-middle">${{ number_format($c->monto, 2) }}</td>
                                    <td class="text-end align-middle">${{ number_format($c->saldo, 2) }}</td>
                                    <td class="text-center align-middle">{{ $c->vence ? \Carbon\Carbon::parse($c->vence)->format('d/m/Y') : 'N/D' }}</td>
                                    <td class="align-middle" style="min-width: 160px;">
                                        <div class="progress" style="height:8px;">
                                            <div class="progress-bar {{ $c->saldo <= 0 ? 'bg-success' : 'bg-info' }}" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-muted">{{ $progress }}%</small>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#pagoModal" data-credito-id="{{ $c->id }}" title="Registrar pago"><i class="fas fa-dollar-sign"></i></button>
                                            <a href="{{ route('cuenta_cobrar.credito', ['credito' => $c->id]) }}" class="btn btn-outline-primary" title="Ver crédito"><i class="fas fa-eye"></i></a>
                                            @if($c->saldo <= 0)
                                                <form action="{{ route('creditos.markPaid', ['credito' => $c->id]) }}" method="POST" onsubmit="return confirm('Marcar como pagado este crédito?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success" title="Marcar pagado"><i class="fas fa-check"></i></button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal de Pago -->
        <div class="modal fade" id="pagoModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-money-bill me-2"></i>Registrar Pago</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @php($pago = new \App\Models\Pago())
                        @include('pagos.form', [
                            'route' => route('pagos.store'),
                            'method' => 'POST',
                            'pago' => $pago,
                            'creditos' => $creditos,
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('buscarCliente');
            if (input) {
                input.addEventListener('input', function(e) {
                    const t = e.target.value.toLowerCase().trim();
                    document.querySelectorAll('#tablaCuentas tbody tr').forEach(row => {
                        const cliente = (row.children[0]?.innerText || '').toLowerCase();
                        const codigo = (row.children[1]?.innerText || '').toLowerCase();
                        row.style.display = (!t || cliente.includes(t) || codigo.includes(t)) ? '' : 'none';
                    });
                });
            }

            // Preseleccionar crédito en el modal
            const pagoModal = document.getElementById('pagoModal');
            if (pagoModal) {
                pagoModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const creditoId = button?.getAttribute('data-credito-id');
                    const select = pagoModal.querySelector('select[name="credito_id"]');
                    if (select && creditoId) {
                        select.value = creditoId;
                    }
                });
            }
        });
    </script>
@endsection
