<?php

// config for Hwkdo/IntranetAppFormwerk
return [
'roles' => [
        'admin' => [
            'name' => 'App-Formwerk-Admin',
            'permissions' => [
                'see-app-formwerk',
                'manage-app-formwerk',
            ]
        ],
        'user' => [
            'name' => 'App-Formwerk-Benutzer',
            'permissions' => [
                'see-app-formwerk',                
            ]
        ],
]
];
