<?php

namespace IlBronza\FileCabinet\Providers\FieldsetsParameters;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

use function array_keys;
use function config;
use function implode;

class FilecabinetTemplateCreateStoreFieldsetsParameters extends FieldsetParametersFile
{
	public function getModelsArray() : array
	{
		return config('app.filecabinet.filecabinetTemplate.applicableToModels');
	}

	public function getEventsArray() : array
	{
		return config('app.filecabinet.filecabinetTemplate.events', [
			'created' => 'created',
			'updated' => 'updated',
			'saved' => 'saved'
		]);
	}

	public function _getFieldsetsParameters() : array
	{
		return [
			'package' => [
				'translationPrefix' => 'filecabinet::fields',
				'fields' => [
					'category' => [
						'type' => 'select',
						'multiple' => false,
						'mustBeSorted' => false,
						'rules' => 'nullable|exists:' . config('category.models.category.table') . ',id',
						'relation' => 'category'
					],


					'models' => [
						'type' => 'json',
						'fields' => [
							'model' => [
								'type' => 'select',
								'multiple' => false,
								'select2' => false,
								'rules' => 'string|nullable|in:' . implode(',', array_keys($this->getModelsArray())),
								'possibleValuesArray' => $this->getModelsArray(),
								'roles' => ['superadmin', 'administrator']
							],
							'event' => [
								'type' => 'select',
								'multiple' => false,
								'select2' => false,
								'rules' => 'string|nullable|in:' . implode(',', array_keys($this->getEventsArray())),
								'possibleValuesArray' => $this->getEventsArray(),
								'roles' => ['superadmin', 'administrator']
							]
						],
						'rules' => 'array|nullable',
					],

					// 'name' => ['text' => 'string|required|max:255'],
					// 'form' => [
					//     'type' => 'select',
					//     'multiple' => false,
					//     'rules' => 'nullable|exists:' . config('filecabinet.models.form.table') . ',id',
					//     'relation' => 'form'
					// ],
					// 'dossierable' => [
					//     'type' => 'select',
					//     'multiple' => false,
					//     'rules' => 'nullable',
					//     'relation' => 'dossierable'
					// ],
					// 'populator' => [
					//     'type' => 'select',
					//     'multiple' => false,
					//     'rules' => 'nullable',
					//     'relation' => 'populator'
					// ],
					// 'populated_at' => ['datetime' => 'date|nullable'],
					// 'must_be_updated' => ['boolean' => 'bool'],
				],
				'width' => ['xlarge@m']
			],
			'pdfSettings' => [
				'translationPrefix' => 'filecabinet::fields',
				'fields' => [
					'pdf_image' => [
						'type' => 'file',
						'multiple' => false,
						'rules' =>'file|nullable|max:255'
					],
					'pdf_title' => ['textarea' => 'string|nullable|max:255'],
					'pdf_description' => [
						'type' => 'texteditor',
						'max' => '2048',
						'tooltip' => 'Leave empty',
						'rules' => 'string|nullable'
					],
					'pdf_show_menu' => ['boolean' => 'bool|required'],
					'pdf_print_fields_when_empty' => ['boolean' => 'bool|required'],
				],
				'width' => ['large@m']
			]
		];
	}
}
