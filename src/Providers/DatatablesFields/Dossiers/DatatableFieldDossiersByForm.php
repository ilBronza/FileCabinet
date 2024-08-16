<?php

namespace IlBronza\FileCabinet\Providers\DatatablesFields\Dossiers;

use IlBronza\Datatables\DatatablesFields\Links\DatatableFieldLink;
use IlBronza\FileCabinet\Models\Form;

class DatatableFieldDossiersByForm  extends DatatableFieldLink
{
	public $faIcon = 'box-archive';
	public Form $form;

	/** declare this to true to get the correct javascript array management in datatables cell construction **/
	public $textParameter = true;

	public function transformValue($value)
	{
		if(! $value)
			return [null, null];

		if(isset($this->form))
			return [
				$this->form->getDossiersIndexUrl(),
				$this->form->getDossiersCount()
			];

		return [
			$value->getDossiersIndexUrl(),
			$value->getDossiersCount()
		];
	}	
}