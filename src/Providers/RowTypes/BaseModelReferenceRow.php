<?php

namespace IlBronza\FileCabinet\Providers\RowTypes;

use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Providers\FormFields\ModelReferenceSelectFormField;
use IlBronza\FormField\FormField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Stores dossier-specific IDs; never synchronizes the owner's relationships. */
abstract class BaseModelReferenceRow extends BaseRow implements FormrowListInterface
{
	use StandardCheckFieldValidityParametersTrait;

	static $fieldType = 'select';
	static $databaseField = 'string';
	public ?bool $multiple = false;

	/** Both queries must enforce ownership. Historical queries may include archived records. */
	abstract protected function referenceQuery(bool $historical = false) : Builder;
	abstract protected function referenceLabel(Model $model) : string;

	public function getFormField() : FormField { return new ModelReferenceSelectFormField(); }
	public function getDatatableValueMethod() : string { return 'renderValueByFormrow'; }

	public function getDefaultRules() : array
	{
		return [
			$this->isMultiple() ? 'array' : 'string',
			function ($attribute, $value, $fail) {
				$ids = $this->isMultiple() ? $value : [$value];
				if (! is_array($ids) || ! array_is_list($ids))
					return $fail(trans('filecabinet::rowTypes.invalidReference'));

				$stored = $this->storedIds();
				foreach ($ids as $id)
				{
					if (! is_string($id) || $id === '')
						return $fail(trans('filecabinet::rowTypes.invalidReference'));

					if ($this->referenceQuery()->whereKey($id)->exists())
						continue;

					if (in_array($id, $stored, true) && $this->referenceQuery(true)->whereKey($id)->exists())
						continue;

					return $fail(trans('filecabinet::rowTypes.invalidReference'));
				}
			},
		];
	}

	protected function storedIds() : array
	{
		$row = $this->getDossierrow();
		if (! $row?->exists)
			return [];

		$value = $this->transformValue($row->getRawOriginal($this->getDatabaseField()));
		return $value === null ? [] : ($this->isMultiple() ? $value : [$value]);
	}

	public function getPossibleValuesArray() : array
	{
		$result = [];
		foreach ($this->referenceQuery()->get() as $model)
			$result[$model->getKey()] = $this->referenceLabel($model);

		// Only references already stored in THIS field are offered for retention.
		foreach ($this->storedIds() as $id)
			if (! array_key_exists($id, $result))
			{
				$model = $this->referenceQuery(true)->whereKey($id)->first();
				$result[$id] = trans('filecabinet::rowTypes.historical', [
					'label' => $model ? $this->referenceLabel($model) : $id,
				]);
			}

		return $result;
	}

	public function getShowValue(mixed $databaseValue) : mixed
	{
		$value = $this->transformValue($databaseValue);
		if ($value === null)
			return null;

		$labels = [];
		foreach ($this->isMultiple() ? $value : [$value] as $id)
		{
			$model = $this->referenceQuery(true)->whereKey($id)->first();
			$labels[] = $model ? $this->referenceLabel($model) : $id;
		}

		return $this->isMultiple() ? $labels : ($labels[0] ?? null);
	}

	public function renderValueForView($value) : ?string
	{
		$labels = $this->getShowValue($value);
		return $labels === null ? null : implode('<br />', array_map('e', (array) $labels));
	}

	public function setDossierrowValue(Dossierrow $dossierrow, $value)
	{
		$this->setDossierrow($dossierrow);
		// Ownership is mandatory even when callers bypass request validation.
		validator(['value' => $value], ['value' => $this->buildRules($dossierrow->getFormrow(), $dossierrow)])->validate();
		parent::setDossierrowValue($dossierrow, $value);
	}

	public function _storeDossierrow(Dossierrow $dossierrow, mixed $value, bool $validate = false) : bool
	{
		$this->setDossierrowValue($dossierrow, $value);
		return $dossierrow->save();
	}
}
