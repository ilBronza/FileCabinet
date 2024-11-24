<?php

namespace IlBronza\FileCabinet\Providers\RowTypes\Rows;

use IlBronza\AccountManager\Models\Role;
use IlBronza\AccountManager\Models\User;
use IlBronza\CRUD\Helpers\QueryHelpers\ModelSelectQueryHelper;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Providers\RowTypes\BaseModelRelationRow;
use IlBronza\FileCabinet\Providers\RowTypes\StandardCheckFieldValidityParametersTrait;
use IlBronza\Operators\Models\Operator;

class FormrowOperatorSelect extends BaseModelRelationRow
{
	public ? bool $multiple = false;
	
	use StandardCheckFieldValidityParametersTrait;

	public function getShowValue(mixed $databaseValue) : mixed
	{
		return User::getProjectClassName()::findCached($databaseValue)?->getFullName();
	}

	public function getPossibleValuesArray() : array
	{
		if (! $rolesIds = $this->getModel()->getSpecialParameter('roles', []))
			return Operator::gpc()::getSelfPossibleList();

		$userIdArray = User::gpc()::select('id')->byRolesIds($rolesIds)->get();

		Operator::with('user.userdata')->whereIn('user_id', $userIdArray)->get();

		return Operator::gpc()::buildElementsArryForSelect($elements);
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
}