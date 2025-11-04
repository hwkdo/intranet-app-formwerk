<?php
 
namespace Hwkdo\IntranetAppFormwerk\Models;

use Hwkdo\MsGraphLaravel\Interfaces\MsGraphMailServiceInterface;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Microsoft\Graph\Generated\Models\Message;
use Spatie\WebhookClient\Models\WebhookCall;
 
class TypHasWebhook extends Pivot
{
    protected $table = 'intranet_app_formwerk_typ_webhooks';
    protected $guarded = [];

    public function typ(): BelongsTo
    {
        return $this->belongsTo(Typ::class);
    }

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(WebhookCall::class,'webhook_call_id');
    }

    public function mail(): ?Message
    {
        return $this->ms_graph_mail_resource ? app(MsGraphMailServiceInterface::class)->get($this->ms_graph_mail_resource) : null;
    }
}