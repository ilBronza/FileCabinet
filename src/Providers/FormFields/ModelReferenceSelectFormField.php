<?php

namespace IlBronza\FileCabinet\Providers\FormFields;

use IlBronza\FormField\Fields\SelectFormField;

class ModelReferenceSelectFormField extends SelectFormField
{
	public function getShowValue()
	{
		// The field's name is the Dossierrow UUID, not a model attribute or relation.
		return $this->getModel()->renderFormfieldValue();
	}
}
