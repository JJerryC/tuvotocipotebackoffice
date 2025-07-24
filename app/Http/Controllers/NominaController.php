<?php

namespace App\Http\Controllers;

use App\Models\Nomina;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class NominaController extends Controller
{
    public function index()
    {
        $nominas = Nomina::orderBy('name')->get();
        return view('nominas.index', compact('nominas'));
    }

    public function create()
    {
        return view('nominas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Convertir a mayúsculas
        $data['name'] = mb_strtoupper($data['name'], 'UTF-8');

        Nomina::create($data);

        return redirect()->route('nominas.index')->with('success', 'Nómina creada correctamente');
    }

    public function show(string $id)
    {
        $nomina = Nomina::findOrFail($id);
        return view('nominas.show', compact('nomina'));
    }

    public function edit(string $id)
    {
        $nomina = Nomina::findOrFail($id);
        return view('nominas.edit', compact('nomina'));
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Convertir a mayúsculas
        $data['name'] = mb_strtoupper($data['name'], 'UTF-8');

        $nomina = Nomina::findOrFail($id);
        $nomina->update($data);

        return redirect()->route('nominas.index')->with('success', 'Nómina actualizada correctamente');
    }

    public function destroy(string $id)
    {
        $nomina = Nomina::findOrFail($id);

        try {
            $nomina->delete();
            return redirect()->route('nominas.index')->with('success', 'Nómina eliminada correctamente');
        } catch (QueryException $e) {
            return redirect()->route('nominas.index')->with('error', 'No se puede eliminar esta nómina porque tiene registros asociados.');
        }
    }
}
