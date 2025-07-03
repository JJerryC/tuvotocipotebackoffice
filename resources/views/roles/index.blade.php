@extends('adminlte::page')

@section('title','Roles')

@section('content_header')
    <h1 class="m-0 text-dark">Roles</h1>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span>Listado de roles</span>
        <a href="{{ route('roles.create') }}" class="btn btn-sm btn-success">
            <i class="fas fa-plus mr-1"></i> Nuevo rol
        </a>
    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>Rol</th>
                <th>Permisos</th>
                <th style="width: 90px">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                    <td>
                        @forelse($role->permissions as $perm)
                            <span class="badge bg-info">{{ $perm->name }}</span>
                        @empty
                            <span class="text-muted">—</span>
                        @endforelse
                    </td>
                    <td>
                        <a href="{{ route('roles.edit',$role) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer">{{ $roles->links() }}</div>
</div>
@stop
