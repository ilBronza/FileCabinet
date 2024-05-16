<?php

namespace IlBronza\FileCabinet\Providers\DatatablesFields;

use IlBronza\Datatables\DatatablesFields\DatatableField;
use IlBronza\FileCabinet\Providers\RowTypes\FormrowNamesTypeHelper;

class DatatableFieldRowType  extends DatatableField
{
	public function transformValue($value)
	{
		if(! $value)
			return ;

		return FormrowNamesTypeHelper::getTranslatedNameByType($value);
	}	
}