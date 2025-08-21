<?php

namespace IlBronza\FileCabinet\Http\Controllers\Filecabinets;

use IlBronza\FileCabinet\Helpers\FilecabinetGetTreeHelper;
use IlBronza\FileCabinet\Http\Controllers\Filecabinets\FilecabinetCRUD;
use IlBronza\FileCabinet\Models\Filecabinet;
use Illuminate\Http\Request;

class FilecabinetDisplayController extends FilecabinetCRUD
{
    public function setRootFilecabinet() : Filecabinet
    {
        $this->rootFilecabinet = FilecabinetGetTreeHelper::getDescendantTreeWithForms($this->getModel());

        return $this->getRootFilecabinet();
    }

    public function getViewMode() : string
    {
        return $this->viewMode;
    }

    public function getRootFilecabinet() : Filecabinet
    {
        return $this->rootFilecabinet;        
    }

    public function buildNavbar()
    {
        return app('menu')->createFromRecursiveTree(
            'filecabinets',
            $this->getRootFilecabinet(),
            $this->getModel()
        );
    }

    public function display(Filecabinet|string $filecabinet)
    {
        if(is_string($filecabinet))
            $filecabinet = $this->findModel($filecabinet);

        $this->setModel($filecabinet);

        $this->setRootFilecabinet();

        $navbar = $this->buildNavbar();
        $viewMode = $this->getViewMode();

        return view('filecabinet::filecabinet.populate', compact(
            'filecabinet',
            'navbar',
            'viewMode'
        ));
    }
}
