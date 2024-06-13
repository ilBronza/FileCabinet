<?php

namespace IlBronza\FileCabinet\Models\Traits;

use IlBronza\FileCabinet\Models\Form;
use IlBronza\FileCabinet\Models\Traits\PopulationScopesTrait;

trait DossierScopesTrait
{
	use PopulationScopesTrait;

	public function scopeByForm($query, Form|string $form)
	{
		if(! is_string($form))
			$form = $form->getKey();

		$query->where('form_id', $form);
	}
}