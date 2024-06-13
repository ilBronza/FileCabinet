<?php

namespace IlBronza\FileCabinet\Models\Traits;

use Auth;
use Carbon\Carbon;
use IlBronza\FileCabinet\Models\Formrow;
use IlBronza\Ukn\Facades\Ukn;
use Illuminate\Support\Str;

trait FormGettersSettersTrait
{
	public function getUpdatedAt() : ? Carbon
	{
		return $this->updated_at;
	}

	public function getFormrowByName(string $name) : Formrow
	{
		return cache()->remember(
			Str::slug('dossier' . $this->getName() . $name),
			3600,
			function() use($name)
			{
				if($this->relationLoaded('formrows'))
					return $this->formrows->where('name', $name)->first();

				return $this->formrows()->where('name', $name)->first();				
			}
		);
	}

	public function getCreatedAt() : ? Carbon
	{
		return $this->created_at;
	}

	public function getInterventionsArray() : array
	{
		return json_decode($this->interventions ?? '', true) ?? [];
	}

	public function getDossiersCountAttribute() : int
	{
		if($this->live_dossiers_count)
			return $this->live_dossiers_count;

		return $this->dossiers()->count();
	}

	public function getPrettyInterventionsAttribute() : string
	{
		$interventions = $this->getInterventionsArray();

		$result = [];

		foreach($interventions as $time => $intervention)
			$result[] = Carbon::createFromTimestamp($time)->toDateTimeString() . " -> " . $intervention;

		return implode("<br />", $result);
	}

	public function setHistoryIntervention(string $intervention)
	{
		if(Auth::id())
			Ukn::w(__('filecabinet::messages.formHasBeenUsedRememberToUpdateDossiersIfYouWantToSeeChanges'));

		$interventions = $this->getInterventionsArray();

		$interventions[time()] = $intervention;

		$this->interventions = json_encode($interventions);
		$this->touch();
	}

}