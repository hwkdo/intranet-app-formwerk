<?php

declare(strict_types=1);

namespace Hwkdo\IntranetAppFormwerk\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

final class FormwerkModels
{
    /**
     * @return class-string<Model>
     */
    public static function user(): string
    {
        return (string) config('intranet-app-formwerk.user_model', User::class);
    }
}
