<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    // Mostrar lista de clientes
    public function index()
    {
        $clientes = Cliente::all();
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
}
