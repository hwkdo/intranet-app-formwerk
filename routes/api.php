<?php

declare(strict_types=1);

use Hwkdo\IntranetAppFormwerk\Http\Controllers\FormwerkDatenabrufController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/kunden/formwerk')
    ->middleware('throttle:60,1')
    ->group(function (): void {
        Route::get('gewerke', [FormwerkDatenabrufController::class, 'gewerke'])
            ->name('api.formwerk.gewerke');

        Route::get('rechtsformen', [FormwerkDatenabrufController::class, 'rechtsformen'])
            ->name('api.formwerk.rechtsformen');

        Route::get('eintragungsvoraussetzung', [FormwerkDatenabrufController::class, 'eintragungsvoraussetzung'])
            ->name('api.formwerk.eintragungsvoraussetzung');

        Route::get('betrieb/{nr}', [FormwerkDatenabrufController::class, 'betrieb'])
            ->name('api.formwerk.betrieb');
    });
