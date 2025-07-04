@extends('adminlte::page')

@section('title', 'Editar Entidad')

@section('content_header')
    <h1>Editar Entidad</h1>
@stop

@section('content')
<form action="{{ route('entidades.update', $entidad->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="name">Nombre de la Entidad</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $entidad->name) }}" required>
    </div>

    <div class="form-group">
        <label for="party_id">Partido</label>
        <select name="party_id" class="form-control" required>
            <option value="">Seleccione…</option>
            @foreach($parties as $id => $name)
                <option value="{{ $id }}" {{ $id == $entidad->party_id ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Actualizar</button>
</form>
@stop