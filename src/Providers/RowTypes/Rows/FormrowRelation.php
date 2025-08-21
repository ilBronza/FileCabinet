<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use Carbon\Carbon;
use IlBronza\CRUD\Helpers\ModelManagers\CrudModelAssociatorHelper;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Providers\RowTypes\BaseModelRelationRow;
use IlBronza\FileCabinet\Providers\RowTypes\StandardCheckFieldValidityParametersTrait;

class FormrowRelation extends BaseModelRelationRow
{
	use StandardCheckFieldValidityParametersTrait;

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

		dd('qua c\'è una relazione di tipo ' . $relationType);
	}

	public function getRelationName() : ? string
	{
		return $this->getModel()->getSpecialParameter('relation_name', null);
	}


	public function getValidationRulesArrayFromSpecialParametersArray() : array
	{
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