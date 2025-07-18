@extends('adminlte::page')

@section('title', 'Crear Entidad')

@section('content_header')
    <h1 class="m-0 text-dark">Crear Entidad</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-building" title="Nueva Entidad" class="col-md-6">
    <form action="{{ route('entidades.store') }}" method="POST">
        @csrf

        <x-adminlte-input name="name" label="Nombre de la Entidad" placeholder="Ingrese el nombre" required />

        <x-adminlte-select name="party_id" label="Partido" required>
            <option value="">Seleccione…</option>
            @foreach($parties as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </x-adminlte-select>

        {{-- Botones alineados a izquierda y derecha --}}
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('entidades.index') }}" class="btn btn-default">Cancelar</a>

            <x-adminlte-button type="submit" label="Guardar" theme="success" icon="fas fa-save" />
        </div>
    </form>
</x-adminlte-card>
@stop