@extends('adminlte::page')

@section('title', 'Editar Candidato')

@section('content_header')
    <h1 class="m-0 text-dark">Editar Candidato</h1>
@stop

@section('content')
<div class="card card-primary">
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title"><i class="fas fa-user-edit mr-1"></i>Editar Candidato</h3>
    </div>

    <form id="candidate-form" action="{{ route('candidates.update', $candidate) }}" method="POST" enctype="multipart/form-data" autocomplete="off">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="row">

                {{-- Primer Nombre --}}
                <div class="col-md-6 form-group">
                    <label for="primer_nombre">Primer Nombre</label>
                    <input type="text" name="primer_nombre" id="primer_nombre" class="form-control @error('primer_nombre') is-invalid @enderror" value="{{ old('primer_nombre', $candidate->primer_nombre) }}">
                    @error('primer_nombre')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Fotografía --}}
                <div class="col-md-6 form-group">
                    <label for="fotografia"><i class="fas fa-camera mr-1"></i>Fotografía</label>
                    <div class="custom-file">
                        <input type="file" name="fotografia" id="fotografia" class="custom-file-input @error('fotografia') is-invalid @enderror" accept="image/*">
                        <label class="custom-file-label" for="fotografia">Seleccionar archivo</label>
                    </div>
                    @error('fotografia')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror

                    <div class="mt-2" id="preview-container" @if(!$candidate->fotografia) style="display:none;" @endif>
                        <img src="{{ $candidate->fotografia ? asset('storage/'.$candidate->fotografia) : '#' }}" alt="Vista previa" id="preview-image" style="max-width: 200px; max-height: 200px; border-radius: 5px;">
                        @if($candidate->fotografia_original ?? false)
                            <small class="form-text text-muted">Foto actual: {{ $candidate->fotografia_original }}</small>
                        @endif
                    </div>
                </div>

                {{-- Segundo Nombre --}}
                <div class="col-md-6 form-group">
                    <label for="segundo_nombre">Segundo Nombre</label>
                    <input type="text" name="segundo_nombre" id="segundo_nombre" class="form-control @error('segundo_nombre') is-invalid @enderror" value="{{ old('segundo_nombre', $candidate->segundo_nombre) }}">
                    @error('segundo_nombre')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Candidato Independiente --}}
                <div class="col-md-6 form-group d-flex align-items-center">
                    <div class="form-check">
                        <input type="checkbox" name="independiente" id="independiente" class="form-check-input" value="1" @checked(old('independiente', $candidate->independiente))>
                        <label for="independiente" class="form-check-label">¿Es candidato independiente?</label>
                    </div>
                </div>

                {{-- Primer Apellido --}}
                <div class="col-md-6 form-group">
                    <label for="primer_apellido">Primer Apellido</label>
                    <input type="text" name="primer_apellido" id="primer_apellido" class="form-control @error('primer_apellido') is-invalid @enderror" value="{{ old('primer_apellido', $candidate->primer_apellido) }}">
                    @error('primer_apellido')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Partido --}}
                <div class="col-md-6 form-group">
                    <label for="party_id"><i class="fas fa-flag mr-1"></i>Partido</label>
<select name="party_id" id="party_id" class="form-control @error('party_id') is-invalid @enderror">
    <option value="">Seleccione…</option>
    @foreach($parties as $party)
        <option 
            value="{{ $party->id }}" 
            data-foto="{{ $party->foto_partido ? asset('storage/'.$party->foto_partido) : '' }}"
            @selected(old('party_id', $candidate->party_id) == $party->id)>
            {{ $party->name }}
        </option>
    @endforeach
</select>

<div id="party-photo-container" class="mt-2" style="display:none;">
    <img id="party-photo" src="#" alt="Foto del partido" style="max-width: 150px; max-height: 150px; border-radius: 5px; border: 1px solid #ccc;">
</div>
                    @error('party_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Segundo Apellido --}}
                <div class="col-md-6 form-group">
                    <label for="segundo_apellido">Segundo Apellido</label>
                    <input type="text" name="segundo_apellido" id="segundo_apellido" class="form-control @error('segundo_apellido') is-invalid @enderror" value="{{ old('segundo_apellido', $candidate->segundo_apellido) }}">
                    @error('segundo_apellido')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Entidad --}}
                <div class="col-md-6 form-group">
                    <label for="entidad_id"><i class="fas fa-building mr-1"></i>Movimiento</label>
                    <select name="entidad_id" id="entidad_id" class="form-control @error('entidad_id') is-invalid @enderror">
                        <option value="">Seleccione…</option>
                        @foreach($entidades as $id => $name)
                            <option value="{{ $id }}" @selected(old('entidad_id', $candidate->entidad_id) == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('entidad_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Posición --}}
                <div class="col-md-6 form-group">
                    <label for="posicion"><i class="fas fa-sort-numeric-down mr-1"></i>Posición</label>
                    <input type="number" name="posicion" id="posicion" step="1" class="form-control @error('posicion') is-invalid @enderror" value="{{ old('posicion', $candidate->posicion) }}">
                    @error('posicion')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Número de Identidad --}}
                <div class="col-md-6 form-group">
                    <label for="numero_identidad"><i class="fas fa-id-card mr-1"></i>Número de Identidad</label>
                    <input type="text" name="numero_identidad" id="numero_identidad" maxlength="13" class="form-control @error('numero_identidad') is-invalid @enderror" value="{{ old('numero_identidad', $candidate->numero_identidad) }}">
                    @error('numero_identidad')<span class="invalid-feedback">{{ $message }}</span>@enderror
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

                {{-- Municipio --}}
                <div class="col-md-6 form-group">
                    <label for="municipio_id"><i class="fas fa-map-marker-alt mr-1"></i>Municipio</label>
                    <select name="municipio_id" id="municipio_id" class="form-control @error('municipio_id') is-invalid @enderror">
                        <option value="">Seleccione…</option>
                        @foreach($municipios as $id => $name)
                            <option value="{{ $id }}" @selected(old('municipio_id', $candidate->municipio_id) == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('municipio_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Planilla --}}
                <div class="col-md-6 form-group">
                    <label for="planilla_id"><i class="fas fa-list-alt mr-1"></i>Planilla</label>
                    <select name="planilla_id" id="planilla_id" class="form-control @error('planilla_id') is-invalid @enderror">
                        <option value="">Seleccione…</option>
                        @foreach($planillas as $planilla)
                            <option 
                                value="{{ $planilla->id }}" 
                                data-foto="{{ $planilla->foto ? asset('storage/'.$planilla->foto) : '' }}" 
                                @selected(old('planilla_id', $candidate->planilla_id) == $planilla->id)>
                                {{ $planilla->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('planilla_id')<span class="invalid-feedback">{{ $message }}</span>@enderror

                    <div id="planilla-preview-container" class="mt-3" style="display:none;">
                        <img id="planilla-preview-image" src="#" alt="Vista previa de planilla" style="max-width: 250px; max-height: 150px; border-radius: 5px; border: 1px solid #ccc;">
                    </div>
                </div>

                {{-- Candidato a reelección --}}
                <div class="col-md-6 form-group d-flex align-items-center">
                    <div class="form-check">
                        <input type="checkbox" name="reeleccion" id="reeleccion" class="form-check-input" value="1" @checked(old('reeleccion', $candidate->reeleccion))>
                        <label for="reeleccion" class="form-check-label">Candidato a reelección</label>
                    </div>
                </div>

                {{-- Espacio vacío para balancear --}}
                <div class="col-md-6"></div>

                {{-- Planes y propuestas --}}
                <div class="col-md-12 form-group">
                    <label for="propuestas"><i class="fas fa-file-alt mr-1"></i>Planes y propuestas</label>
                    <textarea name="propuestas" id="propuestas" rows="4" class="form-control @error('propuestas') is-invalid @enderror">{{ old('propuestas', $candidate->propuestas) }}</textarea>
                    @error('propuestas')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

            </div>
        </div>

<div class="card-footer">
    <div class="d-flex justify-content-between">
        <a href="{{ route('candidates.index') }}" class="btn btn-default">
            <i class="fas fa-arrow-left mr-1"></i>Cancelar
        </a>

        <x-adminlte-button type="submit" label="Actualizar candidato" theme="primary" icon="fas fa-save" />
    </div>
</div>
    </form>
</div>
@stop

@push('js')
<script>
$(function(){
    function toggleIndependienteFields() {
        let independiente = $('#independiente').is(':checked');
        $('#party_id').prop('disabled', independiente);
        if(independiente) $('#party_id').val('');
        $('#entidad_id').prop('disabled', independiente);
        if(independiente) $('#entidad_id').val('');
    }

    toggleIndependienteFields();
    $('#independiente').on('change', toggleIndependienteFields);

    // Cargar entidades según partido seleccionado
    $('#party_id').on('change', function(){
        var pid = $(this).val();
        if (!pid) {
            $('#entidad_id').html('<option value="">Seleccione…</option>').prop('disabled', true);
        } else {
            $.getJSON('/api/entidades/' + pid, function(data){
                var opts = '<option value="">Seleccione…</option>';
                $.each(data, function(_, e){
                    opts += '<option value="'+e.id+'">'+e.name+'</option>';
                });
                $('#entidad_id').html(opts).prop('disabled', false);

                // Si en edición hay entidad guardada que no coincide, re-seleccionarla
                var entidad_id_actual = "{{ old('entidad_id', $candidate->entidad_id) }}";
                if(entidad_id_actual) {
                    $('#entidad_id').val(entidad_id_actual);
                }
            });
        }
    });

    // Cargar municipios según departamento seleccionado
    function cargarMunicipios(departamento_id, selectedMunicipioId = null) {
        if (!departamento_id) {
            $('#municipio_id').html('<option value="">Seleccione…</option>').prop('disabled', true);
        } else {
            $.getJSON('/api/municipios/' + departamento_id, function(data){
                var opts = '<option value="">Seleccione…</option>';
                $.each(data, function(_, m){
                    opts += '<option value="'+m.id+'">'+m.name+'</option>';
                });
                $('#municipio_id').html(opts).prop('disabled', false);

                if(selectedMunicipioId) {
                    $('#municipio_id').val(selectedMunicipioId);
                }
            });
        }
    }

    // Al cambiar departamento, cargar municipios y filtrar planillas
    $('#departamento_id').on('change', function(){
        var departamentoId = $(this).val();
        cargarMunicipios(departamentoId);
        filtrarPlanillas();
    });

    // Al cambiar municipio o cargo, filtrar planillas
    $('#municipio_id, #cargo_id').on('change', filtrarPlanillas);

    // Vista previa fotografía candidato
    $('#fotografia').on('change', function(){
        const input = this;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-image').attr('src', e.target.result);
                $('#preview-container').show();
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            // Si no hay archivo, mostrar la foto actual o esconder preview
            @if($candidate->fotografia)
                $('#preview-image').attr('src', '{{ asset("storage/".$candidate->fotografia) }}');
                $('#preview-container').show();
            @else
                $('#preview-container').hide();
                $('#preview-image').attr('src', '#');
            @endif
        }
    });

    // Función para filtrar planillas según filtros seleccionados
    function filtrarPlanillas() {
        var cargo_id = $('#cargo_id').val();
        var departamento_id = $('#departamento_id').val();
        var municipio_id = $('#municipio_id').val();

        $.ajax({
            url: "{{ route('api.planillas.filtrar') }}",
            data: {
                cargo_id: cargo_id,
                departamento_id: departamento_id,
                municipio_id: municipio_id
            },
            success: function(planillas) {
                var $select = $('#planilla_id');
                $select.empty().append('<option value="">Seleccione…</option>');

                $.each(planillas, function(_, planilla) {
                    var fotoUrl = planilla.foto ? "{{ asset('storage') }}/" + planilla.foto : '';
                    var $option = $('<option></option>')
                        .val(planilla.id)
                        .text(planilla.nombre)
                        .attr('data-foto', fotoUrl);
                    $select.append($option);
                });

                // Intentar seleccionar la planilla guardada si está disponible
                var planillaGuardada = "{{ old('planilla_id', $candidate->planilla_id) }}";
                if(planillaGuardada && $select.find('option[value="' + planillaGuardada + '"]').length) {
                    $select.val(planillaGuardada);
                } else {
                    $select.val('');
                }

                $select.trigger('change');
            },
            error: function() {
                $('#planilla_id').html('<option value="">Seleccione…</option>');
                $('#planilla-preview-image').attr('src', '#');
                $('#planilla-preview-container').hide();
            }
        });
    }

    // Vista previa foto planilla
    function updatePlanillaPreview() {
        var selectedOption = $('#planilla_id option:selected');
        var fotoUrl = selectedOption.attr('data-foto');
        if (fotoUrl && fotoUrl.trim() !== '') {
            $('#planilla-preview-image').attr('src', fotoUrl);
            $('#planilla-preview-container').show();
        } else {
            $('#planilla-preview-image').attr('src', '#');
            $('#planilla-preview-container').hide();
        }
    }

    $('#planilla_id').on('change', updatePlanillaPreview);

    // AL CARGAR LA PÁGINA: cargar municipios y filtrar planillas con valores actuales del candidato

    // 1. cargar municipios del departamento guardado y seleccionar municipio guardado
    var departamento_id_actual = "{{ old('departamento_id', $candidate->departamento_id) }}";
    var municipio_id_actual = "{{ old('municipio_id', $candidate->municipio_id) }}";

    if(departamento_id_actual) {
        cargarMunicipios(departamento_id_actual, municipio_id_actual);
    } else {
        $('#municipio_id').html('<option value="">Seleccione…</option>').prop('disabled', true);
    }

    // 2. cargar entidades si hay party seleccionado y entidad guardada
    var party_id_actual = "{{ old('party_id', $candidate->party_id) }}";
    var entidad_id_actual = "{{ old('entidad_id', $candidate->entidad_id) }}";
    if(party_id_actual) {
        $.getJSON('/api/entidades/' + party_id_actual, function(data){
            var opts = '<option value="">Seleccione…</option>';
            $.each(data, function(_, e){
                opts += '<option value="'+e.id+'">'+e.name+'</option>';
            });
            $('#entidad_id').html(opts).prop('disabled', false);
            if(entidad_id_actual) {
                $('#entidad_id').val(entidad_id_actual);
            }
        });
    } else {
        $('#entidad_id').html('<option value="">Seleccione…</option>').prop('disabled', true);
    }

    // 3. finalmente filtrar planillas con los valores actuales
    filtrarPlanillas();

    // También actualizar preview planilla al cargar
    updatePlanillaPreview();


    function updatePartyPhoto() {
    var selectedOption = $('#party_id option:selected');
    var fotoUrl = selectedOption.data('foto');

    if (fotoUrl && fotoUrl.trim() !== '') {
        $('#party-photo').attr('src', fotoUrl);
        $('#party-photo-container').show();
    } else {
        $('#party-photo').attr('src', '#');
        $('#party-photo-container').hide();
    }
}

// Ejecutar al cargar la página para mostrar la foto si hay partido seleccionado
updatePartyPhoto();

// Ejecutar cada vez que cambia el select de partido
$('#party_id').on('change', function() {
    updatePartyPhoto();

    // También limpiar o actualizar las entidades (como ya tienes)
    var pid = $(this).val();
    if (!pid) {
        $('#entidad_id').html('<option value="">Seleccione…</option>').prop('disabled', true);
    } else {
        $.getJSON('/api/entidades/' + pid, function(data){
            var opts = '<option value="">Seleccione…</option>';
            $.each(data, function(_, e){
                opts += '<option value="'+e.id+'">'+e.name+'</option>';
            });
            $('#entidad_id').html(opts).prop('disabled', false);
        });
    }
});

});
</script>
@endpush