<?php

namespace IlBronza\FileCabinet\Providers\FieldsetsParameters;

use IlBronza\AccountManager\Models\Permission;
use IlBronza\AccountManager\Models\Role;
use IlBronza\FileCabinet\Providers\RowTypes\FormrowNamesTypeHelper;
use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

use function array_keys;
use function implode;

class FormrowCreateStoreFieldsetsParameters extends FieldsetParametersFile
{
	public function _getFieldsetsParameters() : array
	{
		$rowTypes = FormrowNamesTypeHelper::getPossibleTypesArray();

		return [
			'baseData' => [
				'translationPrefix' => 'filecabinet::fields',
				'fields' => [
					'name' => ['text' => 'string|required|max:255'],
					'slug' => ['text' => 'string|nullable|max:255'],

					'type' => [
						'type' => 'select',
						'multiple' => false,
						'select2' => false,
						'list' => $rowTypes,
						'rules' => 'string|required|in:' . implode(',', array_keys($rowTypes))
					],

					'required' => ['boolean' => 'bool|required'],
					'repeatable' => ['boolean' => 'bool|required'],
					'read_only' => ['boolean' => 'bool|required'],

					'default_value_getter_method' => ['text' => 'string|nullable|max:64'],
				],
				'width' => ['large@l']
			],

			'appearence' => [
				'translationPrefix' => 'filecabinet::fields',
				'fields' => [
					'sorting_index' => [
						'type' => 'number',
						'default' => $this->getModel()->getForm()->getFormrowsMaxSortingIndex() + 1,
						'rules' => 'integer|required|min:0|max:65535'
					],
					'description' => [
						'type' => 'texteditor',
						'rules' => 'string|nullable|max:2048',
						'vertical' => true
					],
					'placeholder' => ['text' => 'string|nullable|max:64'],
					'placeholder_getter_method' => ['text' => 'string|nullable|max:64'],
				],
				'width' => ['large@l']
			],
			'pdfSettings' => [
				'translationPrefix' => 'filecabinet::fields',
				'fields' => [
					'pdf_title' => ['textarea' => 'string|nullable|max:255'],
					'pdf_description' => [
						'type' => 'texteditor',
						'tooltip' => 'Leave empty',
						'vertical' => true,
						'max' => '2048',
						'rules' => 'string|nullable'
					],
					'pdf_show_menu' => ['boolean' => 'bool|nullable'],
					'pdf_print_fields_when_empty' => ['boolean' => 'bool|nullable'],

					'pdf_print_label' => ['boolean' => 'bool|nullable'],
					'pdf_label' => ['text' => 'string|nullable|max:255'],
				],
				'width' => ['large@l']
			],
			'rolesAndPermissions' => [
				'translationPrefix' => 'filecabinet::fields',
				'fields' => [
					'roles' => [
						'type' => 'json',
						'value' => $this->getRolesValue(),
						'fields' => [
							'action' => [
								'translatedLabel' => 'masscio',
								'type' => 'select',
								'multiple' => false,
								'select2' => false,
								'list' => $this->getPossibleActions(),
								'rules' => 'string|nullable|in:' . implode(',', array_keys($this->getPossibleActions()))
							],
							'roles' => [
								'type' => 'select',
								'multiple' => true,
								'select2' => false,
								'list' => $this->getPossibleRoles(),
								'rules' => 'array|nullable|in:' . implode(',', array_keys($this->getPossibleRoles()))
							],
						],
						'rules' => 'array|nullable',
					],

					'permissions' => [
						'type' => 'json',
						'value' => $this->getPermissionsValue(),
						'fields' => [
							'action' => [
								'type' => 'select',
								'multiple' => false,
								'select2' => false,
								'list' => $this->getPossibleActions(),
								'rules' => 'string|nullable|in:' . implode(',', array_keys($this->getPossibleActions()))
							],
							'permissions' => [
								'type' => 'select',
								'multiple' => true,
								'select2' => false,
								'list' => $this->getPossiblePermissions(),
								'rules' => 'array|nullable|in:' . implode(',', array_keys($this->getPossiblePermissions()))
							],
						],
						'rules' => 'array|nullable',
					],
				],
				'width' => ['large@l']
			]
		];
	}

	public function getRolesValue()
	{
		return $this->getModel()->getRolesParameters();
	}

	protected function getPossibleActions()
	{
		return [
			'edit' => trans('filecabinet::actions.edit'),
			'create' => trans('filecabinet::actions.create'),
			'delete' => trans('filecabinet::actions.delete'),
		];
	}

	protected function getPossibleRoles() : array
	{
		return Role::getProjectClassName()::all()->pluck('name', 'id')->toArray();
	}

	public function getPermissionsValue()
	{
		return $this->getModel()->getPermissionsParameters();
	}

	protected function getPossiblePermissions() : array
	{
		return Permission::getProjectClassName()::all()->pluck('name', 'id')->toArray();
	}
}
