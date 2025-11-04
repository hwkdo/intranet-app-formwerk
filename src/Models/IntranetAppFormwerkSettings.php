<?php

namespace Hwkdo\IntranetAppFormwerk\Models;

use Hwkdo\IntranetAppFormwerk\Data\AppSettings;
use Illuminate\Database\Eloquent\Model;

class IntranetAppFormwerkSettings extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'settings' => AppSettings::class.':default',
        ];
    }

    public static function current(): IntranetAppFormwerkSettings|null
    {
        return self::orderBy('version', 'desc')->first();
    }
}
