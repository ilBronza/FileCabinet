<?php

namespace IlBronza\FileCabinet\Providers\RowTypes;

use IlBronza\Form\Helpers\FieldsetsExtractorHelper;

trait SpecialParametersRulesFieldTrait
{
	public function getRulesFieldSpecialParametersArray()
	{
		return [
			'type' => 'json',
			'value' => $this->getSpecialParametersRuleArray(),
			'fields' => [
				'rule' => ['text' => 'string|required|max:1024'],
			],
			'rules' => 'array|nullable'
		];
	}

	public function getSpecialParametersRuleArray() : array
	{
		$parameters = $this->getModel()->getSpecialParameters();

		return $parameters['rules'] ?? [];
	}

	public function getSpecialParametersUserDefinedRuleValues() : array
	{
		return array_column(
			$this->getSpecialParametersRuleArray(),
			'rule'
		);
	}

	public function mergeSpecialParametersUserDefinedRules(array $defaultRules) : array
	{
		$userDefinedRules = $this->getSpecialParametersUserDefinedRuleValues();

		if(in_array('required', $userDefinedRules))
			if (($key = array_search('nullable', $defaultRules)) !== false)
			    unset($defaultRules[$key]);

		if(in_array('nullable', $userDefinedRules))
			if (($key = array_search('required', $defaultRules)) !== false)
			    unset($defaultRules[$key]);

		return $defaultRules + $userDefinedRules;
	}
}