@extends('adminlte::page')

@section('title','Editar rol')

@section('content_header')
    <h1 class="m-0 text-dark">Editar rol: {{ $role->name }}</h1>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('roles.update',$role) }}">
    @csrf @method('PUT')
<div class="form-group">
    <label>Permisos</label>
    @foreach($permissions as $group => $perms)
        <h5 class="mt-3">{{ ucfirst($group) }}</h5>
        <div class="row">
            @foreach($perms as $perm)
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               name="permissions[]" id="p{{ $perm->id }}" value="{{ $perm->id }}"
                               @checked($role->permissions->pluck('id')->contains($perm->id))>
                        <label class="form-check-label" for="p{{ $perm->id }}">{{ $perm->name }}</label>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>

        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
            <button class="btn btn-primary"><i class="fas fa-save mr-1"></i> Guardar</button>
        </div>
    </div>
</form>
@stop
