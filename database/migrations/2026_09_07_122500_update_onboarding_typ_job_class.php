<?php

declare(strict_types=1);

use Hwkdo\IntranetAppFormwerk\Jobs\ProcessMaOnboarding;
use Hwkdo\IntranetAppFormwerk\Models\Typ;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Typ::query()
            ->where('form_identifier', 'onboarding')
            ->update([
                'jobClass' => ProcessMaOnboarding::class,
                'filepath' => storage_path('app/non-public/files/formwerk/onboarding/'),
            ]);
    }

    public function down(): void
    {
        Typ::query()
            ->where('form_identifier', 'onboarding')
            ->update([
                'jobClass' => '\\Hwkdo\\IntranetAppFormwerk\\Webhooks\\Jobs\\ProcessMaOnboarding',
                'filepath' => storage_path('app/non-public/files/formwerk/'),
            ]);
    }
};