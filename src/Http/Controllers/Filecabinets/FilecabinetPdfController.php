<?php

namespace IlBronza\FileCabinet\Http\Controllers\Filecabinets;

use Barryvdh\DomPDF\Facade\Pdf;
use IlBronza\FileCabinet\Models\Filecabinet;

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

    public function getPdfFilename(Filecabinet $filecabinet)
    {
        return $filecabinet->filecabinetable->getPdfFilename();
    }

    public function generateTotalPdf(string $filecabinet)
    {
        $filecabinet = $this->findModel($filecabinet);

        $this->setModel($filecabinet);

        $this->assignPdfModel();

		if($filecabinet->getFilecabinetTemplate()->mustPrintMenu())
            $navbar = $this->buildNavbar()->setViewFolder('pdf');

        $view = $this->getView();

        $pdf = Pdf::loadView($view, [
            'filecabinet' => $this->getModel(),
            'navbar' => $navbar ?? null
        ]);

	    $pdf->set_option('isRemoteEnabled', true);

        $pdf->setPaper('a4', 'portrait');

        $filename = $this->getPdfFilename($filecabinet);

        return $pdf->stream($filename);
    }
}
