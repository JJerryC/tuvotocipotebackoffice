@extends('adminlte::page')

@section('title', 'Editar Nómina')

@section('content_header')
    <h1 class="m-0 text-dark">Editar Nómina</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-list" title="Editar Nómina" class="col-md-6">
    <form action="{{ route('nominas.update', $nomina) }}" method="POST">
        @csrf
        @method('PUT')

        <x-adminlte-select name="entidad_id" label="Entidad" required>
            <option value="">Seleccione una entidad</option>
            @foreach($entidades as $entidad)
                <option value="{{ $entidad->id }}" {{ $nomina->entidad_id == $entidad->id ? 'selected' : '' }}>
                    {{ $entidad->name }}
                </option>
            @endforeach
        </x-adminlte-select>

        <x-adminlte-input name="name" label="Nombre" value="{{ old('name', $nomina->name) }}" required />
        <x-adminlte-button type="submit" label="Actualizar" theme="primary" icon="fas fa-save" />
        <a href="{{ route('nominas.index') }}" class="btn btn-default">Cancelar</a>
    </form>
</x-adminlte-card>
@stop