<?php

namespace Hwkdo\IntranetAppFormwerk\Webhooks\Jobs;

use App\Webhooks\Events\WebhookReceived;
use App\Webhooks\WebhookData;
use Hwkdo\IntranetAppFormwerk\Models\Typ;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob as SpatieProcessWebhookJob;

class FormwerkJob extends SpatieProcessWebhookJob
{

    // protected $classes = [
    //     "App\\News" => IntranetSyncNewsService::class,
    //     "App\\Newsanhang" => IntranetSyncNewsanhangService::class,
    //     "App\\User" => IntranetSyncUserService::class,
    //     "App\\Standort" => IntranetSyncStandortService::class,
    //     "App\\Kategorie" => IntranetSyncKategorieService::class,
    //     "App\\Gvp" => IntranetSyncGvpService::class,
    //     "App\\Objekt" => IntranetSyncObjektService::class,
    // ];

    public function handle()
    {         
        // $this->webhookCall // contains an instance of `WebhookCall`
        // if (isset($this->classes[$data->class])) {
        //     $service = new $this->classes[$data->class];
        //     WebhookReceived::dispatch();
        //     defer(fn() => $service->handle($data));
        // } else {
        //     Log::info('IntranetSyncJob', ['No service found for class: ' . $data->class]);
        // }
        Log::info('FormwerkJob', $this->webhookCall->toArray());
        $config = collect(config('webhook-client.configs'))->where('name','formwerk')->first();
        foreach(Typ::all() as $typ) {
            if(Hash::check($typ->token,$this->webhookCall->headers[$config['signature_header_name']][0])) {                                
                $identifier = collect($this->webhookCall->payload)->firstWhere('name', $typ->identifier)["value"];
                $uuid = collect($this->webhookCall->payload)->firstWhere('name', 'formwerk_uuid')["value"];
                if($uuid) {
                    $typ->webhooks()->attach($this->webhookCall, ['identifier' => $identifier, 'formwerk_uuid' => $uuid]);
                } else {
                    Log::info('FormwerkJob', ['Formwerk UUID not found for Typ: ' . $typ->name]);
                    $typ->webhooks()->attach($this->webhookCall, ['identifier' => $identifier]);
                }
                Log::info('FormwerkJob', ['Typ found: ' . $typ->name . ' with identifier: ' . $identifier]);
            } 
        }
    }
}
