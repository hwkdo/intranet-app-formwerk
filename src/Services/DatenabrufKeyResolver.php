<?php

declare(strict_types=1);

namespace Hwkdo\IntranetAppFormwerk\Services;

use Hwkdo\IntranetAppFormwerk\Enums\DatenabrufType;
use Hwkdo\IntranetAppFormwerk\Models\Datenabruf;

class DatenabrufKeyResolver
{
    public function authorizeAndCount(DatenabrufType $type, ?string $key): Datenabruf
    {
        $query = Datenabruf::query()
            ->where('type', $type)
            ->where('is_active', true);

        if ($key !== null && $key !== '') {
            $query->where('key', $key);
        } else {
            $query->whereNull('key');
        }

        $datenabruf = $query->first();

        if ($datenabruf === null) {
            abort(401, 'Ungültiger oder fehlender Datenabruf-Key.');
        }

        $datenabruf->increment('call_count');
        $datenabruf->refresh();

        return $datenabruf;
    }
}
