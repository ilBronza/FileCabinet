<?php

namespace IlBronza\FileCabinet\Providers\FieldsetsParameters;

use IlBronza\AccountManager\Models\Permission;
use IlBronza\AccountManager\Models\Role;
use IlBronza\FileCabinet\Providers\RowTypes\FormrowNamesTypeHelper;
use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;

class FormrowCreateStoreFieldsetsParameters extends FieldsetParametersFile
{
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

	protected function getPossiblePermissions() : array
	{
		return Permission::getProjectClassName()::all()->pluck('name', 'id')->toArray();
	}

	public function _getFieldsetsParameters() : array
	{
		$rowTypes = FormrowNamesTypeHelper::getPossibleTypesArray();

		return [
			'package' => [
				'translationPrefix' => 'filecabinet::fields',
				'fields' => [
					'name' => ['text' => 'string|required|max:255'],
					'slug' => ['text' => 'string|nullable|max:255'],
					'required' => ['boolean' => 'bool|required'],
					'repeatable' => ['boolean' => 'bool|required'],
					'read_only' => ['boolean' => 'bool|required'],
					'sorting_index' => [
						'type' => 'number',
						'default' => $this->getModel()->getForm()->getFormrowsMaxSortingIndex() + 1,
						'rules' => 'integer|required|min:0|max:65535'
					],
					'description' => ['texteditor' => 'string|nullable|max:2048'],
					'type' => [
						'type' => 'select',
						'multiple' => false,
						'select2' => false,
						'list' => $rowTypes,
						'rules' => 'string|required|in:' . implode(",", array_keys($rowTypes))
					],

					'roles' => [
						'type' => 'json',
						'fields' => [
							'action' => [
								'type' => 'select',
								'multiple' => false,
								'select2' => false,
								'list' => $this->getPossibleActions(),
								'rules' => 'string|nullable|in:' . implode(",", array_keys($this->getPossibleActions()))
							],
							'roles' => [
								'type' => 'select',
								'multiple' => true,
								'select2' => false,
								'list' => $this->getPossibleRoles(),
								'rules' => 'string|nullable|in:' . implode(",", array_keys($this->getPossibleRoles()))
							],
						],
						'rules' => 'array|nullable',
					],

					'permissions' => [
						'type' => 'json',
						'fields' => [
							'action' => [
								'type' => 'select',
								'multiple' => false,
								'select2' => false,
								'list' => $this->getPossibleActions(),
								'rules' => 'string|nullable|in:' . implode(",", array_keys($this->getPossibleActions()))
							],
							'roles' => [
								'type' => 'select',
								'multiple' => true,
								'select2' => false,
								'list' => $this->getPossiblePermissions(),
								'rules' => 'string|nullable|in:' . implode(",", array_keys($this->getPossiblePermissions()))
							],
						],
						'rules' => 'array|nullable',
					],
				]
			]
		];
	}
}
