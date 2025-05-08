<?php

namespace IlBronza\FileCabinet\Providers\FieldsGroups;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class FormrowByFormFieldsGroupParametersFile extends FormrowFieldsGroupParametersFile
{
	static function getFieldsGroup() : array
	{
		return [
			'translationPrefix' => 'filecabinet::fields',
			'fields' => [
				'mySelfPrimary' => 'primary',
				'mySelfEdit' => 'links.edit',
				'mySelfSee' => 'links.see',
				'mySelfCondense' => [
					'type' => 'links.link',
					'faIcon' => 'arrows-to-dot',
					'function' => 'getCondenseIndexUrl'
				],
				'mySelfMove' => [
					'type' => 'links.link',
					'faIcon' => 'up-down-left-right',
					'function' => 'getMoveIndexUrl'
				],
				'name' => 'flat',
				'slug' => 'flat',
				'sorting_index' => 'utilities.sorting',
				'description' => 'flat',
				'required' => 'editor.toggle',
				'type' => 'filecabinet::rowType',

				'mySelfDelete' => 'links.delete'
			]
		];
	}
}