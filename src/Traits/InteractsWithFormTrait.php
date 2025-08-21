<?php

namespace IlBronza\FileCabinet\Traits;

use IlBronza\Category\Models\Category;
use IlBronza\FileCabinet\Helpers\DossierrowProviderHelper;
use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Filecabinet;
use IlBronza\FileCabinet\Models\Form;
use IlBronza\FileCabinet\Models\Formrow;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

use function in_array;
use function json_encode;

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

	public function getDossiersByForm(Form $form) : Collection
	{
		$formId = $form->getKey();

		if($this->relationLoaded('dossiers'))
			return $this->dossiers->filter(function($item) use($formId)
			{
				return $item->form_id == $formId;
			});

		return $this->dossiers()->where(
			'form_id', $formId
		)->get();
	}

	public function getValidDossierByForm(Form $form) : ? Dossier
	{
		$formId = $form->getKey();

		if($this->relationLoaded('dossiers'))
			return $this->dossiers->filter(function($item) use($formId)
			{
				return $item->form_id == $formId;
			})->sortByDesc('created_at')->first();

		return $this->dossiers()->orderByDesc('created_at')->where(
			'form_id', $formId
		)->first();
	}

	public function getDossiersByForms(Collection $forms) : Collection
	{
		$formIds = $forms->pluck('id')->toArray();

		if($this->relationLoaded('dossiers'))
			return $this->dossiers->filter(function($item) use($formIds)
			{
				return in_array($item->form_id, $formIds);
			});

		return $this->dossiers()->whereIn(
			'form_id', $formIds
		)->get();
	}

	public function getDossierValueByNames(string $formName, string $formrowName) : mixed
	{
		$form = Form::gpc()::findCachedByField('name', $formName);
		$formrow = Formrow::gpc()::where('name', $formrowName)->where('form_id', $form->getKey())->first();

		if(! $dossierrow = DossierrowProviderHelper::getFromModelFormFormrow(
			$this,
			$form,
			$formrow
		))
			return null;

		return $dossierrow->getValue();
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