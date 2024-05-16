<?php

namespace IlBronza\FileCabinet\Http\Controllers\Filecabinets;

use IlBronza\FileCabinet\Models\Helpers\FilecabinetShow;

class FilecabinetShowController extends FilecabinetPopulateController
{
    public string $viewMode = 'show';

    public $allowedMethods = [
        'show'
    ];

    public function setModelClass()
    {
        $this->modelClass = FilecabinetShow::class;
    }

    public function show(string $filecabinet)
    {
        return $this->display($filecabinet);        
    }
}
