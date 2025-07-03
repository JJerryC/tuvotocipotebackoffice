<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\{Role, Permission};
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        // Solo administradores pueden entrar a este módulo
        $this->middleware(['auth', 'role:admin']);
    }

    /* ---------- LISTA ---------- */
    public function index()
    {
        $users = User::with('roles')->paginate(15);
        return view('users.index', compact('users'));
    }

    /* ---------- FORMULARIO NUEVO ---------- */
    public function create()
    {
        return view('users.create', [
            'roles'       => Role::pluck('name', 'id'),
            'permissions' => Permission::pluck('name', 'id'),
        ]);
    }

    /* ---------- GUARDAR USUARIO NUEVO ---------- */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:6|confirmed',
            'roles'                 => 'array',
            'roles.*'               => 'exists:roles,id',
            'permissions'           => 'array',
            'permissions.*'         => 'exists:permissions,id',
        ]);

        /** @var User $user */
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Convierte IDs a modelos antes de sincronizar
        $roles  = Role::whereIn('id', $data['roles'] ?? [])->get();
        $perms  = Permission::whereIn('id', $data['permissions'] ?? [])->get();

        $user->syncRoles($roles);
        $user->syncPermissions($perms);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario creado correctamente');
    }

    /* ---------- FORMULARIO EDICIÓN ---------- */
    public function edit(User $user)
    {
        return view('users.edit', [
            'user'        => $user->load('roles', 'permissions'),
            'roles'       => Role::pluck('name', 'id'),
            'permissions' => Permission::pluck('name', 'id'),
        ]);
    }

    /* ---------- ACTUALIZAR USUARIO ---------- */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'roles'         => 'array',
            'roles.*'       => 'exists:roles,id',
            'permissions'   => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $roles = Role::whereIn('id', $data['roles'] ?? [])->get();
        $perms = Permission::whereIn('id', $data['permissions'] ?? [])->get();

        $user->syncRoles($roles);
        $user->syncPermissions($perms);

        return redirect()
            ->route('users.index')
            ->with('success', 'Roles y permisos actualizados');
    }
}
