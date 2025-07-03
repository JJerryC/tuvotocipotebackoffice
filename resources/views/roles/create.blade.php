@extends('adminlte::page')

@section('title','Crear rol')

@section('content_header')
    <h1 class="m-0 text-dark">Nuevo rol</h1>
@stop

@section('content')
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('roles.store') }}">
    @csrf
    <div class="card card-primary">
        <div class="card-header"><h3 class="card-title">Datos</h3></div>

        <div class="card-body">
            {{-- nombre --}}
            <div class="form-group">
                <label for="role_name">Nombre del rol</label>
                <input type="text" id="role_name" name="role_name"
                       class="form-control" value="{{ old('role_name') }}"
                       placeholder="ej: editor">
            </div>

            {{-- permisos --}}
            <div class="form-group">
                <label>Permisos</label>
                <div class="row">
                    @foreach($permissions as $id => $name)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="permissions[]" id="p{{ $id }}" value="{{ $id }}">
                                <label class="form-check-label" for="p{{ $id }}">{{ $name }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary mr-2">Cancelar</a>
            <button class="btn btn-primary"><i class="fas fa-save mr-1"></i>Guardar</button>
        </div>
    </div>
</form>
@stop
