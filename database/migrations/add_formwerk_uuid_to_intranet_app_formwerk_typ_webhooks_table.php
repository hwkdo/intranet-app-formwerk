<?php

use Hwkdo\IntranetAppFormwerk\Data\AppSettings;
use Hwkdo\IntranetAppFormwerk\Models\Typ;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\WebhookClient\Models\WebhookCall;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('intranet_app_formwerk_typ_webhooks', function (Blueprint $table) {
            $table->string('formwerk_uuid')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intranet_app_formwerk_typ_webhooks', function (Blueprint $table) {
            $table->dropColumn('formwerk_uuid');
        });
    }
};

