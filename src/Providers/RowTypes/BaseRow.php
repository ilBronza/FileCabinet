<?php

namespace IlBronza\FileCabinet\Providers\RowTypes;

use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Providers\RowTypes\FormrowListInterface;
use IlBronza\FileCabinet\Providers\RowTypes\FormrowWithSpecialParametersInterface;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Interfaces\FormfieldModelCompatibilityInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRow
{
	public Model $model;
	public bool $required;
	public array $defaultRules;

	abstract public function getDefaultRules() : array;
	abstract public function getFormField() : FormField;

	public function getDossierrowValue(Dossierrow $dossierrow)
	{
		$databaseField = $this->getDatabaseField();

		$value = $dossierrow->$databaseField;

		return $this->transformValue($value);
	}

	public function transformValue(mixed $databaseValue) : mixed
	{
		return $databaseValue;
	}

	public function getShowValue(mixed $databaseValue) : mixed
	{
		return $this->transformValue($databaseValue);
	}

	public function renderValueForView($value) : ? string
	{
		return $this->getFormField()->renderValueForView($value);
	}

	public function hasSpecialParameters() : bool
	{
		if(! $this instanceof FormrowWithSpecialParametersInterface)
			return false;

		return count($this->getSpecialParametersFieldsetParameters());
	}

	public function hasValuesList() : bool
	{
		return $this instanceof FormrowListInterface;
	}

	public function setRequired(bool $required) : bool
	{
		return $this->required = $required;
	}

	public function isRequired() : bool
	{
		return $this->required;
	}

	public function getFormfieldType() : string
	{
		return static::$fieldType;
	}

	static function getType() : string
	{
		$filename = static::class;

		$namespace = FormrowNamesTypeHelper::getNamespace();

		$filename = str_replace($namespace, "", $filename);

		return lcfirst(str_replace("Formrow", "", $filename));
	}

	static function getTranslatedName() : string
	{
		return __('filecabinet::formrowstypes.' . static::getType());
	}

	public function setModel(Model $model)
	{
		$this->model = $model;
	}

	public function getModel() : Model
	{
		return $this->model;
	}

	public function getDatabaseField() : string
	{
		return static::$databaseField;
	}

	public function unsetRule(string $rule)
	{
		$key = array_search($rule, $this->defaultRules);

		if($key !== false)
			unset($this->defaultRules[$key]);
	}

	public function setRule(string $rule)
	{
		$key = array_search($rule, $this->defaultRules);

		if($key === false)
			$this->defaultRules[] = $rule;
	}

	public function isMultiple() : bool
	{
		return $this->acceptsArray();
	}

	public function isRepeatable() : bool
	{
		return $this->getModel()->getFormfieldRepeatable();
	}

	public function acceptsArray() : bool
	{
		return in_array('array', $this->buildRules(
			$this->getModel()
		));
	}

	private function manageRequiredRule()
	{
		$this->setRequired(
			$this->getModel()->isRequired()
		);

		if(! $this->isRequired())
		{
			$this->unsetRule('required');
			$this->setRule('nullable');
		}
		else
		{
			$this->unsetRule('nullable');
			$this->setRule('required');
		}
	}

	public function buildRules(FormfieldModelCompatibilityInterface $model) : array
	{
		$this->setModel($model);

		$this->defaultRules = $this->getDefaultRules();

		$this->manageRequiredRule();


		if($this->hasSpecialParameters())
			$this->addSpecialParametersValidationRules();

		return $this->defaultRules;
	}

	public function renderValue($value)
	{
		dd($this);
	}

	public function storeDossierrow(Dossierrow $dossierrow, mixed $value, bool $validate = false) : bool
	{
		$databaseField = $dossierrow->getDatabaseField();

		$dossierrow->$databaseField = $value;

		return $dossierrow->save();
	}
}