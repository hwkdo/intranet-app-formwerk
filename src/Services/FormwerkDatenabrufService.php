<?php

declare(strict_types=1);

namespace Hwkdo\IntranetAppFormwerk\Services;

use Hwkdo\BueLaravel\BueLaravel;
use Hwkdo\IntranetAppFormwerk\Enums\DatenabrufType;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FormwerkDatenabrufService
{
    public function __construct(
        private readonly BueLaravel $bue,
        private readonly DatenabrufKeyResolver $keyResolver,
    ) {}

    /**
     * @return array<string|int, string>
     */
    public function gewerke(?string $key): array
    {
        $this->keyResolver->authorizeAndCount(DatenabrufType::Gewerke, $key);

        $result = [];
        foreach ($this->bue->getGewerke() as $row) {
            $result[$row->gewerbenr] = $row->gewerbe;
        }

        return $result;
    }

    /**
     * @return array<string|int, string>
     */
    public function rechtsformen(?string $key): array
    {
        $this->keyResolver->authorizeAndCount(DatenabrufType::Rechtsformen, $key);

        $result = [];
        foreach ($this->bue->getRechtsformen() as $row) {
            $result[$row->rechtsformnr] = $row->rechtsform;
        }

        return $result;
    }

    /**
     * @return array<string|int, string>
     */
    public function eintragungsvoraussetzung(?string $key): array
    {
        $this->keyResolver->authorizeAndCount(DatenabrufType::Eintragungsvoraussetzung, $key);

        $result = [];
        foreach ($this->bue->getEintragungsvorraussetzungen() as $row) {
            $result[$row->eintragungsvoraussetzungnr] = $row->eintragungsvoraussetzung;
        }

        return $result;
    }

    /**
     * @return array{
     *     betrieb: object,
     *     gewerke: Collection<int, object>,
     *     personen: Collection<int, object>,
     *     staetten: Collection<int, object>
     * }
     */
    public function betrieb(int|string $nr, ?string $key): array
    {
        $this->keyResolver->authorizeAndCount(DatenabrufType::Betrieb, $key);

        $betrieb = $this->bue->getBetriebByBetriebsnr($nr);

        if ($betrieb === null) {
            throw new NotFoundHttpException('Betrieb nicht gefunden.');
        }

        return [
            'betrieb' => $betrieb,
            'gewerke' => $this->bue->getBetriebGewerbeByBetriebsnr($nr),
            'personen' => $this->bue->getBetriebPersonenOhneAusbilderByBetriebsnr($nr),
            'staetten' => $this->bue->getBetriebsstaettenByBetriebsnr($nr),
        ];
    }
}
