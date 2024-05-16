<?php

namespace IlBronza\FileCabinet\Models\Traits;

use IlBronza\Category\Models\Category;
use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\DossierFilecabinet;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

trait FilecabinetRelationsTrait
{
	public function category()
	{
		return $this->belongsTo(Category::getProjectClassName());
	}

	public function getCategory() : Category
	{
		return $this->category;
	}

	public function filecabinetable() : MorphTo
	{
		return $this->morphTo();
	}

	public function dossiers() : BelongsToMany
	{
		return $this->belongsToMany(
			Dossier::getProjectClassName(),
			config('filecabinet.models.dossierFilecabinet.table')
		)->using(DossierFilecabinet::getProjectClassName());
	}

	public function getDossiers() : Collection
	{
		return $this->dossiers;
	}	
}