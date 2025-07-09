{{-- resources/views/candidates/create.blade.php --}}
@extends('adminlte::page')

@section('title', 'Nuevo Candidato')

@section('content_header')
    <h1 class="m-0 text-dark">Crear Candidato</h1>
@stop

@section('content')
<div class="card card-primary">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title"><i class="fas fa-user-plus mr-1"></i>Datos del candidato</h3>
        {{-- Botón Guardar en el header para mejor visibilidad --}}
        <button type="submit" form="candidate-form" class="btn btn-sm btn-success">
            <i class="fas fa-save mr-1"></i>Guardar
        </button>
    </div>

    <form id="candidate-form" action="{{ route('candidates.store') }}" method="POST" autocomplete="off">
        @csrf
        <div class="card-body">
            <div class="row">
                {{-- Partido --}}
                <div class="col-md-6 form-group">
                    <label for="party_id"><i class="fas fa-flag mr-1"></i>Partido</label>
                    <select name="party_id" id="party_id"
                            class="form-control @error('party_id') is-invalid @enderror">
                        <option value="">Seleccione…</option>
                        @foreach($parties as $id => $name)
                            <option value="{{ $id }}" @selected(old('party_id') == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('party_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Entidad (cargada tras elegir partido) --}}
                <div class="col-md-6 form-group">
                    <label for="entidad_id"><i class="fas fa-building mr-1"></i>Entidad</label>
                    <select name="entidad_id" id="entidad_id"
                            class="form-control @error('entidad_id') is-invalid @enderror" disabled>
                        <option value="">Seleccione…</option>
                    </select>
                    @error('entidad_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Nómina --}}
                <div class="col-md-6 form-group">
                    <label for="nomina_id"><i class="fas fa-users mr-1"></i>Nómina</label>
                    <select name="nomina_id" id="nomina_id"
                            class="form-control @error('nomina_id') is-invalid @enderror">
                        <option value="">Seleccione…</option>
                        @foreach($nominas as $id => $name)
                            <option value="{{ $id }}" @selected(old('nomina_id') == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('nomina_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Departamento --}}
                <div class="col-md-6 form-group">
                    <label for="departamento_id"><i class="fas fa-map mr-1"></i>Departamento</label>
                    <select name="departamento_id" id="departamento_id"
                            class="form-control @error('departamento_id') is-invalid @enderror">
                        <option value="">Seleccione…</option>
                        @foreach($departamentos as $id => $name)
                            <option value="{{ $id }}" @selected(old('departamento_id') == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('departamento_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Municipio --}}
                <div class="col-md-6 form-group">
                    <label for="municipio_id"><i class="fas fa-map-marker-alt mr-1"></i>Municipio</label>
                    <select name="municipio_id" id="municipio_id"
                            class="form-control @error('municipio_id') is-invalid @enderror" disabled>
                        <option value="">Seleccione…</option>
                    </select>
                    @error('municipio_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Cargo --}}
                <div class="col-md-6 form-group">
                    <label for="cargo_id"><i class="fas fa-briefcase mr-1"></i>Cargo</label>
                    <select name="cargo_id" id="cargo_id"
                            class="form-control @error('cargo_id') is-invalid @enderror">
                        <option value="">Seleccione…</option>
                        @foreach($cargos as $id => $name)
                            <option value="{{ $id }}" @selected(old('cargo_id') == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('cargo_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Sexo --}}
                <div class="col-md-6 form-group">
                    <label for="sexo_id"><i class="fas fa-venus-mars mr-1"></i>Sexo</label>
                    <select name="sexo_id" id="sexo_id"
                            class="form-control @error('sexo_id') is-invalid @enderror">
                        <option value="">Seleccione…</option>
                        @foreach($sexos as $id => $desc)
                            <option value="{{ $id }}" @selected(old('sexo_id') == $id)>{{ $desc }}</option>
                        @endforeach
                    </select>
                    @error('sexo_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Posición --}}
                <div class="col-md-3 form-group">
                    <label for="posicion"><i class="fas fa-sort-numeric-down mr-1"></i>Posición</label>
                    <input type="number" name="posicion" id="posicion" min="1" step="1"
                           class="form-control @error('posicion') is-invalid @enderror"
                           value="{{ old('posicion') }}">
                    @error('posicion')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Número de Identidad --}}
                <div class="col-md-9 form-group">
                    <label for="numero_identidad"><i class="fas fa-id-card mr-1"></i>Número de Identidad</label>
                    <input type="text" name="numero_identidad" id="numero_identidad" maxlength="25"
                           class="form-control @error('numero_identidad') is-invalid @enderror"
                           value="{{ old('numero_identidad') }}">
                    @error('numero_identidad')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Primer Nombre --}}
                <div class="col-md-6 form-group">
                    <label for="primer_nombre">Primer Nombre</label>
                    <input type="text" name="primer_nombre" id="primer_nombre"
                           class="form-control @error('primer_nombre') is-invalid @enderror"
                           value="{{ old('primer_nombre') }}">
                    @error('primer_nombre')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Segundo Nombre --}}
                <div class="col-md-6 form-group">
                    <label for="segundo_nombre">Segundo Nombre</label>
                    <input type="text" name="segundo_nombre" id="segundo_nombre"
                           class="form-control @error('segundo_nombre') is-invalid @enderror"
                           value="{{ old('segundo_nombre') }}">
                    @error('segundo_nombre')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Primer Apellido --}}
                <div class="col-md-6 form-group">
                    <label for="primer_apellido">Primer Apellido</label>
                    <input type="text" name="primer_apellido" id="primer_apellido"
                           class="form-control @error('primer_apellido') is-invalid @enderror"
                           value="{{ old('primer_apellido') }}">
                    @error('primer_apellido')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Segundo Apellido --}}
                <div class="col-md-6 form-group">
                    <label for="segundo_apellido">Segundo Apellido</label>
                    <input type="text" name="segundo_apellido" id="segundo_apellido"
                           class="form-control @error('segundo_apellido') is-invalid @enderror"
                           value="{{ old('segundo_apellido') }}">
                    @error('segundo_apellido')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <div class="card-footer">
            {{-- Botón Cancelar solo en footer --}}
            <a href="{{ route('candidates.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Cancelar
            </a>
        </div>
    </form>
</div>
@stop

@push('js')
<script>
$(function(){
    // Inicialmente deshabilitados
    $('#entidad_id, #municipio_id').prop('disabled', true);

    // Al cambiar partido, cargamos entidades
    $('#party_id').on('change', function(){
        var pid = $(this).val();
        if (!pid) {
            $('#entidad_id')
                .html('<option value="">Seleccione…</option>')
                .prop('disabled', true);
        } else {
            $.getJSON('/api/partidos/'+pid+'/entidades', function(data){
                var opts = '<option value="">Seleccione…</option>';
                $.each(data, function(_, e){
                    opts += '<option value="'+e.id+'">'+e.name+'</option>';
                });
                $('#entidad_id').html(opts).prop('disabled', false);
            });
        }
    });

    // Al cambiar departamento, cargamos municipios
    $('#departamento_id').on('change', function(){
        var did = $(this).val();
        if (!did) {
            $('#municipio_id')
                .html('<option value="">Seleccione…</option>')
                .prop('disabled', true);
        } else {
            $.getJSON('/api/departamentos/'+did+'/municipios', function(data){
                var opts = '<option value="">Seleccione…</option>';
                $.each(data, function(_, m){
                    opts += '<option value="'+m.id+'">'+m.name+'</option>';
                });
                $('#municipio_id').html(opts).prop('disabled', false);
            });
        }
    });
});
</script>
@endpush
