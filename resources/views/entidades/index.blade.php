<table class="table">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Partido</th>
        </tr>
    </thead>
    <tbody>
        @foreach($entidades as $entidad)
        <tr>
            <td>{{ $entidad->name }}</td>
            <td>{{ $entidad->party->name }}</td>
        </tr>
        @endforeach
    </tbody>
</table>