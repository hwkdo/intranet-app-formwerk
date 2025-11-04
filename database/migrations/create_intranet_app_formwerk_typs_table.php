<?php

use Hwkdo\IntranetAppFormwerk\Data\AppSettings;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Hwkdo\IntranetAppFormwerk\Models\Typ;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('intranet_app_formwerk_typs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('form_identifier')->unique();
            $table->string('identifier');
            $table->string('identifier_xml_field');
            $table->string('subject');
            $table->string('jobClass');
            $table->string('filepath');
            $table->string('token');
            $table->json('config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $token = str()->random(20);

        Typ::create([
            'name' => 'Asset-Rueckgabe',
            'form_identifier' => 'asset-rueckgabe',
            'identifier' => 'asset_id',
            'identifier_xml_field' => 'Value',
            'subject' => '##asset-rueckgabe##',
            'jobClass' => '\Hwkdo\MsGraphLaravel\Jobs\ProcessFormwerkAssetRueckgabe',
            'filepath' => storage_path('app/non-public/files/formwerk/asset-rueckgabe'),
            'token' => $token,
            'config' => [
                
            ],
        ]);

        $token = str()->random(20);
        $tokenHash = Hash::make($token);
        Typ::create([
            'name' => 'Asset-Uebergabe',
            'form_identifier' => 'asset-uebergabe',
            'identifier' => 'asset_id',
            'identifier_xml_field' => 'Value',
            'subject' => '##asset-uebergabe##',
            'jobClass' => '\Hwkdo\MsGraphLaravel\Jobs\ProcessFormwerkAssetUebergabe',
            'filepath' => storage_path('app/non-public/files/formwerk/asset-uebergabe'),
            'token' => $token,
            'config' => [
                
            ],
        ]);

        $token = str()->random(20);
        $tokenHash = Hash::make($token);
        Typ::create([
            'name' => 'HWR-Eintrag Selfservice',
            'form_identifier' => 'hwreintrag-selfservice',
            'identifier' => 'Vorgangsnummer',
            'identifier_xml_field' => 'Eingabe',
            'subject' => '##HwrEintragSelfService##',
            'jobClass' => '\Hwkdo\MsGraphLaravel\Jobs\ProcessFormwerkHwrEintragungSelfService',
            'filepath' => storage_path('app/non-public/files/formwerk/'),
            'token' => $token,
            'config' => [
                
            ],
        ]);

        $token = str()->random(20);
        $tokenHash = Hash::make($token);
        Typ::create([
            'name' => 'HWR-Eintrag-Betriebsleiter GF',
            'form_identifier' => 'hwreintrag-betriebsleiter-gf',
            'identifier' => 'Vorgangsnummer',
            'identifier_xml_field' => 'Eingabe',
            'subject' => '##HwrEintragBetriebsleiterGF##',
            'jobClass' => '\Hwkdo\MsGraphLaravel\Jobs\ProcessFormwerkHwrEintragBetriebsleiterGF',
            'filepath' => storage_path('app/non-public/files/formwerk/'),
            'token' => $token,
            'config' => [
                
            ],
        ]);

        $token = str()->random(20);
        $tokenHash = Hash::make($token);
        Typ::create([
            'name' => 'HWR-Eintrag-Betriebsleiter AN',
            'form_identifier' => 'hwreintrag-betriebsleiter-an',
            'identifier' => 'Vorgangsnummer',
            'identifier_xml_field' => 'Eingabe',
            'subject' => '##HwrEintragBetriebsleiterAN##',
            'jobClass' => '\Hwkdo\MsGraphLaravel\Jobs\ProcessFormwerkHwrEintragBetriebsleiterAN',
            'filepath' => storage_path('app/non-public/files/formwerk/'),
            'token' => $token,
            'config' => [
                
            ],
        ]);

        $token = str()->random(20);
        $tokenHash = Hash::make($token);
        Typ::create([
            'name' => 'Handwerksrolleneintragung',
            'form_identifier' => 'hwreintrag',
            'identifier' => 'hiddenbetrnr',
            'identifier_xml_field' => 'Eingabe',
            'subject' => 'Handwerksrolleneintragung##',
            'jobClass' => '\Hwkdo\MsGraphLaravel\Jobs\ProcessFormwerkHwrEintragung',
            'filepath' => storage_path('app/non-public/files/formwerk/'),
            'token' => $token,
            'config' => [
                'auto_push_d3' => true,
                'send_success_mail' => true,
                'mail_empfaenger' => 'formwerk_hwr_eintragung@hwk-do.de'
            ],
        ]);

        $token = str()->random(20);
        $tokenHash = Hash::make($token);
        Typ::create([
            'name' => 'Mitarbeiter-Onboarding',
            'form_identifier' => 'onboarding',
            'identifier' => 'username',
            'identifier_xml_field' => 'StandardWert',
            'subject' => '##onboarding##',
            'jobClass' => '\Hwkdo\MsGraphLaravel\Jobs\ProcessFormwerkMaOnboarding',
            'filepath' => storage_path('app/non-public/files/formwerk/'),
            'token' => $token,
            'config' => [
                'send_success_mail' => true,
                'mail_empfaenger' => 'formwerk_onboarding@hwk-do.de'                
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intranet_app_formwerk_typs');
    }
};

