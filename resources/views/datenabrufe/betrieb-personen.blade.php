<h3>3. Betriebspersonen</h3>
<hr>
<br>
    <table id="table3" class="table-blue" width=100%>
        <thead>
            <tr>
                <th>Vorname</th>
                <th>Name</th>
                <th>Geburtstdatum</th>
                <th>Stellung</th>
                <th>Eintragungsvoraussetzung</th>
                <th>Teiltätigkeit</th>
                <th>Befristungsdatum</th>
            </tr>
        </thead>
        <tbody>
            @foreach($personen as $person)
            <tr>
                <td>{{ $person->vorname ?? '' }}</td>
                <td>{{ $person->name ?? '' }}</td>
                <td>{{ $person->geburtsdatum ?? '' }}</td>
                <td>{{ $person->personhatstellung ?? '' }}</td>
                <td>{{ $person->eintragungsvoraussetzung ?? '' }}</td>
                <td>{{ $person->teiltaetigkeit ?? '' }}</td>
                <td>{{ $person->befristungsdatum ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br>
