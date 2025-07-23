@extends('adminlte::page')

@section('title', 'Crear Planilla')

@section('content_header')
    <h1>Crear Planilla</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-list" title="Nueva Planilla" class="col-md-8">
    <form action="{{ route('planillas.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <x-adminlte-input name="nombre" label="Nombre" required />

        <x-adminlte-select name="cargo_id" label="Cargo" id="cargo_id" required>
            <option value="">Seleccione...</option>
            @foreach($cargos as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </x-adminlte-select>

        <x-adminlte-select name="departamento_id" label="Departamento (opcional)" id="departamento_id">
            <option value="">-- ninguno --</option>
            @foreach($departamentos as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
            @endforeach
        </x-adminlte-select>

        <x-adminlte-select name="municipio_id" label="Municipio (opcional)" id="municipio_id" disabled>
            <option value="">-- seleccione un municipio --</option>
        </x-adminlte-select>

        <div class="form-group">
            <label for="foto">Foto de Planilla</label>
            <div class="custom-file">
                <input type="file" name="foto" id="foto" class="custom-file-input" accept="image/*" />
                <label class="custom-file-label" for="foto">Seleccionar archivo</label>
            </div>
            <small id="fileName" class="form-text text-muted"></small>
            <img id="previewImg" src="#" alt="Vista previa" class="img-thumbnail mt-2 d-none" style="max-width: 300px;" />
        </div>

        <div class="d-flex justify-content-between mt-3">
            <a href="{{ route('planillas.index') }}" class="btn btn-default">Cancelar</a>
            <x-adminlte-button type="submit" label="Guardar" theme="success" icon="fas fa-save" />
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

// Vista previa y mostrar nombre archivo
document.getElementById('foto').addEventListener('change', function () {
    let file = this.files[0];
    let preview = document.getElementById('previewImg');
    let label = this.nextElementSibling; // label.custom-file-label
    let fileName = document.getElementById('fileName');

    if (file) {
        label.textContent = file.name;
        fileName.textContent = `Archivo seleccionado: ${file.name}`;
        let reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        }
        reader.readAsDataURL(file);
    } else {
        label.textContent = 'Seleccionar archivo';
        fileName.textContent = '';
        preview.src = '#';
        preview.classList.add('d-none');
    }
});
</script>
@endpush