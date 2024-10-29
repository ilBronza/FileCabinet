<?php

namespace IlBronza\FileCabinet\Models\Traits;

use Auth;
use Carbon\Carbon;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Formrow;

trait DossierGettersSettersTrait
{
	public function hasUpdateEditor() : bool
	{
		if(isset($this->updateEditor))
			return $this->updateEditor;

		return config('filecabinet.updateEditor', true);
	}

	public function getRowByName(string $rowName) : Dossierrow
	{
		if(! $this->relationLoaded('dossierrows'))
		{
			if($result = $this->dossierrows()->byFormrowName($rowName)->orderByDesc('created_at')->first())
				return $result;

			dd($rowName);
		}

		$formrow = $this->formrows()->where('name', $rowName)->first();

		if($result = ($this->filterDossierrowsByFormrow($formrow)))
			return $result;

		return $this->dossierrows()->byFormrowName($rowName)->orderByDesc('created_at')->first();
	}

	public function getDossierrowByFormrowSlug(string $formrowSlug)
	{
		if(! $this->relationLoaded('dossierrows'))
			return $this->dossierrows()->byFormrowSlug($formrowSlug)->orderByDesc('created_at')->first();

		$formrow = $this->formrows()->where('slug', $formrowSlug)->first();

		if($result = ($this->filterDossierrowsByFormrow($formrow)))
			return $result;

		return $this->dossierrows()->byFormrowSlug($formrowSlug)->orderByDesc('created_at')->first();
	}

	public function getDossierrowByFormrow(Formrow $formrow) : Dossierrow
	{
		if($this->relationLoaded('dossierrows'))
			if($result = $this->getDossierrows()->firstWhere('formrow_id', $formrow->getKey()))
				return $result;

		return $this->dossierrows()->byFormrow($formrow)->first();
	}

	public function setNotPopulated(bool $save = true)
	{
		$this->populated_at = null;
		$this->populated_by = null;

		if($save)
			$this->save();

		if($save)
			foreach($this->getFilecabinets() as $filecabinet)
				$filecabinet->checkPopulation();		
	}

	public function setPopulated(bool $save = true)
	{
		$this->populated_at = Carbon::now();
		$this->populated_by = Auth::id();

		if($save)
			$this->save();

		foreach($this->getFilecabinets() as $filecabinet)
			$filecabinet->checkPopulation();
	}

	public function getPopulatedAt() : ? Carbon
	{
		return $this->populated_at;
	}

	public function getName() : ? string
	{
		return $this->getForm()->getName();
	}

	public function getNameAttribute() : string
	{
		return $this->getName();
	}

	public function getDescription() : ? string
	{
		return $this->getForm()->getDescription();
	}

	public function getUpdatedAt() : ? Carbon
	{
		return $this->updated_at;
	}

	public function getCreatedAt() : ? Carbon
	{
		return $this->created_at;
	}

	public function getMustBeUpdatedAt() : ? Carbon
	{
		return $this->must_be_updated_at;
	}

	public function setFieldsUpdated()
	{
		return $this->_customSetter('must_be_updated_at', null, true);
	}

	/**
	 * @param  \Illuminate\Database\Eloquent\Model|object|\Illuminate\Database\Eloquent\Relations\HasMany|null  $formrow
	 *
	 * @return mixed
	 */
	public function filterDossierrowsByFormrow(Formrow $formrow)
	{
		return $this->getDossierrows()->sortByDesc('created_at')->first(function ($item) use ($formrow)
		{
			return $item->formrow_id == $formrow->getKey();
		});
	}

	public function getValueByFormrow(Formrow $formrow)
	{
		return $this->getDossierrowByFormrow($formrow)?->getValue();
	}

	public function setValueByFormrow(Formrow $formrow, mixed $value, bool $validate = false)
	{
		return $this->getDossierrowByFormrow($formrow)->storeRowValue($value, true);
	}

	public function pushValueByFormrow(Formrow $formrow, mixed $value, bool $validate = false)
	{
		return $this->getDossierrowByFormrow($formrow)->pushRowValue($value, true);
	}
}

