<?php

declare(strict_types=1);

namespace Hwkdo\IntranetAppFormwerk\Enums;

enum DatenabrufType: string
{
    case Gewerke = 'gewerke';
    case Rechtsformen = 'rechtsformen';
    case Eintragungsvoraussetzung = 'eintragungsvoraussetzung';
    case Betrieb = 'betrieb';

    public function label(): string
    {
        return match ($this) {
            self::Gewerke => 'Gewerke',
            self::Rechtsformen => 'Rechtsformen',
            self::Eintragungsvoraussetzung => 'Eintragungsvoraussetzung',
            self::Betrieb => 'Betrieb',
        };
    }

    /**
     * Relativer API-Pfad für Formwerk (ohne App-URL).
     */
    public function endpointPath(): string
    {
        return match ($this) {
            self::Gewerke => 'api/kunden/formwerk/gewerke',
            self::Rechtsformen => 'api/kunden/formwerk/rechtsformen',
            self::Eintragungsvoraussetzung => 'api/kunden/formwerk/eintragungsvoraussetzung',
            self::Betrieb => 'api/kunden/formwerk/betrieb/{nr}',
        };
    }

    /**
     * Absolute URL zum Eintragen in Formwerk (PORTAL_URL + Endpunkt).
     */
    public function formwerkUrl(): string
    {
        return rtrim((string) config('app.portal_url'), '/').'/'.$this->endpointPath();
    }
}
