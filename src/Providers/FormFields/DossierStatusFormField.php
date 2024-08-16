<?php

namespace IlBronza\FileCabinet\Providers\FormFields;

use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FormField\Fields\FormFieldInterface;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Traits\SingleValueFormFieldTrait;

use function view;

class DossierStatusFormField extends FormField implements FormFieldInterface
{
	public Dossier $dossier;
	use SingleValueFormFieldTrait;
	use FilecabinetBaseFormFieldTrait;

	static string $viewFilename = '_dossierStatus';

//	public function renderShow()
//	{
//		// $this->executeBeforeRenderingOperations();
//
//		$type = $this->getRenderType();
//
//		return view($this->getShowViewName($type), ['field' => $this]);
//	}

	public function hasModalStatusAlerts() : bool
	{
		return config('filecabinet.dossier.statusAlerts.type', 'modal') == 'modal';
	}

	public function getValue()
	{
		return $this->getDossier()
				->getStatus();
	}

	public $htmlClasses = [];
}