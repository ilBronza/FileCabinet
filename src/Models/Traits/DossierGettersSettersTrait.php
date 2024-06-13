<?php

namespace IlBronza\FileCabinet\Models\Traits;

use Auth;
use Carbon\Carbon;
use IlBronza\FileCabinet\Models\Dossierrow;

trait DossierGettersSettersTrait
{
	public function getRowByName(string $rowName) : Dossierrow
	{
		// if(! $this->relationLoaded('dossierrows'))
		// 	return $this->dossierrows()->byFormrowName($rowName)->orderByDesc('created_at')->first();

		$dossierrows = $this->getDossierrows();

		$formrow = $this->getForm()->getFormrowByName($rowName);

		return $dossierrows->sortByDesc('created_at')->first(function($item) use($formrow)
		{
			return $item->formrow_id == $formrow->getKey();
		});
	}

	public function setNotPopulated()
	{
		$this->populated_at = null;
		$this->populated_by = null;
		$this->save();

		foreach($this->getFilecabinets() as $filecabinet)
			$filecabinet->checkPopulation();		
	}

	public function setPopulated()
	{
		$this->populated_at = Carbon::now();
		$this->populated_by = Auth::id();
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
}
