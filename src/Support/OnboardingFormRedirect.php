<?php

declare(strict_types=1);

namespace Hwkdo\IntranetAppFormwerk\Support;

use Hwkdo\IntranetAppFormwerk\Services\jwtService;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

/**
 * Baut die Formwerk-Onboarding-URL inkl. JWT (Legacy: MitarbeiterController@onboarding).
 */
final class OnboardingFormRedirect
{
    public function __construct(private jwtService $jwt) {}

    /**
     * @return array{
     *     username: string|null,
     *     vorname: string|null,
     *     nachname: string|null,
     *     email: string|null,
     *     gb: string|null,
     *     abteilung: string|null,
     *     standort: string|null,
     *     einstellungsdatum: string|null,
     *     tel: string|null
     * }
     */
    public function payloadFor(Model $user): array
    {
        $user->loadMissing(['gvp.parent.parent', 'standort']);

        $gvp = $user->gvp;
        $einstellungsdatum = null;
        if (isset($user->eintrittsdatum) && $user->eintrittsdatum) {
            $einstellungsdatum = $user->eintrittsdatum->format('d.m.Y');
        }

        $standort = $user->standort;
        $standortLabel = null;
        if ($standort !== null) {
            $standortLabel = (string) ($standort->name ?? $standort->bezeichnung ?? $standort->ort ?? '');
            $standortLabel = $standortLabel !== '' ? $standortLabel : null;
        }

        return [
            'username' => $user->username ?? null,
            'vorname' => $user->vorname ?? null,
            'nachname' => $user->nachname ?? null,
            'email' => $user->email ?? null,
            'gb' => GvpBusinessUnitResolver::label($gvp),
            'abteilung' => $gvp ? (string) $gvp->bezeichnung : null,
            'standort' => $standortLabel,
            'einstellungsdatum' => $einstellungsdatum,
            'tel' => $user->telefon ?? null,
        ];
    }

    public function formBaseUrl(): string
    {
        $url = trim((string) config('intranet-app-formwerk.onboarding_form_url', ''));
        if ($url === '') {
            throw new RuntimeException(
                'FORMWERK_ONBOARDING_FORM_URL ist nicht gesetzt (config intranet-app-formwerk.onboarding_form_url).',
            );
        }

        return rtrim($url, '/');
    }

    public function urlFor(Model $user): string
    {
        $jwt = $this->jwt->make($this->payloadFor($user));

        return $this->formBaseUrl().'?token='.$jwt;
    }
}
