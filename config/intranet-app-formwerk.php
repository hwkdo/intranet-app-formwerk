<?php

declare(strict_types=1);

// config for Hwkdo/IntranetAppFormwerk
return [
    'roles' => [
        'admin' => [
            'name' => 'App-Formwerk-Admin',
            'permissions' => [
                'see-app-formwerk',
                'manage-app-formwerk',
            ],
        ],
        'user' => [
            'name' => 'App-Formwerk-Benutzer',
            'permissions' => [
                'see-app-formwerk',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User model (Host)
    |--------------------------------------------------------------------------
    */
    'user_model' => env('FORMWERK_USER_MODEL', \App\Models\User::class),

    /*
    |--------------------------------------------------------------------------
    | Onboarding Formwerk-Formular (pro Umgebung)
    |--------------------------------------------------------------------------
    | Dev/local z. B. https://my.formwerk.app/jnj45
    | Production: andere Formwerk-URL
    | Redirect: apps.formwerk.onboarding → JWT → diese URL?token=…
    |
    | Formwerk-Konvention (PDF-Mail-Betreff, fest):
    |   onboarding {username} ##{formwerk_uuid}##
    | Webhook: formwerk_uuid + Identifier-Feld username
    | PDF-Mailbox: intranet-app-formwerk@hwk-do.de (Graph-Subscription formwerk)
    */
    'onboarding_form_url' => env('FORMWERK_ONBOARDING_FORM_URL'),

    /*
    |--------------------------------------------------------------------------
    | Legacy Betrieb-API-Key (Formwerk Datenabruf)
    |--------------------------------------------------------------------------
    | Seed-Default für den Datenabruf-Typ "betrieb". Entspricht dem bisherigen
    | Shared Secret aus Legacy (intranet.evolnet.ds_user_webhook_call_token).
    */
    'legacy_betrieb_api_key' => env(
        'FORMWERK_LEGACY_BETRIEB_API_KEY',
        '3GDLAZLtT0LV5Cfopa5FCTkAaK9AJepSU7o6JoAPhSErmd9m2gdNUKRo6yZb',
    ),
];

