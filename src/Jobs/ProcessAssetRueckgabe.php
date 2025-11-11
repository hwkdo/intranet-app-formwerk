<?php

namespace Hwkdo\IntranetAppFormwerk\Jobs;

use Hwkdo\MsGraphLaravel\Interfaces\MsGraphMailServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAssetRueckgabe implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $mailResource, public string $identifier, public ?string $uuid = null)
    {
        //
    }

    public function handle()
    {
        $mailService = app(MsGraphMailServiceInterface::class);
        $mail = $mailService->get($this->mailResource);
        Log::info('ProcessAssetRueckgabe', ['subject' => $mail->getSubject(), 'identifier' => $this->identifier]);        
    }
}
