<?php

namespace IlBronza\FileCabinet\Models\Traits;

use IlBronza\FileCabinet\Models\DossierFilecabinet;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Filecabinet;
use IlBronza\FileCabinet\Models\Form;
use IlBronza\FileCabinet\Models\Formrow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

trait DossierRelationsTrait
{
	public function filecabinets() : BelongsToMany	
	{
		return $this->belongsToMany(
			Filecabinet::getProjectClassName(),
			config('filecabinet.models.dossierFilecabinet.table')
		)->using(DossierFilecabinet::getProjectClassName());
	}

	public function getFilecabinets() : Collection
	{
		return $this->filecabinets;
	}

	public function getMainFilecabinet() : ? Filecabinet
	{
		return $this->filecabinets->first();
	}

	public function form() : BelongsTo
	{
		return $this->belongsTo(Form::getProjectClassName());
	}

	public function formrows()
	{
		return $this->hasMany(Formrow::getProjectClassName(), 'form_id', 'form_id');
	}

	public function scopeWithFormrowsIds($query)
	{
		$query->with(['formrows' => function($_query)
		{
			$_query->select('id');
		}]);
	}

	public function getForm() : Form
	{
		return $this->form;
	}

	public function getFormrowsIds() : Collection
	{
		return $this->formrows->pluck('id');
	}

	public function getDossierrowsFormrowsIds() : Collection
	{
		return $this->dossierrows->pluck('formrow_id');
	}

	public function dossierrows() : HasMany
	{
		return $this->hasMany(Dossierrow::getProjectClassName())->with('formrow');
	}

	public function getDossierrows(bool $force = false) : Collection
	{
		if($force)
			return $this->dossierrows()->get();

		return $this->dossierrows;
	}

	public function getSortedDossierrows() : Collection
	{
		return $this->getDossierrows()->sortBy(function($item)
			{
				return $item->getFormrow()->getSortingIndex();
			});
	}

	public function dossierable() : MorphTo
	{
		return $this->morphTo();
	}

	public function getDossierable() : Model
	{
		return $this->dossierable;
	}

}
