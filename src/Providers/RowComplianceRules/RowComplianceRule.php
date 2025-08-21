<?php

namespace IlBronza\FileCabinet\Providers\RowComplianceRules;

use IlBronza\FormField\Interfaces\FormfieldModelCompatibilityInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

use function array_values;

abstract class RowComplianceRule
{
	public FormfieldModelCompatibilityInterface $field;
	public array $laravelValidatorRules = [];

	public function getValueTypeString() : string
	{
		return $this->getField()->getFormrow()->getRowType()->getValueType();
	}

	abstract public function extractProblems() : array;

	static function createByParameters(array $parameters) : static
	{
		$name = "IlBronza\FileCabinet\Providers\RowComplianceRules\\" . ucfirst($parameters['limitType']) . "RowComplianceRule";

		$complianceRule = new $name();

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

	public function setField(FormfieldModelCompatibilityInterface $field)
	{
		$this->field = $field;
	}

	public function getField() : FormfieldModelCompatibilityInterface
	{
		return $this->field;
	}

	public function getComplianceValue() : mixed
	{
		return $this->value;
	}

	public function getFieldValue() : mixed
	{
		return $this->getField()->getValue();
	}

	public function getFieldValues() : array
	{
		return [
			'fieldValue' => $this->getFieldValue()
		];
	}

	public function getProblems(FormfieldModelCompatibilityInterface $field) : array
	{
		$this->setField($field);

		return $this->extractProblems();
	}

	public function setLaravelValidatorRules(array $laravelValidatorRules)
	{
		$this->laravelValidatorRules = $laravelValidatorRules;
	}

	public function getLaravelValidatorRules() : array
	{
		return $this->laravelValidatorRules;
	}

	public function extractProblemsByLaravelValidatorRules() : array
	{
		$validator = Validator::make(
			$this->getFieldValues(),
			$this->getLaravelValidatorRules()
		);

		$result = [];

		if($validator->passes())
			return $result;

		foreach($validator->errors()->messages() as $message)
			foreach($message as $index => $text)
				$result[] = $text;

		return $result;
	}
}