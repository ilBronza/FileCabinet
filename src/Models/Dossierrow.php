<?php

namespace IlBronza\FileCabinet\Models;

use Carbon\Carbon;
use IlBronza\CRUD\Traits\Media\InteractsWithMedia;
use IlBronza\FileCabinet\Helpers\DossierrowStatusHelper;
use IlBronza\FileCabinet\Helpers\DossierStatusHelper;
use IlBronza\FileCabinet\Models\BaseFileCabinetModel;
use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Form;
use IlBronza\FileCabinet\Models\Formrow;
use IlBronza\FileCabinet\Providers\RowTypes\BaseRow;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Helpers\FormFieldsProvider\FormfieldParametersHelper;
use IlBronza\FormField\Interfaces\FormfieldModelCompatibilityInterface;
use IlBronza\Schedules\Traits\InteractsWithSchedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\HasMedia;

class Dossierrow extends BaseFileCabinetModel implements FormfieldModelCompatibilityInterface, HasMedia
{
	use InteractsWithMedia;
	use InteractsWithSchedule;

	static $modelConfigPrefix = 'dossierrow';
	static $deletingRelationships = [];

	public function scopeByFormrowName($query, string $formrowName)
	{
		$query->whereHas('formrow', function ($query) use ($formrowName)
		{
			$query->where('name', $formrowName);
		});
	}

	public function getCreateNewInstanceUrl() : string
	{
		return $this->getKeyedRoute('createNewInstance');
	}

	public function getNameForDisplayRelation()
	{
		return cache()->remember(
			$this->cacheKey('getNameForDisplayRelation'), 3600 * 24, function ()
		{
			if (! $this->getDossierable())
				return null;

			if (! $this->getFormrow())
				return null;

			return "{$this->getFormrow()?->getNameForDisplayRelation()} - {$this->getDossierable()->getName()}";
		}
		);
	}

	public function getDossierable() : ?Model
	{
		return $this->getDossier()?->getDossierable();
	}

	public function getDossier() : Dossier
	{
		return $this->dossier;
	}

	public function getFormrow() : ?Formrow
	{
		if ($this->formrow)
			return $this->formrow;

		throw new \Exception('manca la formrow con questo id: ' . $this->formrow_id);
	}

	public function getName() : ?string
	{
		return $this->getFormrow()?->getName() ?? '-';
	}

	/** END INTERFACE FormfieldModelCompatibilityInterface methods **/

	public function getUpdateUrl(array $data = [])
	{
		return $this->getDossier()->getUpdateUrl();
	}

	public function getShowUrl(array $data = [])
	{
		return $this->getDossier()->getShowUrl();
	}

	public function scopeByFormrowSlug($query, string $formrowSlug)
	{
		$query->whereHas('formrow', function ($query) use ($formrowSlug)
		{
			$query->where('slug', $formrowSlug);
		});
	}

	public function scopeByFormrow($query, Formrow $formrow)
	{
		$query->where('formrow_id', $formrow->getKey());
	}

	public function getRepeatableFormfieldAjaxUrl() : string
	{
		return $this->getKeyedRoute('addInstance');
	}

	public function getNameAttribute() : ?string
	{
		return $this->getName();
	}

	public function formrow() : BelongsTo
	{
		return $this->belongsTo(Formrow::getProjectClassName());
	}

	public function getFormrowId() : string
	{
		return $this->formrow_id;
	}

	public function dossier() : BelongsTo
	{
		return $this->belongsTo(Dossier::getProjectClassName());
	}

	public function getDatabaseField()
	{
		return $this->getRowType()->getDatabaseField();
	}

	public function getRowType() : BaseRow
	{
		return $this->getFormrow()->getRowType();
	}

	public function emptyRowValue()
	{
		return $this->getRowType()->emptyRowValue(
			$this
		);
	}

	public function pushRowValue(mixed $value, bool $validate = false) : bool
	{
		if (! $this->getValue())
			return $this->storeRowValue($value, $validate);

		$newDossierrow = $this->replicate();

		return $newDossierrow->storeRowValue($value, $validate);
	}

	public function getValue() : mixed
	{
		return $this->getFormfieldValue();
	}

	public function getFormfieldValue() : mixed
	{
		return $this->getRowType()->getDossierrowValue(
			$this
		);
	}

	public function storeRowValue(mixed $value, bool $validate = false) : bool
	{
		if ($validate)
			$this->validateRowValue($value);

		return $this->getRowType()->storeDossierrow(
			$this, $value, $validate
		);
	}

	public function validateRowValue(mixed $value)
	{
		$fieldname = $this->getFormfieldName();

		$rules = FormfieldParametersHelper::getValidationRulesFromModel($this);

		$validator = Validator::make([
			'value' => $value
		], [
			'value' => $rules
		]);

		if ($validator->fails())
			throw new \Exception(implode(" . ", [
				'errore in questo campo del dossier: ' . $this->getName(),
				'valore' . json_encode($value),
				json_encode($validator->getMessageBag()->getMessages()),
				json_encode($rules)
			]));
	}

	public function getFormfieldName() : string
	{
		return $this->getKey();
		//		return $this->getFormrow()->getFormfieldName();
	}

	public function getFormfieldLabel() : string
	{
		return $this->getFormrow()->getFormfieldLabel();
	}

	public function getFormfieldPlaceholder(Model $model) : ?string
	{
		return $this->getFormrow()->getFormfieldPlaceholder($this->getDossierable());
	}

	public function isCompleted() : bool
	{
		if (! $this->isFormfieldRequired())
			return true;

		return $this->getValue() !== null;
	}

	public function isFormfieldRequired() : bool
	{
		return $this->getFormrow()->isFormfieldRequired();
	}

	// public function getFormField() : FormField
	// {
	// 	return $this->getRowType()->getFormField();
	// }

	public function isFormfieldDisabled() : bool
	{
		return $this->getFormrow()->isFormfieldDisabled();
	}

	public function getFormfieldRules() : array
	{
		return $this->getFormrow()->getFormfieldRules($this);
	}

	public function getFormfieldRepeatable() : bool
	{
		return $this->getFormrow()->getFormfieldRepeatable();
	}

	public function getFormfieldTranslatedTooltip() : ?string
	{
		return $this->getFormrow()->getFormfieldTranslatedTooltip();
	}

	public function getFormfieldProblems() : array
	{
		$formrow = $this->getFormrow();
		if($formrow->slug != 'test-decimale')
			return [];

		return DossierStatusHelper::checkDossierrowComplianceProblems($this);
	}

	public function isFormfieldMultiple() : bool
	{
		$formrow = $this->getFormrow();

		$formrow->dossierrow = $this;

		return $formrow->isFormfieldMultiple();
	}

	public function getFormfieldRelationName() : ?string
	{
		return $this->getFormrow()->getFormfieldRelationName();
	}

	public function getFormfieldRoles() : ?array
	{
		return $this->getFormrow()->getFormfieldRoles();
	}

	/** START INTERFACE FormfieldModelCompatibilityInterface methods **/
	public function getFormfieldType() : string
	{
		return $this->getFormrow()->getFormfieldType();
	}

	public function isRepeatable() : bool
	{
		return $this->getFormrow()->isRepeatable();
	}

	public function isMissing() : bool
	{
		if (! $this->isFormfieldRequired())
			return false;

		if ($this->isPopulated())
			return false;

		return true;
	}

	public function isPopulated() : bool
	{
		return $this->getValue() !== null;
	}

	public function getShowValue() : mixed
	{
		return $this->getRowType()->getShowValue(
			$this->getFormfieldValue()
		);
	}

	public function getCurrentDate() : Carbon
	{
		return Carbon::now();
	}

	public function renderFormfieldValue() : ?string
	{
		return $this->getRowType()->renderValueForView(
			$this->getFormfieldValue()
		);
	}

	public function isFormfieldReadOnly() : bool
	{
		return $this->getFormrow()->isReadOnly();
	}

	public function getStatus()
	{
		return DossierrowStatusHelper::getStatus($this);
	}
}
