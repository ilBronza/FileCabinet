<?php

namespace IlBronza\FileCabinet\Models\Traits;

use Auth;
use Carbon\Carbon;
use IlBronza\FileCabinet\Models\Formrow;
use IlBronza\Ukn\Facades\Ukn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait FormCheckersTrait
{
	public function isAutomaticallyCreatable() : bool
	{
		return $this->getAutomaticCreatable();
	}

	public function hasAutomaticCreationCheckerMethod() : bool
	{
		return !! $this->getAutomaticCreationCheckerMethod();
	}

	public function canAutomaticallyBeCreatedBy(Model $target) : bool
	{
		if(! $this->hasAutomaticCreationCheckerMethod())
			return $this->isAutomaticallyCreatable();

		return !! $target->{$this->getAutomaticCreationCheckerMethod()}();
	}
}