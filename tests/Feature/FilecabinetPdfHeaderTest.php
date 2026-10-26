<?php

namespace IlBronza\FileCabinet\Tests\Feature;

use Dompdf\Dompdf;
use IlBronza\FileCabinet\Http\Controllers\Filecabinets\FilecabinetPdfController;
use IlBronza\FileCabinet\Models\Filecabinet;
use IlBronza\FileCabinet\Models\FilecabinetTemplate;
use IlBronza\UikitTemplate\UikitTemplate;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;

if (! class_exists(\App\Http\Controllers\Controller::class)) {
    require_once __DIR__ . '/../Fixtures/AppController.php';
}

class FilecabinetPdfHeaderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['view']->addNamespace('filecabinet', __DIR__ . '/../../resources/views');
        $this->app['view']->addNamespace('uikittemplate', dirname((new ReflectionClass(UikitTemplate::class))->getFileName(), 2) . '/resources/views');
        $this->app->instance('uikittemplate', new class {
            public function getPageTitle(): string { return 'PDF layout'; }
            public function getBodyClass(): string { return ''; }
        });
        config()->set('dompdf.options', [
            'temp_dir' => storage_path('framework/cache'),
            'font_cache' => storage_path('framework/cache'),
        ]);
    }

    public static function headerLayouts(): array
    {
        $title = 'CCM-X<br>MOTOR DRIVE TESTING<br>REPORT OF PERFORMANCE';

        return [
            'multiline title and default width limit' => [800, 200, $title, [], 200, 50],
            'tall logo without a height limit' => [100, 280, $title, [], 100, 280],
            'both limits preserve aspect ratio' => [800, 200, $title, ['logo_max_width' => 120, 'logo_max_height' => 20], 80, 20],
            'height limit without a width limit' => [800, 200, $title, ['logo_max_width' => null, 'logo_max_height' => 40], 160, 40],
            'title wraps naturally' => [800, 200, str_repeat('MOTOR DRIVE TESTING ', 8), [], 200, 50],
        ];
    }

    #[DataProvider('headerLayouts')]
    public function test_header_does_not_overlap_content_on_any_page(int $width, int $height, string $title, array $limits, int $expectedWidth, int $expectedHeight): void
    {
        $image = imagecreatetruecolor($width, $height);
        ob_start();
        imagepng($image);
        $logo = 'data:image/png;base64,' . base64_encode(ob_get_clean());
        config()->set('filecabinet.pdf', ['logo' => $logo] + $limits);

        $template = new FilecabinetTemplate();
        $template->forceFill(['pdf_title' => $title]);
        $filecabinet = new Filecabinet();
        $filecabinet->setRelation('filecabinetTemplate', $template);
        $controller = (new ReflectionClass(FilecabinetPdfController::class))->newInstanceWithoutConstructor();
        $controller->setModel($filecabinet);
        $data = $controller->getPdfViewData();

        $html = view($controller->getPdfHeaderMeasurementView(), $data)->render();
        $html = str_replace('</body>', '<div id="pdf-content">Questionnaire content</div><div style="page-break-before: always;"></div><div id="pdf-content">Continued content</div></body>', $html);

        $boxes = [];
        $dompdf = new Dompdf(config('dompdf.options'));
        $dompdf->setPaper('a4', 'portrait');
        $dompdf->setCallbacks([[
            'event' => 'end_frame',
            'f' => function ($frame, $canvas) use (&$boxes) {
                $node = $frame->get_node();
                if (! $node instanceof \DOMElement) return;

                $page = $canvas->get_page_number();
                if ($node->getAttribute('class') === 'filecabinet-pdf-header')
                    $boxes[$page]['header'] = $frame->get_border_box();
                if ($node->getAttribute('id') === 'pdf-content')
                    $boxes[$page]['content'] = $frame->get_border_box();
                if ($node->tagName === 'img')
                    $boxes[$page]['logo'] = $frame->get_border_box();
            },
        ]]);
        $dompdf->loadHtml($html);
        $dompdf->render();

        $this->assertSame(2, $dompdf->getCanvas()->get_page_count());
        $this->assertCount(2, $boxes);
        foreach ($boxes as $page => $box) {
            $this->assertArrayHasKey('header', $box);
            $this->assertArrayHasKey('content', $box);
            $this->assertGreaterThanOrEqual($box['header'][1] + $box['header'][3] + 14.9, $box['content'][1], 'Header overlaps content on page ' . $page);
            $this->assertEqualsWithDelta($expectedWidth * 0.75, $box['logo'][2], 0.1);
            $this->assertEqualsWithDelta($expectedHeight * 0.75, $box['logo'][3], 0.1);
        }
    }
}
