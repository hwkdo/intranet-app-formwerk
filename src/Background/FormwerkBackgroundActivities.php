<?php

declare(strict_types=1);

namespace Hwkdo\IntranetAppFormwerk\Background;

use Hwkdo\IntranetAppBase\Data\BackgroundActivityDefinition;
use Hwkdo\IntranetAppFormwerk\IntranetAppFormwerk;
use Hwkdo\IntranetAppFormwerk\Jobs\ProcessAssetRueckgabe;
use Hwkdo\IntranetAppFormwerk\Jobs\ProcessHwrEintragBetriebsleiterAN;
use Hwkdo\IntranetAppFormwerk\Jobs\ProcessHwrEintragBetriebsleiterGF;
use Hwkdo\IntranetAppFormwerk\Jobs\ProcessHwrEintragungSelfService;
use Hwkdo\IntranetAppFormwerk\Jobs\ProcessMaOnboarding;
use Hwkdo\IntranetAppFormwerk\Mail\MitarbeiterOnboardingDone;

final class FormwerkBackgroundActivities
{
    /**
     * @return list<BackgroundActivityDefinition>
     */
    public static function definitions(): array
    {
        $id = IntranetAppFormwerk::identifier();
        $name = IntranetAppFormwerk::app_name();

        return [
            BackgroundActivityDefinition::queued(key: 'formwerk.hwr-gf', label: 'HWR Betriebsleiter GF', appIdentifier: $id, appName: $name, job: ProcessHwrEintragBetriebsleiterGF::class, sort: 10),
            BackgroundActivityDefinition::queued(key: 'formwerk.hwr-an', label: 'HWR Betriebsleiter AN', appIdentifier: $id, appName: $name, job: ProcessHwrEintragBetriebsleiterAN::class, sort: 20),
            BackgroundActivityDefinition::queued(key: 'formwerk.hwr-self-service', label: 'HWR Self-Service', appIdentifier: $id, appName: $name, job: ProcessHwrEintragungSelfService::class, sort: 30),
            BackgroundActivityDefinition::queued(key: 'formwerk.asset-rueckgabe', label: 'Asset-Rückgabe', appIdentifier: $id, appName: $name, job: ProcessAssetRueckgabe::class, sort: 40),
            BackgroundActivityDefinition::queued(key: 'formwerk.ma-onboarding', label: 'MA-Onboarding', appIdentifier: $id, appName: $name, job: ProcessMaOnboarding::class, sort: 50),
            BackgroundActivityDefinition::mail(key: 'formwerk.mail-onboarding-done', label: 'Mail: Onboarding abgeschlossen', appIdentifier: $id, appName: $name, mailable: MitarbeiterOnboardingDone::class, sort: 60),
        ];
    }
}
