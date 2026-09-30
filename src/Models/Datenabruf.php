<?php

declare(strict_types=1);

namespace Hwkdo\IntranetAppFormwerk\Models;

use Hwkdo\IntranetAppFormwerk\Database\Factories\DatenabrufFactory;
use Hwkdo\IntranetAppFormwerk\Enums\DatenabrufType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Datenabruf extends Model
{
    /** @use HasFactory<DatenabrufFactory> */
    use HasFactory;

    protected $table = 'intranet_app_formwerk_datenabrufe';

    protected $guarded = [];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => DatenabrufType::class,
            'call_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function newFactory(): DatenabrufFactory
    {
        return DatenabrufFactory::new();
    }
}
