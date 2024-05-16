<?php

namespace IlBronza\FileCabinet\Http\Controllers\Filecabinets;

class FilecabinetPopulateController extends FilecabinetDisplayController
{
    public string $viewMode = 'populate';

    public $allowedMethods = [
        'populate'
    ];

    public function populate(string $filecabinet)
    {
        return $this->display($filecabinet);
    }
}
