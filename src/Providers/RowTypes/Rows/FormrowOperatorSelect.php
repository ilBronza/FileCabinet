<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\AccountManager\Models\Role;
use IlBronza\AccountManager\Models\User;
use IlBronza\CRUD\Helpers\QueryHelpers\ModelSelectQueryHelper;
use IlBronza\FormField\Fields\SelectFormField;
use IlBronza\FormField\FormField;

class FormrowOperatorSelect extends FormrowSingleSelect
{
	static $databaseField = 'text';

	public function getDefaultRules() : array
	{
		return [
			'string'
		];
	}

	public function getShowValue(mixed $databaseValue) : mixed
	{
		return User::getProjectClassName()::findCached($databaseValue)?->getFullName();
	}

	public function getPossibleValuesArray() : array
	{
		if (! $rolesIds = $this->getModel()->getSpecialParameter('roles', []))
			return ModelSelectQueryHelper::getArrayForSelect(User::getProjectClassName());

		return ModelSelectQueryHelper::getArrayForSelectWithScopes(
			User::getProjectClassName(), ['byRolesIds' => $rolesIds]
		);
	}

	public function getSpecialParametersFieldsetParameters() : array
	{
		$rolesList = ModelSelectQueryHelper::getArrayForSelect(Role::getProjectClassName());

		return [
			'parameters' => [
				'translationPrefix' => 'filecabinet::fields',
				'fields' => [
					'roles' => [
						'type' => 'select',
						'multiple' => true,
						'rules' => 'array|nullable|in:' . implode(',', array_keys($rolesList)),
						'list' => $rolesList,
						'value' => $this->getModel()->getSpecialParameter('roles', [])
					]
				]
			]
		];
	}

	public function getFormField() : FormField
	{
		return new SelectFormField();
	}
}