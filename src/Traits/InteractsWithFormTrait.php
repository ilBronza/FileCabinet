<?php

namespace IlBronza\FileCabinet\Traits;

use IlBronza\Category\Models\Category;
use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Filecabinet;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

trait InteractsWithFormTrait
{
	/**
	 * Get the url to attach the model to the category
	 *
	 * @param  Category  $category
	 *
	 * @return string
	 */
	public function getAttachFormByCategoryUrl(Category $category, array $data = []) : string
	{
		$routeParams = [
			'class' => get_class($this),
			'key' => $this->getKey(),
			'category' => $category->getKey()
		];

		$routeParams = array_merge($routeParams, $data);

		return app('filecabinet')->route('forms.attachByCategory', $routeParams);
	}

	/**
	 * Check if the model can be attached to the category
	 *
	 * override this to get control about it
	 *
	 * @param  Category  $category
	 *
	 * @return boolean
	 */
	public function canBeAttachedByCategory(Category $category) : bool
	{
		return true;
	}

	public function getDossiersByForms(Collection $forms) : Collection
	{
		return $this->dossiers()->whereIn(
				'form_id', $forms->pluck('id')
			)->get();
	}

	public function getDossiers() : Collection
	{
		return $this->dossiers;
	}

	public function dossiers() : MorphMany
	{
		return $this->morphMany(
			Dossier::getProjectClassName(), 'dossierable'
		);
	}

	public function filecabinets() : MorphMany
	{
		return $this->morphMany(
			Filecabinet::getProjectClassName(), 'filecabinetable'
		);
	}

	public function rootFilecabinets() : MorphMany
	{
		return $this->filecabinets()->root();
	}

	public function getRootsFilecabinets() : Collection
	{
		return $this->rootFilecabinets;
	}

	public function getRootsFilecabinetByMainCategory(Category $mainCategory) : Collection
	{
		return $this->rootFilecabinets()->byMainCategory($mainCategory)->get();
	}

	public function hasRootsFilecabinetByMainCategory(Category $mainCategory) : bool
	{
		return $this->rootFilecabinets()->byMainCategory($mainCategory)->take(1)->count() > 0;
	}

	public function getRelatedDossiersCollection() : Collection
	{
		return $this->dossiers()->with([
			'filecabinets',
			'form',
			'dossierrows.formrow',
			'dossierrows.schedules',
		])->get();
	}
}