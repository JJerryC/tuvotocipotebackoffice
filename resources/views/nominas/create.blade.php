@extends('adminlte::page')

@section('title', 'Crear Nómina')

@section('content_header')
    <h1 class="m-0 text-dark">Crear Nómina</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-list" title="Nueva Nómina" class="col-md-6">
    <form action="{{ route('nominas.store') }}" method="POST">
        @csrf
        <x-adminlte-select name="entidad_id" label="Entidad" required>
            <option value="">Seleccione una entidad</option>
            @foreach($entidades as $entidad)
                <option value="{{ $entidad->id }}">{{ $entidad->name }}</option>
            @endforeach
        </x-adminlte-select>

        <x-adminlte-input name="name" label="Nombre" placeholder="Nombre de la nómina" required />
        <div class="d-flex justify-content-between">
            <a href="{{ route('nominas.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left mr-1"></i> Cancelar
            </a>

            <x-adminlte-button type="submit" label="Guardar" theme="success" icon="fas fa-save" />
        </div>
    </form>
</x-adminlte-card>
@stop