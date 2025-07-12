@extends('adminlte::page')

@section('title', 'Editar Entidad')

@section('content_header')
    <h1 class="m-0 text-dark">Editar Entidad</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-building" title="Editar Entidad" class="col-md-6">
    <form action="{{ route('entidades.update', $entidad->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Campo: Nombre --}}
        <x-adminlte-input name="name" label="Nombre de la Entidad" value="{{ old('name', $entidad->name) }}" required />

        {{-- Campo: Partido --}}
        <x-adminlte-select name="party_id" label="Partido" required>
            <option value="">Seleccione…</option>
            @foreach($parties as $id => $name)
                <option value="{{ $id }}" {{ old('party_id', $entidad->party_id) == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </x-adminlte-select>

        {{-- Botones --}}
        <x-adminlte-button type="submit" label="Actualizar" theme="primary" icon="fas fa-save" />
        <a href="{{ route('entidades.index') }}" class="btn btn-default">Cancelar</a>
    </form>
</x-adminlte-card>
@stop