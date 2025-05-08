<?php

namespace IlBronza\FileCabinet\Providers\FieldsGroups;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;
use IlBronza\FileCabinet\Models\Form;

class FormMoveFieldsGroupParametersFile extends FieldsGroupParametersFile
{
	static function getFieldsGroup() : array
	{
		return [
            'translationPrefix' => 'filecabinet::fields',
            'fields' => 
            [
                'mySelfPrimary' => 'primary',
	            'mySelfMove' => [
		            'type' => 'links.link',
		            'confirmMessage' => 'Sei sicuro di voler spostare la riga in questo form?',
		            'faIcon' => 'up-down-left-right',
		            'function' => 'getMoveUrl'
	            ],
                'name' => 'flat',
                'parent' => 'relations.belongsTo',
                'children' => 'relations.hasMany',
                'description' => 'flat',
                'formrows' => 'relations.hasMany',
                'category' => 'relations.belongsTo',
            ]
        ];
	}
}