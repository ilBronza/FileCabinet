<?php

namespace IlBronza\FileCabinet\Providers\RowComplianceRules;

class MinRowComplianceRule extends RowComplianceRule
{
	public function extractProblems() : array
	{
		$typeString = $this->getValueTypeString();

		$this->setLaravelValidatorRules([
			'fieldValue' => $typeString . '|min:' . $this->getComplianceValue()
		]);

		return $this->extractProblemsByLaravelValidatorRules();
	}
}