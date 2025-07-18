@extends('adminlte::page')

@section('title', 'Editar Candidato')

@section('content_header')
    <h1 class="m-0 text-dark">Editar Candidato</h1>
@stop

@section('content')
<div class="card card-primary">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Datos del candidato</h3>
    </div>

    <form id="candidate-form" action="{{ route('candidates.update', $candidate) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">

                {{-- Independiente --}}
                <div class="col-md-12 form-group d-flex align-items-center mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="independiente" id="independiente" class="form-check-input"
                               value="1" @checked(old('independiente', $candidate->independiente))>
                        <label for="independiente" class="form-check-label font-weight-bold">Candidato Independiente</label>
                    </div>
                </div>

                {{-- Partido --}}
                <div class="col-md-6 form-group">
                    <label for="party_id"><i class="fas fa-flag mr-1"></i>Partido</label>
                    <select name="party_id" id="party_id" class="form-control @error('party_id') is-invalid @enderror" 
                        {{ old('independiente', $candidate->independiente) ? 'disabled' : '' }}>
                        <option value="">Seleccione…</option>
                        @foreach($parties as $id => $name)
                            <option value="{{ $id }}" @selected(old('party_id', $candidate->party_id) == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('party_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Entidad --}}
                <div class="col-md-6 form-group">
                    <label for="entidad_id"><i class="fas fa-building mr-1"></i>Entidad</label>
                    <select name="entidad_id" id="entidad_id" class="form-control @error('entidad_id') is-invalid @enderror"
                        {{ old('independiente', $candidate->independiente) ? 'disabled' : '' }}>
                        @if($entidades)
                            <option value="">Seleccione…</option>
                            @foreach($entidades as $id => $name)
                                <option value="{{ $id }}" @selected(old('entidad_id', $candidate->entidad_id) == $id)>{{ $name }}</option>
                            @endforeach
                        @else
                            <option value="">Seleccione…</option>
                        @endif
                    </select>
                    @error('entidad_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Nómina --}}
                <div class="col-md-6 form-group">
                    <label for="nomina_id"><i class="fas fa-users mr-1"></i>Nómina</label>
                    <select name="nomina_id" id="nomina_id" class="form-control @error('nomina_id') is-invalid @enderror">
                        <option value="">Seleccione…</option>
                        @foreach($nominas as $id => $name)
                            <option value="{{ $id }}" @selected(old('nomina_id', $candidate->nomina_id) == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('nomina_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Departamento --}}
                <div class="col-md-6 form-group">
                    <label for="departamento_id"><i class="fas fa-map mr-1"></i>Departamento</label>
                    <select name="departamento_id" id="departamento_id" class="form-control @error('departamento_id') is-invalid @enderror">
                        <option value="">Seleccione…</option>
                        @foreach($departamentos as $id => $name)
                            <option value="{{ $id }}" @selected(old('departamento_id', $candidate->departamento_id) == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('departamento_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Municipio --}}
                <div class="col-md-6 form-group">
                    <label for="municipio_id"><i class="fas fa-map-marker-alt mr-1"></i>Municipio</label>
                    <select name="municipio_id" id="municipio_id" class="form-control @error('municipio_id') is-invalid @enderror" 
                        {{ old('municipio_id', $candidate->municipio_id) ? '' : 'disabled' }}>
                        @if($municipios)
                            <option value="">Seleccione…</option>
                            @foreach($municipios as $id => $name)
                                <option value="{{ $id }}" @selected(old('municipio_id', $candidate->municipio_id) == $id)>{{ $name }}</option>
                            @endforeach
                        @else
                            <option value="">Seleccione…</option>
                        @endif
                    </select>
                    @error('municipio_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Cargo --}}
                <div class="col-md-6 form-group">
                    <label for="cargo_id"><i class="fas fa-briefcase mr-1"></i>Cargo</label>
                    <select name="cargo_id" id="cargo_id" class="form-control @error('cargo_id') is-invalid @enderror">
                        <option value="">Seleccione…</option>
                        @foreach($cargos as $id => $name)
                            <option value="{{ $id }}" @selected(old('cargo_id', $candidate->cargo_id) == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('cargo_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Sexo --}}
                <div class="col-md-6 form-group">
                    <label for="sexo_id"><i class="fas fa-venus-mars mr-1"></i>Sexo</label>
                    <select name="sexo_id" id="sexo_id" class="form-control @error('sexo_id') is-invalid @enderror">
                        <option value="">Seleccione…</option>
                        @foreach($sexos as $id => $desc)
                            <option value="{{ $id }}" @selected(old('sexo_id', $candidate->sexo_id) == $id)>{{ $desc }}</option>
                        @endforeach
                    </select>
                    @error('sexo_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Posición --}}
                <div class="col-md-6 form-group">
                    <label for="posicion"><i class="fas fa-sort-numeric-down mr-1"></i>Posición</label>
                    <input type="number" name="posicion" id="posicion" step="1"
                           class="form-control @error('posicion') is-invalid @enderror"
                           value="{{ old('posicion', $candidate->posicion) }}">
                    @error('posicion')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Número de Identidad --}}
                <div class="col-md-12 form-group">
                    <label for="numero_identidad"><i class="fas fa-id-card mr-1"></i>Número de Identidad</label>
                    <input type="text" name="numero_identidad" id="numero_identidad" maxlength="25"
                           class="form-control @error('numero_identidad') is-invalid @enderror"
                           value="{{ old('numero_identidad', $candidate->numero_identidad) }}">
                    @error('numero_identidad')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Primer Nombre --}}
                <div class="col-md-6 form-group">
                    <label for="primer_nombre">Primer Nombre</label>
                    <input type="text" name="primer_nombre" id="primer_nombre"
                           class="form-control @error('primer_nombre') is-invalid @enderror"
                           value="{{ old('primer_nombre', $candidate->primer_nombre) }}">
                    @error('primer_nombre')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Segundo Nombre --}}
                <div class="col-md-6 form-group">
                    <label for="segundo_nombre">Segundo Nombre</label>
                    <input type="text" name="segundo_nombre" id="segundo_nombre"
                           class="form-control @error('segundo_nombre') is-invalid @enderror"
                           value="{{ old('segundo_nombre', $candidate->segundo_nombre) }}">
                    @error('segundo_nombre')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Primer Apellido --}}
                <div class="col-md-6 form-group">
                    <label for="primer_apellido">Primer Apellido</label>
                    <input type="text" name="primer_apellido" id="primer_apellido"
                           class="form-control @error('primer_apellido') is-invalid @enderror"
                           value="{{ old('primer_apellido', $candidate->primer_apellido) }}">
                    @error('primer_apellido')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Segundo Apellido --}}
                <div class="col-md-6 form-group">
                    <label for="segundo_apellido">Segundo Apellido</label>
                    <input type="text" name="segundo_apellido" id="segundo_apellido"
                           class="form-control @error('segundo_apellido') is-invalid @enderror"
                           value="{{ old('segundo_apellido', $candidate->segundo_apellido) }}">
                    @error('segundo_apellido')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Fotografía --}}
                <div class="col-md-8 form-group d-flex align-items-center">

                    {{-- Input archivo con label --}}
                    <div class="custom-file flex-grow-1 mr-3">
                        <label for="fotografia"><i class="fas fa-camera mr-1"></i>Fotografía</label>
                        <input type="file" name="fotografia" id="fotografia" class="custom-file-input @error('fotografia') is-invalid @enderror" accept="image/*">
                        <label class="custom-file-label" for="fotografia">Seleccionar archivo</label>

                        @error('fotografia')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                    </div>

                    {{-- Checkbox reelección --}}
                    <div class="form-check" style="white-space: nowrap;">
                        <input type="checkbox" name="reeleccion" id="reeleccion" class="form-check-input"
                            value="1" @checked(old('reeleccion', $candidate->reeleccion))>
                        <label for="reeleccion" class="form-check-label mb-0">Candidato a reelección</label>
                    </div>
                </div>

                {{-- Preview de foto --}}
                <div class="col-md-6 form-group">
                    @if($candidate->fotografia)
                        <small class="form-text text-muted" id="preview-filename">Foto actual: {{ $candidate->fotografia_original ?? basename($candidate->fotografia) }}</small>
                        <div id="preview-container">
                            <img src="{{ asset('storage/' . $candidate->fotografia) }}" alt="Foto del candidato" id="preview-image" style="max-width: 200px; max-height: 200px; border-radius: 5px;">
                        </div>
                    @else
                        <div id="preview-container" style="display:none;">
                            <img src="" alt="Vista previa" id="preview-image" style="max-width: 200px; max-height: 200px; border-radius: 5px;">
                        </div>
                    @endif
                </div>

                {{-- Planes y propuestas --}}
                <div class="col-md-12 form-group">
                    <label for="propuestas"><i class="fas fa-file-alt mr-1"></i>Planes y propuestas</label>
                    <textarea name="propuestas" id="propuestas" rows="4"
                              class="form-control @error('propuestas') is-invalid @enderror">{{ old('propuestas', $candidate->propuestas) }}</textarea>
                    @error('propuestas')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

            </div>
        </div>

        <div class="card-footer d-flex align-items-center">
            <a href="{{ route('candidates.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Cancelar
            </a>

            <button type="submit" form="candidate-form" class="btn btn-success" style="margin-left: auto;">
                <i class="fas fa-save mr-1"></i>Actualizar
            </button>
        </div>
    </form>
</div>
@stop

@push('js')
<script>
$(function() {
    const independienteCheckbox = $('#independiente');
    const partySelect = $('#party_id');
    const entidadSelect = $('#entidad_id');
    const departamentoSelect = $('#departamento_id');
    const municipioSelect = $('#municipio_id');
    const fotografiaInput = $('#fotografia');

    function toggleIndependiente() {
        const isIndependiente = independienteCheckbox.is(':checked');
        partySelect.prop('disabled', isIndependiente);
        entidadSelect.prop('disabled', isIndependiente);
        if (isIndependiente) {
            partySelect.val('');
            entidadSelect.html('<option value="">Seleccione…</option>');
        }
    }

    toggleIndependiente();

    independienteCheckbox.on('change', function() {
        toggleIndependiente();
    });

    // Cargar entidades segun partyId y seleccionar entidadSeleccionada si se da
    function cargarEntidades(partyId, entidadSeleccionada = null) {
        if (!partyId) {
            entidadSelect.html('<option value="">Seleccione…</option>').prop('disabled', true);
            return;
        }
        $.getJSON('/api/entidades/' + partyId, function(data) {
            let options = '<option value="">Seleccione…</option>';
            $.each(data, function(_, entidad) {
                const selected = entidadSeleccionada == entidad.id ? 'selected' : '';
                options += `<option value="${entidad.id}" ${selected}>${entidad.name}</option>`;
            });
            entidadSelect.html(options).prop('disabled', false);
        });
    }

    partySelect.on('change', function() {
        if (independienteCheckbox.is(':checked')) {
            entidadSelect.html('<option value="">Seleccione…</option>').prop('disabled', true);
            return;
        }
        const partyId = $(this).val();
        cargarEntidades(partyId);
    });

    // Cargar municipios segun departamentoId y seleccionar municipioSeleccionado si se da
    function cargarMunicipios(departamentoId, municipioSeleccionado = null) {
        if (!departamentoId) {
            municipioSelect.prop('disabled', true).html('<option value="">Seleccione…</option>');
            return;
        }
        $.getJSON('/api/municipios/' + departamentoId, function(data) {
            let options = '<option value="">Seleccione…</option>';
            $.each(data, function(_, municipio) {
                const selected = municipioSeleccionado == municipio.id ? 'selected' : '';
                options += `<option value="${municipio.id}" ${selected}>${municipio.name}</option>`;
            });
            municipioSelect.html(options).prop('disabled', false);
        });
    }

    departamentoSelect.on('change', function() {
        const departamentoId = $(this).val();
        cargarMunicipios(departamentoId);
    });

    // Carga inicial cuando abres el edit: cargar entidades y seleccionar la correcta
    const partidoInicial = partySelect.val();
    const entidadSeleccionada = '{{ old("entidad_id", $candidate->entidad_id ?? "") }}';
    if (partidoInicial && !independienteCheckbox.is(':checked')) {
        cargarEntidades(partidoInicial, entidadSeleccionada);
    } else {
        entidadSelect.prop('disabled', true).html('<option value="">Seleccione…</option>');
    }

    // Carga inicial municipios
    const departamentoInicial = departamentoSelect.val();
    const municipioSeleccionado = '{{ old("municipio_id", $candidate->municipio_id ?? "") }}';
    if (departamentoInicial) {
        cargarMunicipios(departamentoInicial, municipioSeleccionado);
    } else {
        municipioSelect.prop('disabled', true).html('<option value="">Seleccione…</option>');
    }

    // Actualizar label y vista previa imagen al seleccionar archivo
    fotografiaInput.on('change', function() {
        const file = this.files[0];
        if (file) {
            $(this).next('.custom-file-label').html(file.name);

            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-image').attr('src', e.target.result);
                $('#preview-filename').text(file.name);
                $('#preview-container').show();
            };
            reader.readAsDataURL(file);
        } else {
            $(this).next('.custom-file-label').html('Seleccionar archivo');
            $('#preview-container').hide();
        }
    });
});
</script>
@endpush