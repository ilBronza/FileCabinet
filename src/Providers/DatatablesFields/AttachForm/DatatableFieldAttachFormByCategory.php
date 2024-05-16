<?php

namespace IlBronza\FileCabinet\Providers\DatatablesFields\AttachForm;

use IlBronza\Category\Models\Category;
use IlBronza\Datatables\DatatablesFields\Links\DatatableFieldLink;

class DatatableFieldAttachFormByCategory  extends DatatableFieldLink
{
	public $faIcon = 'box-archive';
	public Category $category;

	/** declare this to true to get the correct javascript array management in datatables cell construction **/
	public $textParameter = true;

	public function transformValue($value)
	{
		if(! $value)
			return [null, null];

		return [
			$value->getAttachFormByCategoryUrl($this->category),
			$this->category->getName()
		];
	}	
}