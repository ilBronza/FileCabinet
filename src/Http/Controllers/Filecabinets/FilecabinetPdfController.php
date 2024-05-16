<?php

namespace IlBronza\FileCabinet\Http\Controllers\Filecabinets;

use Barryvdh\DomPDF\Facade\Pdf;

class FilecabinetPdfController extends FilecabinetDisplayController
{
    public $allowedMethods = [
        'generateTotalPdf'
    ];

    public function getView() : string
    {
        return 'filecabinet::pdf.filecabinet.total';
    }

    public function assignPdfModel()
    {
        $this->setModel($this->setRootFilecabinet());
    }

    public function generateTotalPdf(string $filecabinet)
    {
        $filecabinet = $this->findModel($filecabinet);

        $this->setModel($filecabinet);

        $this->assignPdfModel();

        $navbar = $this->buildNavbar()->setViewFolder('pdf');

        $view = $this->getView();

        // return view($view, [
        //     'filecabinet' => $this->getModel(),
        //     'navbar' => $navbar
        // ]);

        $pdf = Pdf::loadView($view, [
            'filecabinet' => $this->getModel(),
            'navbar' => $navbar
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream();
    }
}
