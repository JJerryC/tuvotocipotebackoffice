@extends('adminlte::page')

@section('title', 'Crear Partido')

@section('content_header')
    <h1 class="m-0 text-dark">Crear Partido</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-flag" title="Nuevo Partido" class="col-md-6">
    <form action="{{ route('parties.store') }}" method="POST">
        @csrf
        <x-adminlte-input name="name" label="Nombre" placeholder="Nombre del partido" required />

        {{-- Botones alineados a izquierda y derecha --}}
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('parties.index') }}" class="btn btn-default">Cancelar</a>

            <x-adminlte-button type="submit" label="Guardar" theme="success" icon="fas fa-save" />
        </div>
    </form>
</x-adminlte-card>
@stop