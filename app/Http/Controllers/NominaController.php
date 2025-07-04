<?php

namespace App\Http\Controllers;

use App\Models\Nomina;
use App\Models\Entidad;
use Illuminate\Http\Request;

class NominaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nominas = Nomina::with('entidad')->get();
        return view('nominas.index', compact('nominas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $entidades = Entidad::all();
        return view('nominas.create', compact('entidades'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'entidad_id' => 'required|exists:entidades,id',
            'name' => 'required|string|max:255',
        ]);

        Nomina::create($request->all());

        return redirect()->route('nominas.index')->with('success', 'Nómina creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $nomina = Nomina::with('entidad')->findOrFail($id);
        return view('nominas.show', compact('nomina'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $nomina = Nomina::findOrFail($id);
        $entidades = Entidad::all();
        return view('nominas.edit', compact('nomina', 'entidades'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'entidad_id' => 'required|exists:entidades,id',
            'name' => 'required|string|max:255',
        ]);

        $nomina = Nomina::findOrFail($id);
        $nomina->update($request->all());

        return redirect()->route('nominas.index')->with('success', 'Nómina actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $nomina = Nomina::findOrFail($id);
        $nomina->delete();

        return redirect()->route('nominas.index')->with('success', 'Nómina eliminada correctamente');
    }
}
