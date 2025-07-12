<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\{Role, Permission};

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /* ---------- LISTA ---------- */
    
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        return view('roles.index', compact('roles'));
    }

    /* ---------- NUEVO ---------- */
    public function create()                            // ← DEBE EXISTIR
    {
        return view('roles.create', [
            'permissions' => Permission::orderBy('name')
                                        ->pluck('name', 'id'),
        ]);
    }

    public function store(Request $request)            // ← DEBE EXISTIR
    {
        $data = $request->validate([
            'role_name'      => 'required|string|max:50|unique:roles,name',
            'permissions'    => 'nullable|array',
            'permissions.*'  => 'exists:permissions,id',
        ]);

        $role  = Role::create(['name' => $data['role_name']]);
        $perms = Permission::whereIn('id', $data['permissions'] ?? [])
                            ->get();
        $role->syncPermissions($perms);

        return redirect()->route('roles.index')
                         ->with('success', 'Rol creado y permisos asignados');
    }

    /* ---------- EDITAR ---------- */
    public function edit(Role $role)
    {
        return view('roles.edit', [
            'role'        => $role,
            'permissions' => Permission::orderBy('name')
                                        ->pluck('name', 'id'),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'permissions'   => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $perms = Permission::whereIn('id', $data['permissions'] ?? [])
                            ->get();
        $role->syncPermissions($perms);

        return redirect()->route('roles.index')
                         ->with('success', 'Permisos actualizados');
    }
}
