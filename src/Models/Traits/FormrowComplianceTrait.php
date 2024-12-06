<?php

namespace IlBronza\FileCabinet\Models\Traits;

use IlBronza\FileCabinet\Providers\RowComplianceRules\RowComplianceRule;

trait FormrowComplianceTrait
{
	public function getValueComplianceRules()
	{
		$complianceRules = collect();

		$limits = $this->getSpecialParameter('limits', []);

		foreach($limits as $limit)
			$complianceRules->push(RowComplianceRule::createByParameters($limit));

		return $complianceRules;
	}
}
