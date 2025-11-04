<?php

namespace Hwkdo\IntranetAppFormwerk\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Hwkdo\IntranetAppFormwerk\IntranetAppFormwerk
 */
class IntranetAppFormwerk extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Hwkdo\IntranetAppFormwerk\IntranetAppFormwerk::class;
    }
}
