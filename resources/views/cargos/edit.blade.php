@extends('adminlte::page')

@section('title', 'Editar Cargo')

@section('content_header')
    <h1 class="m-0 text-dark">Editar Cargo</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-briefcase" title="Editar Cargo" class="col-md-6">
    <form action="{{ route('cargos.update', $cargo) }}" method="POST">
        @csrf
        @method('PUT')
        <x-adminlte-input name="name" label="Nombre" value="{{ old('name', $cargo->name) }}" required />
        <x-adminlte-button type="submit" label="Actualizar" theme="primary" icon="fas fa-save" />
        <a href="{{ route('cargos.index') }}" class="btn btn-default">Cancelar</a>
    </form>
</x-adminlte-card>
@stop