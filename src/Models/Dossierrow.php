<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\CRUD\Traits\Model\CRUDUseUlidKeyTrait;
use IlBronza\FileCabinet\Models\BaseFileCabinetModel;
use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Formrow;
use IlBronza\FileCabinet\Providers\RowTypes\BaseRow;
use IlBronza\FormField\FormField;
use IlBronza\FormField\Interfaces\FormfieldModelCompatibilityInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dossierrow extends BaseFileCabinetModel implements FormfieldModelCompatibilityInterface
{
	use CRUDUseUlidKeyTrait;

	static $modelConfigPrefix = 'dossierrow';
	static $deletingRelationships = [];

	public function getRepeatableFormfieldAjaxUrl() : string
	{
		return $this->getKeyedRoute('addInstance');
	}

	public function getName() : ? string
	{
		return $this->getFormrow()?->getName() ?? '-';
	}

	public function formrow() : BelongsTo
	{
		return $this->belongsTo(Formrow::getProjectClassName());
	}

	public function getFormrow() : ? Formrow
	{
		return $this->formrow;
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

	public function storeRowValue(mixed $value)
	{
		$databaseField = $this->getDatabaseField();

		$this->$databaseField = $value;

		$this->save();
	}

	public function isRepeatable() : bool
	{
		return $this->getFormrow()->isRepeatable();
	}

	public function getValue() : mixed
	{
		$databaseField = $this->getDatabaseField();

		return $this->$databaseField;
	}

	/** START INTERFACE FormfieldModelCompatibilityInterface methods **/
	public function getFormfieldType() : string
	{
		return $this->getFormrow()->getFormfieldType();
	}

	public function getFormfieldValue() : mixed
	{
		return $this->getRowType()->transformValue(
			$this->getValue()
		);
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

}
