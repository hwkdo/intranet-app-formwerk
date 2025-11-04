<?php

namespace Hwkdo\IntranetAppFormwerk\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\WebhookClient\Models\WebhookCall;

class Typ extends Model
{
    protected $table = 'intranet_app_formwerk_typs';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'token' => 'encrypted',
        ];
    }

    public function webhooks(): BelongsToMany
    {
        return $this->belongsToMany(WebhookCall::class, 'intranet_app_formwerk_typ_webhooks')
            ->withPivot('identifier')
            ->withPivot('ms_graph_mail_resource')
            ->withTimestamps();
    }
}