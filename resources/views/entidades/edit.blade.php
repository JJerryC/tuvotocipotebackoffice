@extends('adminlte::page')

@section('title', 'Editar Movimiento')

@section('content_header')
    <h1 class="m-0 text-dark">Editar Movimiento</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-building" title="Editar Movimiento" class="col-md-6">
    <form action="{{ route('entidades.update', $entidad->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Campo: Nombre --}}
        <x-adminlte-input name="name" label="Nombre del Movimiento" value="{{ old('name', $entidad->name) }}" required />

        {{-- Campo: Partido --}}
        <x-adminlte-select name="party_id" label="Partido" required>
            <option value="">Seleccione…</option>
            @foreach($parties as $id => $name)
                <option value="{{ $id }}" {{ old('party_id', $entidad->party_id) == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </x-adminlte-select>

        {{-- Botones alineados a izquierda y derecha --}}
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('entidades.index') }}" class="btn btn-default">Cancelar</a>

            <x-adminlte-button type="submit" label="Actualizar" theme="primary" icon="fas fa-save" />
        </div>
    </form>
</x-adminlte-card>
@stop