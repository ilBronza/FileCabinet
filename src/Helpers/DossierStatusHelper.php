<?php

namespace IlBronza\FileCabinet\Helpers;


use IlBronza\FileCabinet\Models\Dossier;

use IlBronza\FileCabinet\Models\Dossierrow;

use Illuminate\Support\Collection;

use function array_merge;
use function dd;

class DossierStatusHelper
{
	public Collection $dossierrows;

	static function getStatus(Dossier $dossier) : array
	{
		$schedulesResult = [];
		$alerts = [];

		foreach($dossier->getDossierrows() as $dossierrow)
			if($dossierrow->isMissing())
				$alerts[] = $dossierrow->getName() . ' è un campo obbligatorio';

		$schedules = $dossier->getAllSchedules();

		foreach($schedules as $schedule)
		{
			$schedulesResult[$schedule->getKey()] = [
				'name' => $schedule->getType()->getName(),
				'percentage' => $schedule->getPercentageValidity(),
				'expiring' => $schedule->getDeadlineValue()
			];

			if($schedule->isExpired())
				$alerts[] = $schedule->getName() . ' è scaduta (' . $schedule->getDeadlineValue() . ')';
			elseif($schedule->isExpiring())
				$alerts[] = $schedule->getName() . ' sta per scadere (' . $schedule->getDeadlineValue() . ')';
		}

		return [
			'name' => $dossier->getName(),
			'populated' => $dossier->isPopulated(),
			'schedules' => $schedulesResult,
			'alerts' => $alerts,
		];
	}

	public function __construct()
	{
		$this->dossierrows = collect();
	}

	public function getDossierrows() : Collection
	{
		return $this->dossierrows;
	}

	static function checkDossierrowComplianceProblems(Dossierrow $dossierrow) : array
	{
		$helper = new static();

		$helper->addDossierrow($dossierrow);

		return $helper->getDossierrowsProblems();
	}

	public function addDossierrow(Dossierrow $dossierrow) : static
	{
		$this->dossierrows->push($dossierrow);

		return $this;
	}

	public function getDossierrowsProblems() : array
	{
		$problems = [];

		foreach($this->getDossierrows() as $dossierrow)
			$problems += $this->checkDossierrowProblems($dossierrow);

		return $problems;
	}

	public function checkDossierrowProblems(Dossierrow $dossierrow) : array
	{
		$formrow = $dossierrow->getFormrow();

		$rules = $formrow->getValueComplianceRules();

		$problems = [];

		foreach($rules as $rule)
			$problems = array_merge($problems, $rule->getProblems($dossierrow));

		return $problems;
	}
}