<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    // Mostrar lista de clientes
    public function index()
    {
        $clientes = Cliente::with('creditos')->get();
        return view('clientes.index', compact('clientes'));
    }

    // Mostrar formulario para crear un nuevo cliente
    public function create()
    {
        return view('clientes.create');
    }

    // Guardar un nuevo cliente
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'cedula' => 'nullable|string|digits:10|numeric|unique:clientes,cedula',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|digits:10|numeric',
            'email' => 'nullable|email|max:255|unique:clientes,email',
            'limite_credito' => 'nullable|numeric|min:0',
        ]);

        $cliente = new Cliente();
        $cliente->nombre = $request->nombre;
        $cliente->cedula = $request->cedula;
        $cliente->direccion = $request->direccion;
        $cliente->telefono = $request->telefono;
        $cliente->email = $request->email;
        if ($request->filled('limite_credito')) {
            $cliente->limite_credito = $request->limite_credito; // mutator -> _centavos
        }
        $cliente->save();

        return redirect()->route('clientes.index')->with('success', 'Cliente creado correctamente.');
    }

    // Mostrar un cliente específico
    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    // Mostrar formulario para editar un cliente
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    // Actualizar un cliente
    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'cedula' => 'nullable|string|digits:10|numeric|unique:clientes,cedula,' . $cliente->id,
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|digits:10|numeric',
            'email' => 'nullable|email|max:255|unique:clientes,email,' . $cliente->id,
            'limite_credito' => 'nullable|numeric|min:0',
        ]);

        $cliente->nombre = $request->nombre;
        $cliente->cedula = $request->cedula;
        $cliente->direccion = $request->direccion;
        $cliente->telefono = $request->telefono;
        $cliente->email = $request->email;
        if ($request->filled('limite_credito')) {
            $cliente->limite_credito = $request->limite_credito;
        }
        $cliente->save();

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    // Eliminar un cliente
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente.');
    }

    private function generarCodigoUnico(): string
    {
        do {
            $codigo = 'CR' . date('Ym') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (\App\Models\Credito::where('codigo', $codigo)->exists());
        return $codigo;
    }

    // Créditos por cliente: listado y creación con reglas de negocio
    public function creditos(Cliente $cliente)
    {
        $cliente->load(['creditos.detalles', 'creditos.pagos']);

        $creditos = $cliente->creditos()->latest('fecha_credito')->get();
        $abiertos = $cliente->creditos()->whereIn('estado', ['pendiente','activo'])->count();
        $tieneVencido = $cliente->creditos()->where('estado','vencido')->exists();
        $bloqueado = $tieneVencido || $abiertos >= 2;

        // Exposición y límite
        $expuestoCentavos = (int) $cliente->creditos->sum(fn($c) => $c->saldo_pendiente_centavos);
        $limiteCentavos = (int) ($cliente->limite_credito_centavos ?? 0);

        return view('clientes.creditos', compact('cliente','creditos','abiertos','tieneVencido','bloqueado','expuestoCentavos','limiteCentavos'));
    }

    public function storeCredito(Request $request, Cliente $cliente)
    {
        // Reglas: no más de 2 abiertos; no permitir si existe vencido
        $abiertos = $cliente->creditos()->whereIn('estado', ['pendiente','activo'])->count();
        $tieneVencido = $cliente->creditos()->where('estado','vencido')->exists();
        if ($tieneVencido) {
            return back()->with('error', 'El cliente tiene créditos vencidos. No se puede abrir uno nuevo.');
        }
        if ($abiertos >= 2) {
            return back()->with('error', 'Máximo 2 créditos abiertos por cliente.');
        }

        $data = $request->validate([
            'fecha_credito' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_credito',
            'plazo_dias' => 'nullable|integer|min:1',
            'fecha_vencimiento_ext' => 'nullable|date|after_or_equal:fecha_vencimiento',
        ]);

        $credito = new \App\Models\Credito();
        $credito->cliente_id = $cliente->id;
        $credito->codigo = $this->generarCodigoUnico();
        $credito->fecha_credito = $data['fecha_credito'];
        $credito->fecha_vencimiento = $data['fecha_vencimiento'];
        if (!empty($data['plazo_dias'])) $credito->plazo_dias = $data['plazo_dias'];
        if (!empty($data['fecha_vencimiento_ext'])) $credito->fecha_vencimiento_ext = $data['fecha_vencimiento_ext'];
        $credito->estado = 'pendiente';
        $credito->save();

        return redirect()->route('clientes.creditos', $cliente)->with('success', 'Crédito creado. Agrega detalles para calcular el monto.');
    }
}
