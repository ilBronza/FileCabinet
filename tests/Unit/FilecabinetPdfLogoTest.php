<?php

namespace IlBronza\FileCabinet\Tests\Unit;

use IlBronza\FileCabinet\Http\Controllers\Filecabinets\FilecabinetPdfController;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use PHPUnit\Framework\TestCase;

class FilecabinetPdfLogoTest extends TestCase
{
    private Container $previousContainer;
    private Application $app;
    private string $directory;
    private FilecabinetPdfController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->previousContainer = Container::getInstance();
        $this->directory = sys_get_temp_dir().'/filecabinet-logo-'.uniqid();
        mkdir($this->directory.'/public/img', 0777, true);
        mkdir($this->directory.'/storage');
        file_put_contents($this->directory.'/public/img/logo.svg', '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10"><rect width="10" height="10"/></svg>');
        file_put_contents($this->directory.'/storage/custom.svg', 'custom logo');

        $this->app = new Application($this->directory);
        $this->app->instance('config', new Repository([
            'filecabinet' => ['pdf' => ['logo' => null]],
            'uikittemplate' => ['logo' => '/img/logo.svg'],
        ]));
        $this->controller = new FilecabinetPdfController();
    }

    protected function tearDown(): void
    {
        Container::setInstance($this->previousContainer);
        unlink($this->directory.'/public/img/logo.svg');
        unlink($this->directory.'/storage/custom.svg');
        rmdir($this->directory.'/public/img');
        rmdir($this->directory.'/public');
        rmdir($this->directory.'/storage');
        rmdir($this->directory);

        parent::tearDown();
    }

    public function test_site_logo_paths_resolve_to_existing_public_files(): void
    {
        foreach (['/img/logo.svg', 'img/logo.svg', '/img/logo.svg?v=2'] as $logo) {
            $this->app['config']->set('uikittemplate.logo', $logo);
            $this->assertSame($this->directory.'/public/img/logo.svg', $this->controller->getPdfLogo());
        }
    }

    public function test_pdf_override_takes_precedence_and_keeps_absolute_files(): void
    {
        $logo = $this->directory.'/storage/custom.svg';
        $this->app['config']->set('filecabinet.pdf.logo', $logo);
        $this->assertSame($logo, $this->controller->getPdfLogo());

        $this->app['config']->set('filecabinet.pdf.logo', '/img/logo.svg');
        $this->assertSame($this->directory.'/public/img/logo.svg', $this->controller->getPdfLogo());
    }

    public function test_urls_and_data_uris_are_preserved(): void
    {
        foreach (['https://example.com/logo.svg', '//example.com/logo.svg', 'data:image/svg+xml;base64,PHN2Zy8+', 'file://'.$this->directory.'/storage/custom.svg'] as $logo) {
            $this->app['config']->set('filecabinet.pdf.logo', $logo);
            $this->assertSame($logo, $this->controller->getPdfLogo());
        }
    }

    public function test_empty_override_hides_logo_and_null_falls_back_to_site_config(): void
    {
        $this->app['config']->set('filecabinet.pdf.logo', '');
        $this->assertSame('', $this->controller->getPdfLogo());

        $this->app['config']->set('filecabinet.pdf.logo', null);
        $this->app['config']->set('uikittemplate.logo', null);
        $this->assertNull($this->controller->getPdfLogo());
    }

    public function test_missing_files_keep_the_configured_source(): void
    {
        $this->app['config']->set('filecabinet.pdf.logo', '/img/missing.svg');
        $this->assertSame('/img/missing.svg', $this->controller->getPdfLogo());
    }
}
