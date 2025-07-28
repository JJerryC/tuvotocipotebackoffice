<?php

namespace App\Http\Controllers;

use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:manage candidates']); // O el permiso que corresponda
    }

    public function index()
    {
        $parties = Party::orderBy('name')->get();
        return view('parties.index', compact('parties'));
    }

    public function create()
    {
        return view('parties.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:parties,name',
            'foto_partido' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data['name'] = mb_strtoupper($data['name'], 'UTF-8');

        if ($request->hasFile('foto_partido')) {
            // Guarda la imagen en storage/app/public/parties
            $path = $request->file('foto_partido')->store('parties', 'public');
            $data['foto_partido'] = $path;
        }

        Party::create($data);

        return redirect()->route('parties.index')->with('success', 'Partido creado correctamente');
    }


    public function show(Party $party)
    {
        return view('parties.show', compact('party'));
    }

    public function edit(Party $party)
    {
        return view('parties.edit', compact('party'));
    }

    public function update(Request $request, Party $party)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:parties,name,' . $party->id,
            'foto_partido' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data['name'] = mb_strtoupper($data['name'], 'UTF-8');

        if ($request->hasFile('foto_partido')) {
            // Eliminar imagen anterior si existe
            if ($party->foto_partido && Storage::disk('public')->exists($party->foto_partido)) {
                Storage::disk('public')->delete($party->foto_partido);
            }

            // Guardar la nueva imagen
            $path = $request->file('foto_partido')->store('parties', 'public');
            $data['foto_partido'] = $path;
        }

        $party->update($data);

        return redirect()->route('parties.index')->with('success', 'Partido actualizado correctamente');
    }

    public function destroy(Party $party)
    {
        try {
            if ($party->foto_partido && Storage::disk('public')->exists($party->foto_partido)) {
                Storage::disk('public')->delete($party->foto_partido);
            }
            $party->delete();

            return redirect()->route('parties.index')->with('success', 'Partido eliminado correctamente');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('parties.index')->with('error', 'No se puede eliminar este partido porque tiene registros asociados.');
        }
    }
}