<?php

namespace Hwkdo\IntranetAppFormwerk\Commands;

use Illuminate\Console\Command;

class IntranetAppFormwerkCommand extends Command
{
    public $signature = 'intranet-app-formwerk';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
