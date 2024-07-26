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

    protected function populationOrderIsCorrect()
    {
        return (($this->previousFilecabinet = FilecabinetConsecutivenessCheckerHelper::checkConsecutiveness($this->filecabinet)) === true);
    }

    public function populate(string $filecabinet)
    {
        $this->filecabinet = $this->findModel($filecabinet);

        if($this->populationOrderIsCorrect())
        {
            if(! $this->filecabinet->hasDossiers()&&($this->filecabinet->isRoot()))
                if($children = $this->filecabinet->getChildren()->first())
                    return redirect()->to($children->getPopulateUrl());

            return $this->display($this->filecabinet);
        }

        Ukn::e(__('filecabinet::messages.youMustCompletePreviousFilecabinetsBefore', ['name' => $this->previousFilecabinet->getName()]));

        return redirect()->to(
            $this->previousFilecabinet->getPopulateUrl()
        );
    }
}
