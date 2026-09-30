<h3>2. Unternehmensdaten</h3>
<hr>
<br>
    <table id="table2" width=100%>
        <tr><td width="50%">Mitgliedsnummer</td>
            <td>{{ $betrieb->bnr }}</td></tr>
        <tr><td width="50%">Firma / Name des/der Gewerbetreibenden / Geschäftsbezeichnung</td>
            <td>{{ $betrieb->name }}</td></tr>
    </table>
    <br>
    <h5>a. Kontaktdaten</h5> <br>
    <table id="table2a" width=100%>
        <tr>
            <td width="50%">Betriebsanschrift</td>
            <td>{{ $betrieb->betriebsanschrift ?? '' }}</td>
        </tr>
        <tr>
            <td>Zustellanschrift</td>
            <td>{{ $betrieb->zustellanschrift ?? '' }}</td>
        </tr>
        <tr>
            <td>Telefon</td>
            <td>{{ $betrieb->betr_telefon ?? '' }}</td>
        </tr>
        <tr>
            <td>Mobil</td>
            <td>{{ $betrieb->betr_handy ?? '' }}</td>
        </tr>
        <tr>
            <td>Fax</td>
            <td>{{ $betrieb->betr_fax ?? '' }}</td>
        </tr>
        <tr>
            <td>E-Mail</td>
            <td>{{ $betrieb->betr_email ?? '' }}</td>
        </tr>
        <tr>
            <td>Homepage</td>
            <td>{{ $betrieb->internet ?? '' }}</td>
        </tr>
    </table>
    <br>

    <h5>b. Handelsregistereintragung</h5> <br>
    <table id="table2b" width=100%>
        <tr>
            <td width="50%">Registergericht</td>
            <td>{{ $betrieb->hr_gericht ?? '' }}</td>
        </tr>
        <tr>
            <td>Abteilung</td>
            <td>
                @if(! empty($betrieb->hr_abt))
                {{ $betrieb->hr_abt }}
                @else
                k.A.
                @endif
            </td>
        </tr>
        <tr>
            <td>Registernummer</td>
            <td>{{ $betrieb->hr_nummer ?? '' }}</td>
        </tr>
        <tr>
            <td>Datum der Registereintragung</td>
            <td>{{ $betrieb->hr_datum ?? '' }}</td>
        </tr>
    </table>
    <br>

    <h5>c. Betriebsstruktur</h5> <br>
    <table id="table2c" width=100%>
        <tr>
            <td width="50%">Handwerksanteil am Gesamtumsatz</td>
            <td>{{ $betrieb->handwerksanteil ?? '' }} %</td>
        </tr>
    </table>
    <br>

    <table id="table2d" width=100%>
        <tr>
            <td width="50%"><h5>d. Eintragungsgrund</h5></td>
            @if(($betrieb->eintragungsgrund ?? null) != null)
            <td>{{ $betrieb->eintragungsgrund }}</td>
            @else
            <td>k.A.</td>
            @endif
        </tr>
    </table>
    <br>
    <table id="table2e" width=100%>
        <tr>
            <td width="50%"><h5>e. Beitragsbefreiung gem. §113 II 5 HwO ab</h5></td>
            <td>
                @if(! empty($betrieb->beitragsbefreiung))
                {{ $betrieb->beitragsbefreiung }}
                @else
                k.A.
                @endif
            </td>
        </tr>
    </table>
    <br>
    <table id="table2f" width=100%>
        <tr>
            <td width="50%"><h5>f. Weitere handwerkliche Betriebsstätten</h5></td>
            @if($staetten && count($staetten) > 0)
            <td>
                @foreach($staetten as $s)
                {{ $s->betr_nr_betrst ?? '' }} | {{ $s->betriebsanschrift ?? '' }} <br>
                @endforeach
            </td>
            @else
            <td>k.A.</td>
            @endif
        </tr>
    </table>
    <br>
