@extends('adminlte::page')

@section('title', 'Editar Partido')

@section('content_header')
    <h1 class="m-0 text-dark">Editar Partido</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-flag" title="Editar Partido" class="col-md-6">
    <form action="{{ route('parties.update', $party) }}" method="POST">
        @csrf
        @method('PUT')

        <x-adminlte-input name="name" label="Nombre" value="{{ old('name', $party->name) }}" required />

        {{-- Botones alineados a izquierda y derecha --}}
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('parties.index') }}" class="btn btn-default">Cancelar</a>

            <x-adminlte-button type="submit" label="Actualizar" theme="primary" icon="fas fa-save" />
        </div>
    </form>
</x-adminlte-card>
@stop