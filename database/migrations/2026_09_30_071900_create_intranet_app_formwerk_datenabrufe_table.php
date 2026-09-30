<?php

declare(strict_types=1);

use Hwkdo\IntranetAppFormwerk\Enums\DatenabrufType;
use Hwkdo\IntranetAppFormwerk\Models\Datenabruf;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intranet_app_formwerk_datenabrufe', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('key')->nullable();
            $table->unsignedBigInteger('call_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['type', 'key'], 'iafw_da_type_key_uq');
            $table->index(['type', 'is_active'], 'iafw_da_type_active_idx');
        });

        $legacyBetriebKey = config(
            'intranet-app-formwerk.legacy_betrieb_api_key',
            '3GDLAZLtT0LV5Cfopa5FCTkAaK9AJepSU7o6JoAPhSErmd9m2gdNUKRo6yZb',
        );

        $seeds = [
            ['name' => 'Gewerke', 'type' => DatenabrufType::Gewerke, 'key' => null],
            ['name' => 'Rechtsformen', 'type' => DatenabrufType::Rechtsformen, 'key' => null],
            ['name' => 'Eintragungsvoraussetzung', 'type' => DatenabrufType::Eintragungsvoraussetzung, 'key' => null],
            ['name' => 'Betrieb', 'type' => DatenabrufType::Betrieb, 'key' => $legacyBetriebKey],
        ];

        foreach ($seeds as $seed) {
            Datenabruf::query()->create([
                'name' => $seed['name'],
                'type' => $seed['type'],
                'key' => $seed['key'],
                'call_count' => 0,
                'is_active' => true,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('intranet_app_formwerk_datenabrufe');
    }
};
