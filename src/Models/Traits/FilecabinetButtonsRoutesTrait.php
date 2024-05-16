<?php

namespace IlBronza\FileCabinet\Models\Traits;

use IlBronza\Buttons\Button;
use IlBronza\Menu\Navbar;

trait FilecabinetButtonsRoutesTrait
{
	public function getGenerateTotalPdfUrl() : string
	{
		return $this->getKeyedRoute('generateTotalPdf');
	}

	public function getGeneratePartialPdfUrl() : string
	{
		return $this->getKeyedRoute('generatePartialPdf');
	}

	public function getGenerateTotalPdfButton() : Button
	{
        return Button::create([
            'href' => $this->getGenerateTotalPdfUrl(),
            'text' => 'filecabinet::buttons.generateTotalPdf',
            'icon' => 'file-pdf'
        ])->setPrimary();
	}

	public function getGeneratePartialPdfButton() : Button
	{
        return Button::create([
            'href' => $this->getGeneratePartialPdfUrl(),
            'text' => 'filecabinet::buttons.generatePartialPdf',
            'icon' => 'file-lines'
        ]);
	}

	public function getPopulationNavbar() : Navbar
	{
		$this->addNavbarButton(
			$this->getGenerateTotalPdfButton()
		);

		if(! $this->isRoot())
			$this->addNavbarButton(
				$this->getGeneratePartialPdfButton()
			);

		return $this->getButtonsNavbar()->setSmall();
	}


}