<?php

namespace IlBronza\FileCabinet\Providers\RowTypes;

use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Interfaces\FormfieldModelCompatibilityInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRow
{
	public Model $model;
	public ?Dossierrow $dossierrow;
	public bool $required;
	public array $defaultRules;

	public ?bool $multiple;

	public function getValueType() : string
	{
		return static::$valueType;
	}

	static function getTranslatedName() : string
	{
		return __('filecabinet::formrowstypes.' . static::getType());
	}

	static function getType() : string
	{
		$filename = static::class;

		$namespace = FormrowNamesTypeHelper::getNamespace();

		$filename = str_replace($namespace, "", $filename);

		return lcfirst(str_replace("Formrow", "", $filename));
	}

	abstract public function getCheckFieldValidityParametersFieldsetParameters() : array;

	public function getDossierrowValue(Dossierrow $dossierrow)
	{
		$databaseField = $this->getDatabaseField();

		$this->setDossierrow($dossierrow);

		$value = $dossierrow->$databaseField;

		return $this->transformValue($value);
	}

	public function getDatabaseField() : string
	{
		return static::$databaseField;
	}

	public function transformValue(mixed $databaseValue) : mixed
	{
		return $databaseValue;
	}

	public function getShowValue(mixed $databaseValue) : mixed
	{
		return $this->transformValue($databaseValue);
	}

	public function renderValueForView($value) : ?string
	{
		return $this->getFormField()->renderValueForView($value);
	}

	abstract public function getFormField() : FormField;

	public function hasValuesList() : bool
	{
		return $this instanceof FormrowListInterface;
	}

	public function getFormfieldType() : string
	{
		return static::$fieldType;
	}

	public function getDossierrow() : ?Dossierrow
	{
		return $this->dossierrow;
	}

	public function setDossierrow(Dossierrow $dossierrow = null)
	{
		$this->dossierrow = $dossierrow;
	}

	public function isMultiple() : bool
	{
		if (isset($this->multiple))
			return $this->multiple;

		return $this->acceptsArray();
	}

	public function acceptsArray() : bool
	{
		return in_array(
			'array', $this->buildRules(
			$this->getModel()
		)
		);
	}

	public function buildRules(FormfieldModelCompatibilityInterface $model, Dossierrow $dossierrow = null) : array
	{
		$this->setModel($model);

		if ($dossierrow)
			$this->setDossierrow($dossierrow);

		$this->defaultRules = $this->getDefaultRules();

		$this->manageRequiredRule();

		if ($this->hasSpecialParameters())
			$this->addSpecialParametersValidationRules();

		return $this->defaultRules;
	}

	abstract public function getDefaultRules() : array;

	private function manageRequiredRule()
	{
		$this->setRequired(
			$this->getModel()->isRequired()
		);

		if (! $this->isRequired())
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

	public function isRequired() : bool
	{
		return $this->required;
	}

	public function setRequired(bool $required) : bool
	{
		return $this->required = $required;
	}

	public function getModel() : Model
	{
		return $this->model;
	}

	public function setModel(Model $model)
	{
		$this->model = $model;
	}

	public function unsetRule(string $rule)
	{
		$key = array_search($rule, $this->defaultRules);

		if ($key !== false)
			unset($this->defaultRules[$key]);
	}

	public function setRule(string $rule)
	{
		$key = array_search($rule, $this->defaultRules);

		if ($key === false)
			$this->defaultRules[] = $rule;
	}

	public function hasSpecialParameters() : bool
	{
		if (! $this instanceof FormrowWithSpecialParametersInterface)
			return false;

		return count($this->getSpecialParametersFieldsetParameters());
	}

	public function isRepeatable() : bool
	{
		return $this->getModel()->getFormfieldRepeatable();
	}

	public function emptyRowValue(Dossierrow $dossierrow)
	{
		$databaseField = $dossierrow->getDatabaseField();

		$dossierrow->$databaseField = null;

		return $dossierrow->save();
	}

	public function storeDossierrow(Dossierrow $dossierrow, mixed $value, bool $validate = false) : bool
	{
		return $this->_storeDossierrow($dossierrow, $value, $validate);
	}

	public function _storeDossierrow(Dossierrow $dossierrow, mixed $value, bool $validate = false) : bool
	{
		$databaseField = $dossierrow->getDatabaseField();

		$dossierrow->$databaseField = $value;

		return $dossierrow->save();
	}
}