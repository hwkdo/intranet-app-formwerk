<h3>1. Handwerkliche Tätigkeiten</h3>
<hr>
<br>
    <table id="table1" class="table-blue" width=100%>
        <thead>
            <tr>
                <th>Handwerk/Gewerbe</th>
                <th>Schwerpunktgewerbe</th>
                <th>Teiltätigkeit</th>
                <th>Beginn der Tätigkeit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gewerke as $gewerk)
            <tr>
                <td>{{ $gewerk->gewerbename }}</td>
                <td>@if($gewerk->spg) X @endif</td>
                <td>{{ $gewerk->teiltaetigkeit }}</td>
                <td>{{ \Carbon\Carbon::parse($gewerk->eintragungsdatum)->format('d.m.Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br>
