@extends('adminlte::page')

@section('title', 'Editar Planilla')

@section('content_header')
    <h1>Editar Planilla</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-list" title="Planilla: {{ $planilla->nombre }}" class="col-md-8">
    <form action="{{ route('planillas.update', $planilla) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        {{-- Nombre --}}
        <x-adminlte-input name="nombre" label="Nombre" value="{{ old('nombre', $planilla->nombre) }}" required />
        @error('nombre')
            <x-adminlte-alert theme="danger" icon="fas fa-exclamation-circle">{{ $message }}</x-adminlte-alert>
        @enderror

        {{-- Cargo --}}
        <x-adminlte-select name="cargo_id" label="Cargo" id="cargo_id" required>
            @foreach($cargos as $c)
                <option value="{{ $c->id }}" {{ $planilla->cargo_id == $c->id ? 'selected' : '' }}>
                    {{ $c->name }}
                </option>
            @endforeach
        </x-adminlte-select>
        @error('cargo_id')
            <x-adminlte-alert theme="danger" icon="fas fa-exclamation-circle">{{ $message }}</x-adminlte-alert>
        @enderror

        {{-- Departamento --}}
        <x-adminlte-select name="departamento_id" label="Departamento (opcional)" id="departamento_id">
            <option value="">-- ninguno --</option>
            @foreach($departamentos as $d)
                <option value="{{ $d->id }}" {{ $planilla->departamento_id == $d->id ? 'selected' : '' }}>
                    {{ $d->name }}
                </option>
            @endforeach
        </x-adminlte-select>
        @error('departamento_id')
            <x-adminlte-alert theme="danger" icon="fas fa-exclamation-circle">{{ $message }}</x-adminlte-alert>
        @enderror

        {{-- Municipio --}}
        <x-adminlte-select name="municipio_id" label="Municipio (opcional)" id="municipio_id" >
            <option value="">-- seleccione un municipio --</option>
            @foreach($municipios as $m)
                <option value="{{ $m->id }}" {{ old('municipio_id', $planilla->municipio_id) == $m->id ? 'selected' : '' }}>
                    {{ $m->name }}
                </option>
            @endforeach
        </x-adminlte-select>
        @error('municipio_id')
            <x-adminlte-alert theme="danger" icon="fas fa-exclamation-circle">{{ $message }}</x-adminlte-alert>
        @enderror

        {{-- Foto actual y cambiar foto --}}
        <label for="foto" class="form-label">Foto Actual</label>
        <div class="form-group">
            <div class="custom-file">
                <input type="file" name="foto" id="foto" class="custom-file-input @error('foto') is-invalid @enderror" accept="image/*">
                <label class="custom-file-label" for="foto">Seleccionar archivo</label>
                @error('foto')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Preview de foto --}}
        <div class="form-group">
            @if($planilla->foto)
                <small class="form-text text-muted" id="preview-filename">Foto actual:</small>
                <div id="preview-container">
                    <img src="{{ asset('storage/' . $planilla->foto) }}" alt="Foto actual" id="previewImg" 
                        style="max-width: 300px; max-height: 300px; border-radius: 5px;">
                </div>
            @else
                <div id="preview-container" style="display:none;">
                    <img src="" alt="Vista previa" id="previewImg" 
                        style="max-width: 300px; max-height: 300px; border-radius: 5px;">
                </div>
            @endif
        </div>

        {{-- Botones --}}
        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('planillas.index') }}" class="btn btn-default">Cancelar</a>
            <x-adminlte-button type="submit" label="Actualizar" theme="primary" icon="fas fa-save" />
        </div>
    </form>
</x-adminlte-card>
@stop

@push('js')
<script>
function actualizarCamposSegunCargo() {
    let cargoSelect = document.getElementById('cargo_id');
    let cargoText = cargoSelect.options[cargoSelect.selectedIndex]?.text.toUpperCase() || '';
    let departamento = document.getElementById('departamento_id');
    let municipio = document.getElementById('municipio_id');

    if (cargoText.includes('ALCALDE')) {
        departamento.disabled = false;
        municipio.disabled = departamento.value ? false : true;
    } else if (cargoText.includes('DIPUTADO')) {
        departamento.disabled = false;
        municipio.disabled = true;
        municipio.value = '';
    } else if (cargoText.includes('PRESIDENTE')) {
        departamento.disabled = true;
        municipio.disabled = true;
        departamento.value = '';
        municipio.value = '';
    } else {
        departamento.disabled = false;
        municipio.disabled = departamento.value ? false : true;
    }
}

document.getElementById('cargo_id').addEventListener('change', function () {
    actualizarCamposSegunCargo();
});

document.getElementById('departamento_id').addEventListener('change', function () {
    let departamentoId = this.value;
    let municipioSelect = document.getElementById('municipio_id');

    let cargoSelect = document.getElementById('cargo_id');
    let cargoText = cargoSelect.options[cargoSelect.selectedIndex]?.text.toUpperCase() || '';

    municipioSelect.innerHTML = '<option value="">-- seleccione un municipio --</option>';

    if (departamentoId) {
        fetch(`/api/municipios/${departamentoId}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    data.forEach(m => {
                        let option = document.createElement('option');
                        option.value = m.id;
                        option.text = m.name;
                        municipioSelect.appendChild(option);
                    });
                    if (!cargoText.includes('DIPUTADO') && !cargoText.includes('PRESIDENTE')) {
                        municipioSelect.disabled = false;
                    } else {
                        municipioSelect.disabled = true;
                        municipioSelect.value = '';
                    }
                }
            });
    } else {
        municipioSelect.disabled = true;
        municipioSelect.value = '';
    }
});

// Ejecutar al cargar la página para estado inicial correcto
document.addEventListener('DOMContentLoaded', actualizarCamposSegunCargo);

// Vista previa y actualización del label del input file
document.getElementById('foto').addEventListener('change', function () {
    let file = this.files[0];
    let preview = document.getElementById('previewImg');
    let label = this.nextElementSibling; // label.custom-file-label

    if (file) {
        label.textContent = file.name;  // Actualiza texto del label con nombre archivo

        let reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        }
        reader.readAsDataURL(file);
    } else {
        label.textContent = 'Seleccionar archivo';
        preview.src = '#';
        preview.classList.add('d-none');
    }
});
</script>
@endpush
