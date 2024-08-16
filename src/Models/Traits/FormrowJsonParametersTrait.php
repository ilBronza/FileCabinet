<?php

namespace IlBronza\FileCabinet\Models\Traits;

trait FormrowJsonParametersTrait
{
	private function parseSpecialParametersFields()
	{
		if(! $this->hasSpecialParameters())
			return;

		$specialParametersFields = $this->getRowType()->getSpecialParametersFields();

		$parameters = $this->getSpecialParameters();

		foreach($specialParametersFields as $name => $fieldParameters)
		{
			$parameters[$name] = $this->$name;

			unset($this[$name]);
		}

		$this->setSpecialParameters($parameters, false);
	}

	public function hasSpecialParameters() : bool
	{
		return $this->getRowType()->hasSpecialParameters();
	}

	public function getSpecialParameters() : array
	{
		if(is_array($this->parameters))
			return $this->parameters;

		return json_decode($this->parameters, true) ?? [];
	}

	public function getSpecialParameter(string $name, mixed $default = null) : mixed
	{
		return $this->getSpecialParameters()[$name] ?? $default;
	}

	public function setSpecialParameters(array $parameters, bool $save = true)
	{
		$this->parameters = json_encode($parameters);

		if($save)
			$this->save();
	}


}
