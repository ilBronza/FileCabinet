<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use Carbon\Carbon;
use IlBronza\CRUD\Helpers\ModelManagers\CrudModelAssociatorHelper;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Providers\RowTypes\BaseModelRelationRow;
use IlBronza\FileCabinet\Providers\RowTypes\StandardCheckFieldValidityParametersTrait;
use Illuminate\Support\Facades\Log;

class FormrowRelation extends BaseModelRelationRow
{
	use StandardCheckFieldValidityParametersTrait;

	public function getDefaultRules() : array
	{
		return [
			$this->isMultiple() ? 'array' : 'string'
		];
	}

	public function isMultiple() : bool
	{
		$model = $this->getDossierrow()->getDossierable();
		$relationName = $this->getRelationName();

		$relationType = CrudModelAssociatorHelper::getRelationTypeName(
				$model,
				$relationName
			);

		if($relationType == 'BelongsToMany')
			return true;

		if($relationType == 'BelongsTo')
			return false;

		dd("vagliare le altre");
	}

	public function transformValue(mixed $databaseValue) : mixed
	{
		$model = $this->getDossierrow()->getDossierable();
		$relationName = $this->getRelationName();

		$relationType = CrudModelAssociatorHelper::getRelationTypeName(
				$model,
				$relationName
			);

		$relatedModelPrimaryKeyName = $model->{$relationName}()->make()->getKeyName();

		if($relationType == 'BelongsToMany')
			return $model->{$relationName}()->get()->pluck($relatedModelPrimaryKeyName);

		if($relationType == 'BelongsTo')
		{
			$fieldName = $model->{$relationName}()->getForeignKeyName();

			return $model->$fieldName;
		}

		dd('qua c\'è una relazione di tipo ' . $relationType);
	}

	public function getRelationName() : ? string
	{
		return $this->getModel()->getSpecialParameter('relation_name', null);
	}


	public function getValidationRulesArrayFromSpecialParametersArray() : array
	{
		Log::critical('qua invece che fare implode vedi di fare un exists cristo iddio');

		$fields = $this->getPossibleValuesArray();

		return [
			'in:' . implode(",", array_keys($fields))
		];
	}


	public function getPossibleValuesArray() : array
	{
		if(! $dossierrow = $this->getDossierrow())
			return [];

		$dossierable = $dossierrow->getDossierable();

		$getterMethodName = 'getPossible' . ucfirst($this->getRelationName()) . 'ValuesArray';

		if (method_exists($dossierable, $getterMethodName))
			return $dossierable->$getterMethodName();

		if(! $this->getRelationName())
			throw new \Exception("Relation name field (relation_name) not set for Formrow {$this->getModel()->getName()} id {$this->getModel()->getKey()} of form {$this->getModel()->getForm()->getName()}");

		return $dossierable->_getRelationshipPossibleValuesArray(
			$this->getRelationName()
		);
	}

	public function getSpecialParametersFieldsetParameters() : array
	{
		return [
			'parameters' => [
				'translationPrefix' => 'filecabinet::fields',
				'fields' => [
					'relation_name' => [
						'type' => 'text',
						'rules' => 'string|required|max:64|notIn:permissions,roles',
						'value' => $this->getRelationName()
					]
				]
			]
		];
	}

	private function relateBelongsToManyElements($model, string $relationshipMethod, $toRelate)
	{
		if((is_string($toRelate))||(is_null($toRelate)))
			$toRelate = [$toRelate];

		$relation = $model->{$relationshipMethod}();

		if($pivotClass = $relation->getPivotClass())
		{
			if(in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($pivotClass)))
			{
				$alreadyRelated = $relation->withPivot(['id'])->get();

				$pivotToRemove = $alreadyRelated->map(function($item) use($toRelate)
				{
					if(! in_array($item->getKey(), $toRelate))
					{
						return $item->pivot->getKey();
					}
				});

				$pivotClass::whereIn($pivotClass::make()->getKeyName(), $pivotToRemove)->update(
					[
						'deleted_at' => Carbon::now()
					]
				);
			}
		}

		if((count($toRelate) == 1)&&($toRelate[0] == null))
			return ;

		$model->{$relationshipMethod}()->sync($toRelate);
	}

	public function renderValueForView($value) : ?string
	{
		$model = $this->getDossierrow()->getDossierable();
		
		$relationName = $this->getRelationName();

		$relationType = CrudModelAssociatorHelper::getRelationTypeName(
				$model,
				$relationName
			);

		// $relatedModelPrimaryKeyName = $model->{$relationName}()->make()->getKeyName();

		if($relationType == 'BelongsToMany')
		{
			if(! $this->isMultiple())
				return $model->{$relationName}()->first()->getName();

			return $model->{$relationName}()->get()->implode('name', '<br />');
		}

		if($relationType == 'BelongsTo')
		{
			return $model->{$relationName}()->first()->getName();
		}

		dd('CHIAMA DAVIDE qua c\'è una relazione di tipo ' . $relationType);
	}

	public function storeDossierrow(Dossierrow $dossierrow, mixed $value, bool $validate = false) : bool
	{
		$model = $dossierrow->getDossierable();
		$relationName = $this->getRelationName();

		$relationType = CrudModelAssociatorHelper::getRelationTypeName(
				$model,
				$relationName
			);

		$standardAssociationMethod = 'relate' . $relationType . 'Elements';

		$this->$standardAssociationMethod(
			$model,
			$relationName,
			$value
		);

		$model->save();

		return true;
	}
}