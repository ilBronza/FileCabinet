<?php

namespace IlBronza\FileCabinet\Facades;

use Illuminate\Support\Facades\Facade;

class FileCabinet extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'filecabinet';
    }
}
