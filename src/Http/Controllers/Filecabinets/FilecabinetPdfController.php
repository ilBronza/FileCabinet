<?php

namespace IlBronza\FileCabinet\Http\Controllers\Filecabinets;

use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use IlBronza\FileCabinet\Helpers\FilecabinetPdfTreeHelper;
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

    public function getHeaderFooterView() : string
    {
        return 'filecabinet::pdf.filecabinet._headerFooter';
    }

    public function getPdfLogo() : ?string
    {
        $logo = config('filecabinet.pdf.logo') ?? config('uikittemplate.logo');

        if (! $logo || preg_match('~^(?:[a-z][a-z0-9+.-]*:|//)~i', $logo) || is_file($logo))
            return $logo;

        // Site logos use web paths, while Dompdf needs an absolute local file.
        $path = parse_url($logo, PHP_URL_PATH);

        if (! is_string($path) || $path === '')
            return $logo;

        $publicLogo = public_path(ltrim($path, '/'));

        return is_file($publicLogo) ? $publicLogo : $logo;
    }

    public function getPdfTitle() : ?string
    {
        $filecabinet = $this->getModel();

        return $filecabinet->getFilecabinetTemplate()?->getPdfTitle() ?? $filecabinet->getPdfTitle();
    }

    public function getPdfLogoMaxWidth() : ?int
    {
        return config('filecabinet.pdf.logo_max_width', 200);
    }

    public function getPdfLogoMaxHeight() : ?int
    {
        return config('filecabinet.pdf.logo_max_height');
    }

    public function getPdfHeaderMeasurementView() : string
    {
        return 'filecabinet::pdf.filecabinet._measureHeader';
    }

    public function getPdfHeaderHeight(array $headerFooterData) : float
    {
        // Measure with the same layout, fonts and page width as the final PDF.
        // Fixed elements do not reserve space in Dompdf's document flow.
        $dompdf = new Dompdf(config('dompdf.options', []));
        $dompdf->getOptions()->setIsRemoteEnabled(true);
        $dompdf->setBasePath(public_path());
        $dompdf->setPaper('a4', 'portrait');

        $height = 0;
        $dompdf->setCallbacks([[
            'event' => 'end_frame',
            'f' => function ($frame) use (&$height) {
                $node = $frame->get_node();

                if ($node instanceof \DOMElement && in_array('filecabinet-pdf-header', explode(' ', $node->getAttribute('class'))))
                    $height = max($height, $frame->get_margin_height());
            },
        ]]);

        $dompdf->loadHtml(view($this->getPdfHeaderMeasurementView(), [
            'headerFooterView' => $this->getHeaderFooterView(),
            'headerFooterData' => $headerFooterData,
        ])->render());
        $dompdf->render();

        // Convert points to CSS pixels, leaving 20px above and below the header.
        return max(90, ceil($height * $dompdf->getOptions()->getDpi() / 72) + 40);
    }

    public function getHeaderFooterData() : array
    {
        return [
            'logo' => $this->getPdfLogo(),
            'title' => $this->getPdfTitle(),
            'logoMaxWidth' => $this->getPdfLogoMaxWidth(),
            'logoMaxHeight' => $this->getPdfLogoMaxHeight(),
        ];
    }

    public function getPdfViewData($navbar = null) : array
    {
        $headerFooterData = $this->getHeaderFooterData();

        return [
            'filecabinet' => $this->getModel(),
            'navbar' => $navbar,
            'headerFooterView' => $this->getHeaderFooterView(),
            'headerFooterData' => $headerFooterData,
            'headerHeight' => $this->getPdfHeaderHeight($headerFooterData),
        ];
    }

    public function assignPdfModel()
    {
        $this->setModel($this->setRootFilecabinet());
        FilecabinetPdfTreeHelper::prepare($this->getModel());
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

        $pdf = Pdf::loadView($view, $this->getPdfViewData($navbar ?? null));

	    $pdf->set_option('isRemoteEnabled', true);

        $pdf->setPaper('a4', 'portrait');

        $filename = $this->getPdfFilename($filecabinet);

        return $pdf->stream($filename);
    }
}
