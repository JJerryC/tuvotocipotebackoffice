@extends('adminlte::page')

@section('title','Usuarios')

@section('content_header')
    <h1 class="m-0 text-dark">Usuarios</h1>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span>Listado</span>

        <a href="{{ route('users.create') }}" class="btn btn-sm btn-success">
            <i class="fas fa-user-plus mr-1"></i> Nuevo
        </a>
    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Roles</th>
                <th style="width: 100px">Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $u)
                <tr>
                    <td>{{ $u->id }}</td>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>
                        @foreach($u->roles as $role)
                            <span class="badge bg-info">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-user-cog"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer">{{ $users->links() }}</div>
</div>
@stop
