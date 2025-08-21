<?php

namespace IlBronza\FileCabinet\Models\Traits;

use Carbon\Carbon;

trait FilecabinetCompletionTrait
{
	public function _setPopulated(Carbon $populated = null)
	{
		$this->populated_at = $populated;
		$this->save();

		if(! $parent = $this->getParent())
			return ;

		$parent->checkPopulation();
	}

	public function setPopulated()
	{
		$this->_setPopulated(Carbon::now());
	}

	public function setNotPopulated()
	{
		$this->_setPopulated(null);
	}

	public function checkPopulation()
	{
		if($this->children()->toPopulate()->count() > 0)
			return $this->setNotPopulated();

		if($this->dossiers()->toPopulate()->count() > 0)
			return $this->setNotPopulated();

		return $this->setPopulated();
	}

	public function isPopulated() : bool
	{
		return !! $this->populated_at;
	}
	
}