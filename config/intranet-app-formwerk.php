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
];
