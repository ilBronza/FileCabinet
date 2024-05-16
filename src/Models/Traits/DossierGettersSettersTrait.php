<?php

namespace IlBronza\FileCabinet\Models\Traits;

use Auth;
use Carbon\Carbon;

trait DossierGettersSettersTrait
{
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
