<?php

namespace IlBronza\FileCabinet\Providers\FieldsGroups;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class DossierrowFieldsGroupParametersFile extends FieldsGroupParametersFile
{
	static function getFieldsGroup() : array
	{
		return [
            'translationPrefix' => 'filecabinet::fields',
            'fields' => 
            [
                'mySelfPrimary' => 'primary',
//                'mySelfEdit' => 'links.edit',
//                'mySelfSee' => 'links.see',

                'name' => 'flat',
                'mySelfValue' => '_fn_getShowValue',

                'dossier' => 'relations.belongsTo',
                'formrow' => 'relations.belongsTo',
                'formrow.type' => 'flat',

//                'mySelfDelete' => 'links.delete'
            ]
        ];
	}
}