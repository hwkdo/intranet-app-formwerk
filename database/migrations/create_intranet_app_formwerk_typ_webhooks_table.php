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
        Schema::create('intranet_app_formwerk_typ_webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Typ::class);
            $table->foreignIdFor(WebhookCall::class);
            $table->string('identifier')->nullable();
            $table->string('ms_graph_mail_resource')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intranet_app_formwerk_typ_webhooks');
    }
};

