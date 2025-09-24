<?php

namespace App\Http\Controllers;

use App\Models\Credito;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; 
use Carbon\Carbon;

class CreditoController extends Controller
{
    /**
     * Muestra una lista de créditos con funcionalidad de búsqueda.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Credito::with('cliente');

        // Aplicar el filtro de búsqueda si el parámetro 'search' está presente
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->whereHas('cliente', function ($q) use ($searchTerm) {
                $q->where('nombre', 'LIKE', '%' . $searchTerm . '%');
            })
            ->orWhere('codigo', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('estado', 'LIKE', '%' . $searchTerm . '%')
            ->orWhereRaw('CAST(monto_total AS CHAR) LIKE ?', ['%' . $searchTerm . '%'])
            ->orWhereRaw('CAST(saldo_pendiente AS CHAR) LIKE ?', ['%' . $searchTerm . '%']);
        }

        $creditos = $query->get();

        // Si la solicitud es AJAX
        if ($request->ajax()) {
            $formattedCreditos = $creditos->map(function ($credito) {
                $fechaVencimiento = Carbon::parse($credito->fecha_vencimiento);
                $isExpiredAndUnpaid = $fechaVencimiento->isPast() && $credito->estado !== 'pagado';

                return [
                    'id' => $credito->id,
                    'codigo' => $credito->codigo,
                    'cliente' => [
                        'nombre' => $credito->cliente->nombre,
                    ],
                    'fecha_credito' => $credito->fecha_credito,
                    'fecha_vencimiento' => $credito->fecha_vencimiento,
                    'monto_total' => $credito->monto_total,
                    'saldo_pendiente' => $credito->saldo_pendiente,
                    'estado' => $credito->estado,
                    'is_expired_and_unpaid' => $isExpiredAndUnpaid,
                ];
            });
            return response()->json(['creditos' => $formattedCreditos]);
        }

        return view('creditos.index', compact('creditos'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        return view('creditos.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha_credito' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_credito',
            'monto_total' => 'required|numeric|min:0.01',
            'estado' => 'nullable|in:pendiente,pagado',
        ]);

        $credito = new Credito();
        $credito->cliente_id = $request->cliente_id;
        $credito->codigo = $this->generarCodigoUnico();
        $credito->fecha_credito = $request->fecha_credito;
        $credito->fecha_vencimiento = $request->fecha_vencimiento;
        $credito->monto_total = $request->monto_total;
        $credito->saldo_pendiente = $request->monto_total; // Inicialmente igual al monto total
        $credito->estado = $request->estado ?? 'pendiente';
        $credito->save();

        return redirect()->route('creditos.index')->with('success', 'Crédito creado exitosamente.');
    }

    public function show(Credito $credito)
    {
        $credito->load('cliente', 'pagos', 'detalles');
        return view('creditos.show', compact('credito'));
    }

    public function edit(Credito $credito)
    {
        $clientes = Cliente::all();
        return view('creditos.edit', compact('credito', 'clientes'));
    }

    public function update(Request $request, Credito $credito)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'codigo' => 'required|string|unique:creditos,codigo,' . $credito->id,
            'fecha_credito' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_credito',
            'monto_total' => 'required|numeric|min:0.01',
            'saldo_pendiente' => 'required|numeric|min:0|lte:monto_total',
            'estado' => 'required|in:pendiente,pagado',
        ]);

        // Si el estado cambia a 'pagado', el saldo pendiente debe ser 0
        if ($request->estado === 'pagado') {
            $request->merge(['saldo_pendiente' => 0]);
        }

        $credito->update($request->all());

        return redirect()->route('creditos.index')->with('success', 'Crédito actualizado correctamente.');
    }

    public function destroy(Credito $credito)
    {
        $credito->delete();
        return redirect()->route('creditos.index')->with('success', 'Crédito eliminado correctamente.');
    }
    
   
    /**
     * Genera un código único para el crédito
     *
     * @return string
     */
    private function generarCodigoUnico()
    {
        do {
            // Generar código con formato: CR + año + mes + número aleatorio de 4 dígitos
            $codigo = 'CR' . date('Ym') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Credito::where('codigo', $codigo)->exists());

        return $codigo;
    }

    /**
     * Actualiza el saldo pendiente de un crédito
     * Útil cuando se registren pagos
     *
     * @param Credito $credito
     * @return void
     */
    public function actualizarSaldoPendiente(Credito $credito)
    {
        // Calcular el total de pagos realizados
        $totalPagos = $credito->pagos()->sum('monto') ?? 0;
        
        // Calcular el saldo pendiente
        $saldoPendiente = $credito->monto_total - $totalPagos;
        
        // Actualizar el saldo y estado
        $credito->saldo_pendiente = max(0, $saldoPendiente);
        $credito->estado = $credito->saldo_pendiente <= 0 ? 'pagado' : 'pendiente';
        $credito->save();
    }
}