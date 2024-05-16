<?php

namespace IlBronza\FileCabinet\Models\Traits;

trait PopulationScopesTrait
{
	public function scopePopulated($query)
	{
		$query->whereNotNull('populated_at');
	}

	public function scopeToPopulate($query)
	{
		$query->whereNull('populated_at');
	}	
}