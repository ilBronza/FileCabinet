<?php

namespace IlBronza\FileCabinet\Models;

use IlBronza\CRUD\Traits\CRUDSluggableTrait;
use IlBronza\FileCabinet\Models\BaseFileCabinetModel;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Form;
use IlBronza\FileCabinet\Models\Traits\FormrowJsonParametersTrait;
use IlBronza\FileCabinet\Providers\RowTypes\BaseRow;
use IlBronza\FileCabinet\Providers\RowTypes\FormrowNamesTypeHelper;
use IlBronza\FormField\Casts\JsonFieldCast;
use IlBronza\FormField\Interfaces\FormfieldModelCompatibilityInterface;
use Illuminate\Support\Str;

class Formrow extends BaseFileCabinetModel implements FormfieldModelCompatibilityInterface
{
	use CRUDSluggableTrait;
	use FormrowJsonParametersTrait;

	static $modelConfigPrefix = 'formrow';
	static $deletingRelationships = ['dossierrows'];

	protected $casts = [
		'parameters' => JsonFieldCast::class,
		'roles' => JsonFieldCast::class,
		'permissions' => JsonFieldCast::class
	];

	public ? BaseRow $rowtype = null;

	public function dossierrows()
	{
		return $this->hasMany(Dossierrow::getProjectClassName());
	}

	public function form()
	{
		return $this->belongsTo(Form::getProjectClassName());
	}

	public function getForm() : Form
	{
		return $this->form;
	}

	public function getFormId() : string
	{
		return $this->form_id;
	}

	public function getType() : string
	{
		return $this->type;
	}

	public function getRowType() : BaseRow
	{
		if($this->rowtype)
			return $this->rowtype;

		$this->rowtype = FormrowNamesTypeHelper::getByType(
			$this->getType()
		);

		$this->rowtype->setModel($this);

		return $this->rowtype;
	}

	public function getSortingIndex()
	{
		return $this->sorting_index;
	}

	public function isNullable() : bool
	{
		return ! $this->required;
	}

	public function isRequired() : bool
	{
		return !! $this->required;
	}

	public function isDisabled() : bool
	{
		return !! $this->read_only;
	}

	public function isMultiple() : bool
	{
		return $this->getRowType()->isMultiple();
	}

	public function isRepeatable() : bool
	{
		return !! $this->repeatable;
	}

	public function getRelationName() : ? string
	{
		return null;
	}

	public function getRoles() : ? array
	{
		return null;
	}

	public function getDefaultValue()
	{
		return null;
	}


	public static function boot()
	{
		parent::boot();

		self::saving(function($model)
		{
			$model->parseSpecialParametersFields();
		});

		self::deleting(function($model)
		{
			$model->getForm()->setHistoryIntervention(
				__('filecabinet::messages.removedRow', ['element' => $model->getName(), 'type' => $model->getType()])
			);
		});

		self::created(function($model)
		{
			$model->getForm()->setHistoryIntervention(
				__('filecabinet::messages.addedRow', ['element' => $model->getName(), 'type' => $model->getType()])
			);
		});
	}





	/** START INTERFACE FormfieldModelCompatibilityInterface methods **/
	public function getFormfieldType() : string
	{
		return $this->getRowType()->getFormfieldType();
	}

	public function getFormfieldValue() : mixed
	{
		return $this->getDefaultValue();
	}

	public function getFormfieldName() : string
	{
		return Str::slug($this->getName());
	}

	public function getFormfieldLabel() : string
	{
		return $this->getName();
	}

	public function isFormfieldRequired() : bool
	{
		return ! $this->isNullable();
	}

	public function isFormfieldDisabled() : bool
	{
		return !! $this->isDisabled();
	}

	public function getFormfieldRules() : array
	{
		return $this->getRowType()->buildRules($this);
	}

	public function getFormfieldRepeatable() : bool
	{
		return $this->isRepeatable();
	}

	public function isFormfieldMultiple() : bool
	{
		return $this->isMultiple();
	}

	public function getFormfieldRelationName() : ? string
	{
		return $this->getRelationName();
	}

	public function getFormfieldRoles() : ? array
	{
		return $this->getRoles();
	}
	/** END INTERFACE FormfieldModelCompatibilityInterface methods **/

	public function isExpirationDate() : bool
	{
		return $this->type == 'expirationDate';
	}

	static function findBySlug($slug) : ? static
	{
		return static::where('slug', $slug)->first();
	}
}
