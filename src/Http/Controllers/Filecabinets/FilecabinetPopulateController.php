<?php

namespace IlBronza\FileCabinet\Http\Controllers\Filecabinets;

use IlBronza\FileCabinet\Helpers\FilecabinetConsecutivenessCheckerHelper;
use IlBronza\Ukn\Facades\Ukn;

class FilecabinetPopulateController extends FilecabinetDisplayController
{
    public string $viewMode = 'populate';

    public $allowedMethods = [
        'populate'
    ];

    public function populate(string $filecabinet)
    {
        $this->filecabinet = $this->findModel($filecabinet);

        if(($previousFilecabinet = FilecabinetConsecutivenessCheckerHelper::checkConsecutiveness($this->filecabinet)) === true)
            return $this->display($this->filecabinet);

        Ukn::e(__('filecabinets::messages.youMustCompletePreviousFilecabinetsBefore'));

        return redirect()->to(
            $previousFilecabinet->getPopulateUrl()
        );
    }
}
