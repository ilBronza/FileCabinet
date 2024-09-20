<?php

namespace IlBronza\FileCabinet\Models;

use Carbon\Carbon;
use IlBronza\CRUD\Traits\Media\InteractsWithMedia;
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
		$query->whereHas('formrow', function ($query) use($formrowName)
		{
			$query->where('name', $formrowName);
		});
	}

	public function getNameForDisplayRelation()
	{
		return cache()->remember(
			$this->cacheKey('getNameForDisplayRelation'),
			3600 * 24,
			function()
			{
				return "{$this->getFormrow()->getNameForDisplayRelation()} - {$this->getDossierable()->getName()}";
			}
		);
	}

	public function scopeByFormrowSlug($query, string $formrowSlug)
	{
		$query->whereHas('formrow', function ($query) use($formrowSlug)
		{
			$query->where('slug', $formrowSlug);
		});
	}

	public function getDossierable() : ? Model
	{
		return $this->getDossier()?->getDossierable();
	}

	public function scopeByFormrow($query, Formrow $formrow)
	{
		$query->where('formrow_id', $formrow->getKey());
	}

	public function getRepeatableFormfieldAjaxUrl() : string
	{
		return $this->getKeyedRoute('addInstance');
	}

	public function getName() : ? string
	{
		return $this->getFormrow()?->getName() ?? '-';
	}

	public function getNameAttribute() : ? string
	{
		return $this->getName();
	}

	public function formrow() : BelongsTo
	{
		return $this->belongsTo(Formrow::getProjectClassName());
	}

	public function getFormrow() : ? Formrow
	{
		return $this->formrow;
	}

	public function getFormrowId() : string
	{
		return $this->formrow_id;
	}

	public function dossier() : BelongsTo
	{
		return $this->belongsTo(Dossier::getProjectClassName());
	}

	public function getDossier() : Dossier
	{
		return $this->dossier;
	}

	public function getRowType() : BaseRow
	{
		return $this->getFormrow()->getRowType();
	}

	 public function getDatabaseField()
	 {
	 	return $this->getRowType()->getDatabaseField();
	 }

	public function validateRowValue(mixed $value)
	{
		$fieldname = $this->getFormfieldName();

		$rules = FormfieldParametersHelper::getValidationRulesFromModel($this);

 		$validator = Validator::make([
 			'value' => $value
 		],
 		[
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

	public function emptyRowValue()
	{
		return $this->getRowType()
			->emptyRowValue(
				$this
			);
	}

	public function storeRowValue(mixed $value, bool $validate = false) : bool
	{
		if($validate)
			$this->validateRowValue($value);

		return $this->getRowType()
			->storeDossierrow(
				$this,
				$value,
				$validate
			);
	}

	public function isPopulated() : bool
	{
		return $this->getValue() !== null;
	}

	public function isCompleted() : bool
	{
		if(! $this->isFormfieldRequired())
			return true;

		return $this->getValue() !== null;
	}

	public function isRepeatable() : bool
	{
		return $this->getFormrow()->isRepeatable();
	}

	public function getValue() : mixed
	{
		return $this->getFormfieldValue();
	}

	public function isMissing() : bool
	{
		if(! $this->isFormfieldRequired())
			return false;

		if($this->isPopulated())
			return false;

		return true;
	}

	/** START INTERFACE FormfieldModelCompatibilityInterface methods **/
	public function getFormfieldType() : string
	{
		return $this->getFormrow()->getFormfieldType();
	}

	public function getShowValue() : mixed
	{
		return $this->getRowType()->getShowValue(
			$this->getFormfieldValue()
		);
	}

	public function getFormfieldValue() : mixed
	{
		return $this->getRowType()->getDossierrowValue(
			$this
		);
	}

	public function getCurrentDate() : Carbon
	{
		return Carbon::now();
	}

	// public function getFormField() : FormField
	// {
	// 	return $this->getRowType()->getFormField();
	// }

	public function renderFormfieldValue() : ? string
	{
		return $this->getRowType()->renderValueForView(
			$this->getFormfieldValue()
		);
	}

	public function getFormfieldName() : string
	{
		return $this->getFormrow()->getFormfieldName();
	}

	public function getFormfieldLabel() : string
	{
		return $this->getFormrow()->getFormfieldLabel();
	}

	public function isFormfieldRequired() : bool
	{
		return $this->getFormrow()->isFormfieldRequired();
	}

	public function isFormfieldDisabled() : bool
	{
		return $this->getFormrow()->isFormfieldDisabled();
	}

	public function getFormfieldRules() : array
	{
		return $this->getFormrow()->getFormfieldRules();
	}

	public function getFormfieldRepeatable() : bool
	{
		return $this->getFormrow()->getFormfieldRepeatable();
	}

	public function isFormfieldMultiple() : bool
	{
		return $this->getFormrow()->isFormfieldMultiple();
	}

	public function getFormfieldRelationName() : ? string
	{
		return $this->getFormrow()->getFormfieldRelationName();
	}

	public function getFormfieldRoles() : ? array
	{
		return $this->getFormrow()->getFormfieldRoles();
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
}
