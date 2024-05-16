<?php

namespace IlBronza\FileCabinet\Models\Traits;

use IlBronza\Buttons\Button;

trait DossierButtonsRoutesTrait
{
	public function getEditUrl(array $data = [])
	{
		if($url = $this->getMainFilecabinet()?->getPopulateUrl())
			return $url;

		return $this->getKeyedRoute('edit');		
	}

	public function getShowUrl(array $data = [])
	{
		if($url = $this->getMainFilecabinet()?->getShowUrl())
			return $url;

		return $this->getKeyedRoute('show');		
	}

	public function getUpdateFieldsUrl()
	{
		return $this->getKeyedRoute('updateFields');
	}

	public function getCreateNewInstanceUrl()
	{
		return $this->getKeyedRoute('createNewInstance');		
	}

	public function getCreateNewInstanceButton() : Button
	{
		return Button::create([
			'href' => $this->getCreateNewInstanceUrl(),
			'text' => 'filecabinet::forms.createNewInstanceOf', ['element' => $this->getName()],
			'icon' => 'plus'
		]);
	}

	public function getUpdateFieldsButton() : Button
	{
		return Button::create([
			'href' => $this->getUpdateFieldsUrl(),
			'text' => 'filecabinet::forms.updateDossierFields',
			'icon' => 'wrench'
		]);
	}
}
