<?php

namespace IlBronza\FileCabinet\Models\Traits;

use IlBronza\Category\Models\Category;
use IlBronza\FileCabinet\Models\Traits\PopulationScopesTrait;

trait FilecabinetScopesTrait
{
	use PopulationScopesTrait;

	public function scopeByMainCategory($query, Category $category)
	{
		$query->where('category_id', $category->getKey());
	}
}