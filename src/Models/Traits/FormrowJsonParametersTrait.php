<?php

namespace IlBronza\FileCabinet\Models\Traits;

use function dd;

trait FormrowJsonParametersTrait
{
	public function parseSpecialParametersFields()
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

		$validationParametersFields = $this->getRowType()->getCheckFieldValidityParametersFields();

		foreach($validationParametersFields as $name => $fieldParameters)
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

	public function getRolesParameters() : array
	{
		if(is_array($this->roles))
			return $this->roles;

		return json_decode($this->roles, true) ?? [];
	}

	public function getPermissionsParameters() : array
	{
		if(is_array($this->permissions))
			return $this->permissions;

		return json_decode($this->permissions, true) ?? [];
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
