<?php

namespace IlBronza\FileCabinet\Helpers;


use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Dossierrow;

class DossierrowStatusHelper
{
	public array $alerts = [];
	public Dossierrow $dossierrow;

	public function __construct(Dossierrow $dossierrow)
	{
		$this->dossierrow = $dossierrow;


	}

	static function getStatus(Dossierrow $dossierrow) : array
	{
		return ['asdasd'];
		$helper = new static($dossierrow);
		$schedulesResult = [];

		if($dossierrow->isMissing())
			$this->addAlert($dossierrow->getName() . ' è un campo obbligatorio');

		if($dossierrow)

		$schedules = $dossierrow->getSchedules();

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

//	static function getStatus(Dossier $dossier) : array
//	{
//		$schedulesResult = [];
//		$alerts = [];
//
//		foreach($dossier->getDossierrows() as $dossierrow)
//			if($dossierrow->isMissing())
//				$alerts[] = $dossierrow->getName() . ' è un campo obbligatorio';
//
//		$schedules = $dossier->getAllSchedules();
//
//		foreach($schedules as $schedule)
//		{
//			$schedulesResult[$schedule->getKey()] = [
//				'name' => $schedule->getType()->getName(),
//				'percentage' => $schedule->getPercentageValidity(),
//				'expiring' => $schedule->getDeadlineValue()
//			];
//
//			if($schedule->isExpired())
//				$alerts[] = $schedule->getName() . ' è scaduta (' . $schedule->getDeadlineValue() . ')';
//			elseif($schedule->isExpiring())
//				$alerts[] = $schedule->getName() . ' sta per scadere (' . $schedule->getDeadlineValue() . ')';
//		}
//
//		return [
//			'name' => $dossier->getName(),
//			'populated' => $dossier->isPopulated(),
//			'schedules' => $schedulesResult,
//			'alerts' => $alerts,
//		];
//	}
}