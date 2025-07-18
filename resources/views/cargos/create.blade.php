@extends('adminlte::page')

@section('title', 'Crear Cargo')

@section('content_header')
    <h1 class="m-0 text-dark">Crear Cargo</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-briefcase" title="Nuevo Cargo" class="col-md-6">
    <form action="{{ route('cargos.store') }}" method="POST">
        @csrf
        <x-adminlte-input name="name" label="Nombre" placeholder="Nombre del cargo" required />

        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('cargos.index') }}" class="btn btn-default">Cancelar</a>
            <x-adminlte-button type="submit" label="Guardar" theme="success" icon="fas fa-save" />
        </div>
    </form>
</x-adminlte-card>
@stop