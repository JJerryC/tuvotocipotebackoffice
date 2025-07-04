@extends('adminlte::page')

@section('title', 'Crear Entidad')

@section('content_header')
    <h1>Crear Entidad</h1>
@stop

@section('content')
<form action="{{ route('entidades.store') }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="name">Nombre de la Entidad</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="party_id">Partido</label>
        <select name="party_id" class="form-control" required>
            <option value="">Seleccione…</option>
            @foreach($parties as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
</form>
@stop