@extends('adminlte::page')

@section('title', 'Editar Candidato')

@section('content_header')
    <h1 class="m-0 text-dark">Editar Candidato</h1>
@stop

@section('content')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Datos del candidato</h3>
    </div>

    <form action="{{ route('candidates.update', $candidate) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">

                {{-- Partido --}}
                <div class="form-group col-md-6">
                    <label for="party_id"><i class="fas fa-flag mr-1"></i>Partido</label>
                    <select name="party_id" id="party_id" class="form-control">
                        <option value="">Seleccione…</option>
                        @foreach($parties as $id => $name)
                            <option value="{{ $id }}" @selected(old('party_id', $candidate->party_id) == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Nómina --}}
                <div class="form-group col-md-6">
                    <label for="nomina_id"><i class="fas fa-users mr-1"></i>Nómina</label>
                    <select name="nomina_id" id="nomina_id" class="form-control">
                        <option value="">Seleccione…</option>
                        @foreach($nominas as $id => $name)
                            <option value="{{ $id }}" @selected(old('nomina_id', $candidate->nomina_id) == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Departamento --}}
                <div class="form-group col-md-6">
                    <label for="departamento_id"><i class="fas fa-map mr-1"></i>Departamento</label>
                    <select name="departamento_id" id="departamento_id" class="form-control">
                        <option value="">Seleccione…</option>
                        @foreach($departamentos as $id => $name)
                            <option value="{{ $id }}" @selected(old('departamento_id', $candidate->departamento_id) == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Municipio --}}
                <div class="form-group col-md-6">
                    <label for="municipio_id"><i class="fas fa-map-marker-alt mr-1"></i>Municipio</label>
                    <select name="municipio_id" id="municipio_id" class="form-control">
                        <option value="">Seleccione…</option>
                        @foreach($municipios as $id => $name)
                            <option value="{{ $id }}" @selected(old('municipio_id', $candidate->municipio_id) == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Cargo --}}
                <div class="form-group col-md-6">
                    <label for="cargo_id"><i class="fas fa-briefcase mr-1"></i>Cargo</label>
                    <select name="cargo_id" id="cargo_id" class="form-control">
                        <option value="">Seleccione…</option>
                        @foreach($cargos as $id => $name)
                            <option value="{{ $id }}" @selected(old('cargo_id', $candidate->cargo_id) == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Sexo --}}
                <div class="form-group col-md-6">
                    <label for="sexo_id"><i class="fas fa-venus-mars mr-1"></i>Sexo</label>
                    <select name="sexo_id" id="sexo_id" class="form-control">
                        <option value="">Seleccione…</option>
                        @foreach($sexos as $id => $desc)
                            <option value="{{ $id }}" @selected(old('sexo_id', $candidate->sexo_id) == $id)>{{ $desc }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Posición --}}
                <div class="form-group col-md-3">
                    <label for="posicion"><i class="fas fa-sort-numeric-down mr-1"></i>Posición</label>
                    <input type="number" min="1" name="posicion" id="posicion"
                           value="{{ old('posicion', $candidate->posicion) }}" class="form-control">
                </div>

                {{-- Número de identidad --}}
                <div class="form-group col-md-9">
                    <label for="numero_identidad"><i class="fas fa-id-card mr-1"></i>Número de Identidad</label>
                    <input type="text" name="numero_identidad" id="numero_identidad" maxlength="25"
                           value="{{ old('numero_identidad', $candidate->numero_identidad) }}" class="form-control">
                </div>

                {{-- Nombres --}}
                <div class="form-group col-md-6">
                    <label for="primer_nombre">Primer Nombre</label>
                    <input type="text" name="primer_nombre" id="primer_nombre"
                           value="{{ old('primer_nombre', $candidate->primer_nombre) }}" class="form-control">
                </div>

                <div class="form-group col-md-6">
                    <label for="segundo_nombre">Segundo Nombre</label>
                    <input type="text" name="segundo_nombre" id="segundo_nombre"
                           value="{{ old('segundo_nombre', $candidate->segundo_nombre) }}" class="form-control">
                </div>

                {{-- Apellidos --}}
                <div class="form-group col-md-6">
                    <label for="primer_apellido">Primer Apellido</label>
                    <input type="text" name="primer_apellido" id="primer_apellido"
                           value="{{ old('primer_apellido', $candidate->primer_apellido) }}" class="form-control">
                </div>

                <div class="form-group col-md-6">
                    <label for="segundo_apellido">Segundo Apellido</label>
                    <input type="text" name="segundo_apellido" id="segundo_apellido"
                           value="{{ old('segundo_apellido', $candidate->segundo_apellido) }}" class="form-control">
                </div>

            </div> {{-- /.row --}}
        </div> {{-- /.card-body --}}

        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('candidates.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Actualizar
            </button>
        </div>
    </form>
</div>
@stop