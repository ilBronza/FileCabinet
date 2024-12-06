<?php

namespace IlBronza\FileCabinet\Providers\RowComplianceRules;

class RowComplianceRule
{
	static function createByParameters(array $parameters) : static
	{
		$complianceRule = new static();

		return $complianceRule->setParameters($parameters);
	}

	public function setParameters(array $parameters) : static
	{
		foreach ($parameters as $key => $value)
			$this->setParameter($key, $value);

		return $this;
	}

	public function setParameter($key, $value) : static
	{
		$this->{$key} = $value;

		return $this;
	}
}