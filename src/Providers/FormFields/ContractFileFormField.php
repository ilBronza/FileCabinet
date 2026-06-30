<?php

namespace IlBronza\FileCabinet\Providers\FormFields;

use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FormField\Fields\FileFormField;
use IlBronza\FormField\Fields\FormFieldInterface;
use Illuminate\Database\Eloquent\Model;

use function is_string;
use function method_exists;
use function trim;

class ContractFileFormField extends FileFormField implements FormFieldInterface
{
	public function getViewName($type) : string
	{
		if ($this->getDisplayMode() == 'show')
			return $this->getShowViewName($type);

		return 'filecabinet::formFields._contractFile';
	}

	public function getShowViewName($type) : string
	{
		return 'filecabinet::formFields.show._contractFile';
	}

	public function getContractUrl() : ?string
	{
		$dossierrow = $this->getModel();

		$method = $this->getContractUrlGetterMethod($dossierrow);

		$dossierable = $dossierrow->getDossierable();

		return $dossierable->{$method}();
	}

	protected function getContractUrlGetterMethod(Dossierrow $dossierrow) : ?string
	{
		$method = $dossierrow->getFormrow()?->getSpecialParameter('urlContractGetterMethod');

		if (! is_string($method))
			return null;

		$method = trim($method);

		return $method !== '' ? $method : null;
	}
}
