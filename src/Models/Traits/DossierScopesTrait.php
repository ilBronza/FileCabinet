<?php

namespace IlBronza\FileCabinet\Models\Traits;

use IlBronza\Category\Models\Category;
use IlBronza\FileCabinet\Models\Form;
use Illuminate\Support\Collection;

trait DossierScopesTrait
{
	use PopulationScopesTrait;

	public function scopeByCategory($query, Category $category)
	{
		$query->whereHas('form', function($_query) use($category)
		{
			$_query->whereHas('categories', function($__query) use($category)
			{
				$__query->where($category->getTable() . '.id', $category->getKey());
			});
		});
	}

	public function scopeByForm($query, Form|string $form)
	{
		if(! is_string($form))
			$form = $form->getKey();

		$query->where('form_id', $form);
	}

	public function scopeByFormsIds($query, Collection|array $formIds)
	{
		return $query->whereHas('form', function($_query) use($formIds)
		{
			$_query->whereIn('id', $formIds);
		});
	}
}