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
    <div class="card card-primary">
        <div class="card-header"><h3 class="card-title">Permisos</h3></div>

        <div class="card-body">
            <div class="row">
                @foreach($permissions as $id => $name)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                   name="permissions[]" value="{{ $id }}" id="p{{ $id }}"
                                   @checked($role->permissions->pluck('id')->contains($id))>
                            <label class="form-check-label" for="p{{ $id }}">{{ $name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
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
