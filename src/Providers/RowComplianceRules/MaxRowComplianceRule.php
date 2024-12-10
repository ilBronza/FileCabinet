<?php

namespace IlBronza\FileCabinet\Providers\RowComplianceRules;

class MaxRowComplianceRule extends RowComplianceRule
{
	public function extractProblems() : array
	{
		$typeString = $this->getValueTypeString();

		$this->setLaravelValidatorRules([
			'fieldValue' => $typeString . '|max:' . $this->getComplianceValue()
		]);

		return $this->extractProblemsByLaravelValidatorRules();
	}
}