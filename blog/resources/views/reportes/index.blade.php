@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Dashboard Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">
                            <i class="fas fa-chart-bar me-2 text-info"></i>
                            Generación de Reportes
                        </h1>
                        <p class="text-muted mb-0">Centro de reportes y análisis de datos</p>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-outline-primary" onclick="mostrarAyuda()">
                            <i class="fas fa-question-circle me-1"></i>
                            Ayuda
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Cards -->

        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Créditos Activos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $creditos->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tipos de Reportes -->
    <div class="row">
        <!-- Reporte Diario -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-calendar-day me-2"></i>
                            Reporte Diario
                        </h6>
                        <i class="fas fa-file-pdf fa-2x"></i>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Genera un reporte completo de todas las transacciones y actividades de un día específico.
                    </p>
                    <form id="form-reporte-diario" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="fecha_diario" class="form-label fw-bold">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Selecciona la fecha:
                            </label>
                            <input type="date" id="fecha_diario" name="fecha" class="form-control form-control-lg"
                                required>
                            <div class="invalid-feedback">
                                Por favor selecciona una fecha válida.
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-file-pdf me-2"></i>
                                Generar Reporte Diario
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-muted">
                    <small>
                        <i class="fas fa-info-circle me-1"></i>
                        El reporte incluye: ventas, pagos, créditos y resumen financiero
                    </small>
                </div>
            </div>
        </div>

        <!-- Reporte por Cliente -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-user me-2"></i>
                            Reporte por Cliente
                        </h6>
                        <i class="fas fa-file-pdf fa-2x"></i>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Genera un reporte detallado del historial de créditos y pagos de un cliente específico.
                    </p>
                    <form id="form-reporte-cliente" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="credito_id" class="form-label fw-bold">
                                <i class="fas fa-search me-1"></i>
                                Selecciona un Crédito:
                            </label>
                            <select name="credito_id" id="credito_id" class="form-select form-select-lg" required>
                                <option value="">-- Seleccione un crédito --</option>
                                @foreach ($creditos as $credito)
                                    <option value="{{ $credito->id }}"
                                        data-cliente="{{ $credito->cliente->nombre ?? 'Sin cliente' }}"
                                        data-monto="{{ number_format($credito->monto_total, 2) }}"
                                        data-fecha="{{ $credito->fecha_credito }}">
                                        {{ $credito->cliente->nombre ?? 'Sin cliente' }} -
                                        Fecha: {{ \Carbon\Carbon::parse($credito->fecha_credito)->format('d/m/Y') }} -
                                        Monto: ${{ number_format($credito->monto_total, 2) }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Por favor selecciona un crédito válido.
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-file-pdf me-2"></i>
                                Generar Reporte Cliente
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-muted">
                    <small>
                        <i class="fas fa-info-circle me-1"></i>
                        El reporte incluye: historial de créditos, pagos y estado actual
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Reportes Rápidos -->
    <div class="card shadow mb-4">
        <div class="card-header bg-info text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-bolt me-2"></i>
                Reportes Rápidos
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <button class="btn btn-outline-primary w-100" onclick="generarReporteRapido('hoy')">
                        <i class="fas fa-calendar-day d-block mb-2 fa-2x"></i>
                        Reporte de Hoy
                    </button>
                </div>
                <div class="col-md-6 mb-3">
                    <button class="btn btn-outline-success w-100" onclick="generarReporteRapido('cliente-actual')">
                        <i class="fas fa-user d-block mb-2 fa-2x"></i>
                        Último Cliente
                    </button>
                </div>
                <div class="col-md-6 mb-3">
                    <button class="btn btn-outline-danger w-100" onclick="generarReporteRapido('creditos-vencidos')">
                        <i class="fas fa-exclamation-triangle d-block mb-2 fa-2x"></i>
                        Créditos Vencidos
                    </button>
                </div>
                <div class="col-md-6 mb-3">
                    <button class="btn btn-outline-warning w-100" onclick="generarReporteRapido('resumen-cartera')">
                        <i class="fas fa-briefcase d-block mb-2 fa-2x"></i>
                        Resumen de Cartera
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Progreso -->
    <div class="modal fade" id="progressModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-cog fa-spin me-2"></i>
                        Generando Reporte
                    </h5>
                </div>
                <div class="modal-body text-center">
                    <div class="progress mb-3">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                            style="width: 0%" id="progressBar">
                        </div>
                    </div>
                    <p class="text-muted mb-0" id="progressText">Iniciando generación...</p>
                </div>
            </div>
        </div>
    </div>
    </div>

    <style>
        .border-left-primary {
            border-left: 4px solid #4e73df !important;
        }

        .border-left-success {
            border-left: 4px solid #1cc88a !important;
        }

        .border-left-info {
            border-left: 4px solid #36b9cc !important;
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .text-xs {
            font-size: 0.7rem;
        }

        .font-weight-bold {
            font-weight: 700;
        }

        .text-gray-800 {
            color: #5a5c69;
        }

        .text-gray-300 {
            color: #dddfeb;
        }

        .btn-outline-primary:hover,
        .btn-outline-success:hover {
            transform: translateY(-1px);
        }

        .form-control-lg,
        .form-select-lg {
            padding: 0.75rem 1rem;
            font-size: 1.1rem;
        }

        .progress-bar-animated {
            animation: progress-bar-stripes 1s linear infinite;
        }

        @keyframes progress-bar-stripes {
            0% {
                background-position: 1rem 0;
            }

            100% {
                background-position: 0 0;
            }
        }
    </style>

    <script>
        // Configuración de validación Bootstrap
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        // Reporte Diario
        document.getElementById('form-reporte-diario').addEventListener('submit', function(e) {
            e.preventDefault();
            if (this.checkValidity()) {
                const fecha = document.getElementById('fecha_diario').value;
                mostrarProgreso('Generando reporte diario...');

                setTimeout(() => {
                    window.open(`{{ route('reporte.diario') }}?fecha=${fecha}`, '_blank');
                    ocultarProgreso();
                }, 1500);
            }
        });

        // Reporte por Cliente
        document.getElementById('form-reporte-cliente').addEventListener('submit', function(e) {
            e.preventDefault();
            if (this.checkValidity()) {
                const creditoId = document.getElementById('credito_id').value;
                const clienteNombre = document.getElementById('credito_id').selectedOptions[0].dataset.cliente;

                mostrarProgreso(`Generando reporte para ${clienteNombre}...`);

                setTimeout(() => {
                    window.open(`{{ url('reporte-cliente') }}/${creditoId}`, '_blank');
                    ocultarProgreso();
                }, 1500);
            }
        });

        // Reportes Rápidos
        function generarReporteRapido(tipo) {
            const mensajes = {
                'hoy': 'Generando reporte del día de hoy...',
                'cliente-actual': 'Generando reporte del último cliente...',
                'creditos-vencidos': 'Generando reporte de créditos vencidos...',
                'resumen-cartera': 'Generando resumen de cartera (cierre de caja)...'
            };

            mostrarProgreso(mensajes[tipo]);

            setTimeout(() => {
                if (tipo === 'hoy') {
                    const fechaHoy = new Date().toISOString().split('T')[0];
                    window.open(`{{ route('reporte.diario') }}?fecha=${fechaHoy}`, '_blank');
                } else if (tipo === 'cliente-actual') {
                    const selectCredito = document.getElementById('credito_id');
                    if (selectCredito.options.length > 1) {
                        const ultimoCredito = selectCredito.options[1].value;
                        window.open(`{{ url('reporte-cliente') }}/${ultimoCredito}`, '_blank');
                    } else {
                        alert('No hay créditos disponibles para generar reporte.');
                    }
                } else if (tipo === 'creditos-vencidos') {
                    window.open(`{{ route('reporte.creditos.vencidos') }}`, '_blank');
                } else if (tipo === 'resumen-cartera') {
                    window.open(`{{ route('reporte.resumen.cartera') }}`, '_blank');
                }
                ocultarProgreso();
            }, 1500);
        }

        // Funciones de progreso
        function mostrarProgreso(mensaje) {
            document.getElementById('progressText').textContent = mensaje;
            document.getElementById('progressBar').style.width = '0%';

            const modal = new bootstrap.Modal(document.getElementById('progressModal'));
            modal.show();

            // Simular progreso
            let progreso = 0;
            const interval = setInterval(() => {
                progreso += Math.random() * 30;
                if (progreso >= 100) {
                    progreso = 100;
                    clearInterval(interval);
                }
                document.getElementById('progressBar').style.width = progreso + '%';
            }, 200);
        }

        function ocultarProgreso() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('progressModal'));
            if (modal) {
                modal.hide();
            }
        }

        // Funciones adicionales
        function mostrarAyuda() {
            alert('Centro de Ayuda para Reportes:\n\n' +
                '• Reporte Diario: Muestra todas las transacciones del día\n' +
                '• Reporte Cliente: Historial completo de un cliente específico\n' +
                '• Reportes Rápidos:\n' +
                '  - Reporte de Hoy: Transacciones del día actual\n' +
                '  - Último Cliente: Reporte del último crédito registrado\n' +
                '  - Créditos Vencidos: Lista de todos los créditos vencidos\n' +
                '  - Resumen de Cartera: Estado consolidado tipo cierre de caja');
        }


        // Establecer fecha actual por defecto
        document.addEventListener('DOMContentLoaded', function() {
            const fechaHoy = new Date().toISOString().split('T')[0];
            document.getElementById('fecha_diario').value = fechaHoy;
        });
    </script>
@endsection
