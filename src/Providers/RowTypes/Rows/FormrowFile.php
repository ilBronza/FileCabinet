<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\CRUD\Helpers\ModelHelpers\ModelSchemaHelper;
use IlBronza\FileCabinet\Helpers\MediaPathGenerators\MediaPathGeneratorIdFolder;
use IlBronza\FileCabinet\Helpers\MediaPathGenerators\MediaPathGeneratorIdSingleCharFolder;
use IlBronza\FileCabinet\Helpers\MediaPathGenerators\MediaPathGeneratorNameFolder;
use IlBronza\FileCabinet\Helpers\MediaPathGenerators\MediaPathGeneratorSingleFolder;
use IlBronza\FileCabinet\Helpers\MediaPathGenerators\MediaPathGeneratorSlugFolder;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Providers\RowTypes\BaseRow;
use IlBronza\FileCabinet\Providers\RowTypes\FormrowWithSpecialParametersInterface;
use IlBronza\FileCabinet\Providers\RowTypes\Rows\ModelDatabaseTypes\DatabaseTypeHelper;
use IlBronza\FileCabinet\Providers\RowTypes\SpecialParametersRulesFieldTrait;
use IlBronza\FileCabinet\Providers\RowTypes\SpecialParametersTrait;
use IlBronza\FormField\Fields\FileFormField;
use IlBronza\FormField\Fields\SelectFormField;
use IlBronza\FormField\Fields\TextFormField;
use IlBronza\FormField\FormField;
use Illuminate\Database\Eloquent\Model;

use function __;
use function array_keys;
use function config;
use function implode;

class FormrowFile extends BaseRow implements FormrowWithSpecialParametersInterface
{
	use SpecialParametersTrait;

	static $fieldType = 'file';
	static $databaseField = 'string';

	public function getDefaultRules() : array
	{
		return [
			'string'
		];
	}

//	public function getPossibleValuesArray() : array
//	{
//		$array = $this->getModel()->getSpecialParameter('possibleValues', null);
//
//		$result = [];
//
//		foreach($array as $element)
//			$result[$element['value']] = $element['value'];
//
//		return $result;
//	}

	protected function getAvailableDisks() : array
	{
		$result = [];

		foreach(config('app.filecabinet.disks', []) as $disk)
			$result[$disk] = $disk;

		return $result;
	}

	protected function getAvailableFolderTypes() : array
	{
		$possibleValues = config('filecabinet.media.mediaPathGenerators', []);

		$result = [];

		foreach($possibleValues as $key => $class)
			$result[$key] = __('filecabinet::mediaPathGenerators.' . $key);

		return $result;
	}

	public function getValidationRulesArrayFromSpecialParametersArray() : array
	{
		return [
			'file'
		];
	}

	protected function getAvailableNameTypes() : array
	{
		$possibleValues = config('filecabinet.media.mediaNameGenerators', []);

		$result = [];

		foreach($possibleValues as $key => $class)
			$result[$key] = __('filecabinet::mediaNameGenerators.' . $key);

		return $result;
	}

	public function getSpecialParametersFieldsetParameters() : array
	{
		return [
			'parameters' => [
				'translationPrefix' => 'filecabinet::fields',
				'fields' => [
					'disk' => [
						'type' => 'select',
						'multiple' => false,
						'rules' => 'string|required|in:' . implode(',', array_keys($this->getAvailableDisks())),
						'list' => $this->getAvailableDisks(),
						'value' => $this->getModel()->getSpecialParameter('disk', null)
					],
					'folderType' => [
						'type' => 'select',
						'multiple' => false,
						'rules' => 'string|required|in:' . implode(',', array_keys($this->getAvailableFolderTypes())),
						'list' => $this->getAvailableFolderTypes(),
						'value' => $this->getModel()->getSpecialParameter('folderType', null)
					],
					'folderName' => [
						'type' => 'text',
						'rules' => 'required_if:folderType,==,singleFolder|nullable|string|max:255',
						'value' => $this->getModel()->getSpecialParameter('folderName', null)
					],
					'folderPrefix' => [
						'type' => 'text',
						'rules' => 'nullable|string|max:255',
						'value' => $this->getModel()->getSpecialParameter('folderPrefix', null)
					],
					'folderSuffix' => [
						'type' => 'text',
						'rules' => 'nullable|string|max:255',
						'value' => $this->getModel()->getSpecialParameter('folderSuffix', null)
					],
					'nameType' => [
						'type' => 'select',
						'multiple' => false,
						'rules' => 'string|required|in:' . implode(',', array_keys($this->getAvailableNameTypes())),
						'list' => $this->getAvailableNameTypes(),
						'value' => $this->getModel()->getSpecialParameter('nameType', null)
					],
					'nameValueRule' => [
						'type' => 'text',
						'rules' => 'required_if:nameType,==,dossierValueRules|nullable|string|max:1024',
						'value' => $this->getModel()->getSpecialParameter('nameValueRule', null)
					],
					'filePrefix' => [
						'type' => 'text',
						'rules' => 'nullable|string|max:255',
						'value' => $this->getModel()->getSpecialParameter('filePrefix', null)
					],
					'fileSuffix' => [
						'type' => 'text',
						'rules' => 'nullable|string|max:255',
						'value' => $this->getModel()->getSpecialParameter('fileSuffix', null)
					],
				]
			]
		];
	}

	public function getFormField() : FormField
	{
		return new FileFormField();
	}
}