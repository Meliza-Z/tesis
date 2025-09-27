<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DomainSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Productos base
            $productos = [
                ['nombre' => 'Laptop Pro 14"', 'categoria' => 'Tecnología', 'precio_base_centavos' => 1250000, 'descripcion' => 'Equipo profesional'],
                ['nombre' => 'Smartphone X', 'categoria' => 'Tecnología', 'precio_base_centavos' => 650000, 'descripcion' => 'Gama alta'],
                ['nombre' => 'Silla ergonómica', 'categoria' => 'Muebles', 'precio_base_centavos' => 180000, 'descripcion' => 'Con soporte lumbar'],
                ['nombre' => 'Escritorio', 'categoria' => 'Muebles', 'precio_base_centavos' => 220000, 'descripcion' => '120x60 cm'],
                ['nombre' => 'Audífonos', 'categoria' => 'Accesorios', 'precio_base_centavos' => 80000, 'descripcion' => 'Inalámbricos'],
            ];
            $productoIds = [];
            foreach ($productos as $p) {
                $productoIds[] = DB::table('productos')->insertGetId([
                    'nombre' => $p['nombre'],
                    'descripcion' => $p['descripcion'],
                    'precio_base_centavos' => $p['precio_base_centavos'],
                    'categoria' => $p['categoria'],
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }

            // Clientes
            $clientes = [
                ['nombre' => 'Ana Pérez', 'cedula' => 'V-12345678', 'direccion' => 'Av. Principal 1', 'telefono' => '0414-1111111', 'email' => 'ana@example.com', 'limite_credito_centavos' => 1500000],
                ['nombre' => 'Luis Gómez', 'cedula' => 'V-87654321', 'direccion' => 'Calle 2', 'telefono' => '0412-2222222', 'email' => 'luis@example.com', 'limite_credito_centavos' => 900000],
                ['nombre' => 'María López', 'cedula' => 'V-11223344', 'direccion' => 'Urb. Norte', 'telefono' => '0416-3333333', 'email' => 'maria@example.com', 'limite_credito_centavos' => 2000000],
                ['nombre' => 'Pedro Ruiz', 'cedula' => 'V-44332211', 'direccion' => 'Sector Sur', 'telefono' => '0424-4444444', 'email' => 'pedro@example.com', 'limite_credito_centavos' => 500000],
            ];
            $clienteIds = [];
            foreach ($clientes as $c) {
                $clienteIds[] = DB::table('clientes')->insertGetId([
                    'nombre' => $c['nombre'],
                    'cedula' => $c['cedula'],
                    'direccion' => $c['direccion'],
                    'telefono' => $c['telefono'],
                    'email' => $c['email'],
                    'limite_credito_centavos' => $c['limite_credito_centavos'],
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }

            $hoy = Carbon::today();
            $creditosCreados = [];
            $totalesCliente = [];

            // Helper para crear crédito con detalles y pagos
            $crearCredito = function (int $clienteId, array $items, array $pagos, int $plazoDias = 15, ?int $extDias = null, ?string $codigo = null) use (&$hoy, &$creditosCreados) {
                $fechaCredito = (clone $hoy)->subDays(rand(0, 20));
                $fechaVenc = (clone $fechaCredito)->addDays($plazoDias);
                $fechaVencExt = $extDias ? (clone $fechaVenc)->addDays($extDias) : null;
                $creditoId = DB::table('creditos')->insertGetId([
                    'cliente_id' => $clienteId,
                    'codigo' => $codigo ?? Str::upper(Str::random(8)),
                    'fecha_credito' => $fechaCredito->toDateString(),
                    'plazo_dias' => $plazoDias,
                    'fecha_vencimiento' => $fechaVenc->toDateString(),
                    'fecha_vencimiento_ext' => $fechaVencExt?->toDateString(),
                    'estado' => 'pendiente',
                    'created_at' => now(), 'updated_at' => now(),
                ]);

                // Detalles
                $total = 0;
                foreach ($items as $it) {
                    $subtotal = $it['cantidad'] * $it['precio_unitario_centavos'];
                    $total += $subtotal;
                    DB::table('detalle_creditos')->insert([
                        'credito_id' => $creditoId,
                        'producto_id' => $it['producto_id'],
                        'cantidad' => $it['cantidad'],
                        'precio_unitario_centavos' => $it['precio_unitario_centavos'],
                        'subtotal_centavos' => $subtotal,
                        'observaciones' => $it['observaciones'] ?? null,
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }

                // Pagos
                $totalPagado = 0;
                foreach ($pagos as $pago) {
                    $totalPagado += $pago['monto_pagado_centavos'];
                    DB::table('pagos')->insert([
                        'credito_id' => $creditoId,
                        'fecha_pago' => $pago['fecha_pago'] ?? $hoy->toDateString(),
                        'monto_pagado_centavos' => $pago['monto_pagado_centavos'],
                        'metodo_pago' => $pago['metodo_pago'] ?? null,
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }

                $saldo = max(0, $total - $totalPagado);
                $vencEfectivo = $fechaVencExt ?? $fechaVenc;
                $estado = $saldo === 0 ? 'pagado' : ($vencEfectivo->lt($hoy) ? 'vencido' : 'activo');
                DB::table('creditos')->where('id', $creditoId)->update(['estado' => $estado, 'updated_at' => now()]);

                $creditosCreados[] = compact('creditoId', 'clienteId', 'total', 'totalPagado', 'saldo', 'vencEfectivo', 'estado');
                return end($creditosCreados);
            };

            // Crear créditos respetando reglas: hasta 2 abiertos, algunos vencidos/pagados
            foreach ($clienteIds as $idx => $clienteId) {
                $limite = DB::table('clientes')->where('id', $clienteId)->value('limite_credito_centavos');

                // Crédito 1
                $c1 = $crearCredito($clienteId, [
                    ['producto_id' => $productoIds[0], 'cantidad' => 1, 'precio_unitario_centavos' => 1250000],
                    ['producto_id' => $productoIds[4], 'cantidad' => 1, 'precio_unitario_centavos' => 80000],
                ], [
                    ['monto_pagado_centavos' => 400000, 'metodo_pago' => 'efectivo', 'fecha_pago' => $hoy->copy()->subDays(5)->toDateString()],
                ], 15, $idx === 0 ? 5 : null);

                // Crédito 2 (solo si no supera límite y para algunos clientes)
                $sumExpuesto = $c1['saldo'];
                if ($idx % 2 === 0 && $sumExpuesto + 200000 <= $limite) {
                    $crearCredito($clienteId, [
                        ['producto_id' => $productoIds[2], 'cantidad' => 1, 'precio_unitario_centavos' => 180000],
                        ['producto_id' => $productoIds[3], 'cantidad' => 1, 'precio_unitario_centavos' => 220000],
                    ], [
                        // sin pagos para forzar activo/vencido
                    ], 15, $idx === 2 ? null : 0);
                }
            }

            // Cuentas por cobrar (agregado por cliente)
            foreach ($clienteIds as $clienteId) {
                $creditosCliente = array_values(array_filter($creditosCreados, fn ($c) => $c['clienteId'] === $clienteId));
                if (empty($creditosCliente)) {
                    continue;
                }
                $montoAdeudado = array_sum(array_column($creditosCliente, 'total'));
                $saldoPend = array_sum(array_column($creditosCliente, 'saldo'));
                // fecha_vencimiento: la más próxima entre los créditos abiertos; si todos pagados, la más reciente
                $abiertos = array_filter($creditosCliente, fn ($c) => in_array($c['estado'], ['activo', 'vencido']));
                $fechaVenc = !empty($abiertos)
                    ? min(array_map(fn ($c) => $c['vencEfectivo'], $abiertos))
                    : max(array_map(fn ($c) => $c['vencEfectivo'], $creditosCliente));
                $estado = array_reduce($creditosCliente, fn ($carry, $c) => $carry || $c['estado'] === 'vencido', false) ? 'mora' : 'al_dia';

                DB::table('cuentas_por_cobrar')->insert([
                    'cliente_id' => $clienteId,
                    'monto_adeudado_centavos' => $montoAdeudado,
                    'saldo_pendiente_centavos' => $saldoPend,
                    'fecha_vencimiento' => Carbon::parse($fechaVenc)->toDateString(),
                    'estado' => $estado,
                    'proximo_recordatorio_at' => $estado === 'mora' ? now()->addDay() : now()->addDays(3),
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }

            // Reportes (múltiples tipos)
            $tipos = [
                'creditos_vencidos',
                'cartera_total',
                'cobros_periodo',
                'proximos_a_vencer',
                'clientes_en_riesgo',
                'ventas_por_categoria',
            ];

            foreach ($tipos as $tipo) {
                DB::table('reportes')->insert([
                    'fecha_reporte' => $hoy->toDateString(),
                    'tipo_reporte' => $tipo,
                    'cantidad_registros' => 0, // se puede recalcular en generación real
                    'monto_total_centavos' => 0,
                    'descripcion' => 'Reporte inicial seed: ' . $tipo,
                    'detalles' => json_encode(['nota' => 'Semilla de ejemplo']),
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        });
    }
}

