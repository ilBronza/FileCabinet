<?php

namespace IlBronza\FileCabinet\Providers\FieldsGroups;

use IlBronza\Datatables\Providers\FieldsGroupParametersFile;

class DossierrowRelatedFieldsGroupParametersFile extends DossierrowFieldsGroupParametersFile
{
	static function getFieldsGroup() : array
	{
        $result = parent::getTracedFieldsGroup();

        unset($result['fields']['dossier']);

        return $result;
	}
}