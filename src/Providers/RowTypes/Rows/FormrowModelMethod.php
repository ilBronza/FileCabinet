<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Providers\RowTypes\BaseRow;
use IlBronza\FileCabinet\Providers\RowTypes\FormrowWithSpecialParametersInterface;
use IlBronza\FileCabinet\Providers\RowTypes\SpecialParametersRulesFieldTrait;
use IlBronza\FileCabinet\Providers\RowTypes\SpecialParametersTrait;
use IlBronza\FileCabinet\Providers\RowTypes\StandardCheckFieldValidityParametersTrait;
use IlBronza\FormField\Fields\TextFormField;
use IlBronza\FormField\FormField;

class FormrowModelMethod extends BaseRow implements FormrowWithSpecialParametersInterface
{
	use StandardCheckFieldValidityParametersTrait;

	use SpecialParametersTrait;
	use SpecialParametersRulesFieldTrait;

	static $fieldType = 'text';

	public function getFormField() : FormField
	{
		return new TextFormField();
	}

	public function storeDossierrow(Dossierrow $dossierrow, mixed $value, bool $validate = false) : bool
	{
		dd('qua da capire cosa fare con il setter se esiste');
		// $model = $dossierrow->getDossierable();
		// $fieldname = $this->getFieldname();

		// $model->{$fieldname} = $value;
		// $model->save();

		// return true;
	}

	public function getValidationRulesArrayFromSpecialParametersArray() : array
	{
		return [];
	}

	public function getDefaultRules() : array
	{
		return $this->mergeSpecialParametersUserDefinedRules([]);
	}

	public function getDossierrowValue(Dossierrow $dossierrow)
	{
		$model = $dossierrow->getDossierable();
		$methodName = $this->getSpecialParametersSingleAttributeValue('read_method');

		return $this->transformValue(
			$model->{$methodName}()
		);
	}

	public function getSpecialParametersFieldsetParameters() : array
	{
		return [
			'parameters' => [
				'translationPrefix' => 'filecabinet::fields',
				'fields' => [
					'read_method' => [
						'type' => 'text',
						'multiple' => false,
						'value' => $this->getSpecialParametersSingleAttributeValue('read_method'),
						'rules' => 'string|nullable',
					],
					'write_method' => [
						'type' => 'text',
						'multiple' => false,
						'value' => $this->getSpecialParametersSingleAttributeValue('write_method'),
						'rules' => 'string|nullable',
					],
					'rules' => $this->getRulesFieldSpecialParametersArray()
				]
			]
		];
	}

}