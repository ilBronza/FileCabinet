<?php

namespace IlBronza\FileCabinet\Providers\DatatablesFields\AttachForm;

use IlBronza\Category\Models\Category;
use IlBronza\Datatables\DatatablesFields\Iterators\DatatableFieldIterator;

class DatatableFieldAttachFormByRoots extends DatatableFieldIterator
{
	public $faIcon = 'box-archive';
	public Category $category;

	/** declare this to true to get the correct javascript array management in datatables cell construction **/
	public $textParameter = true;

	public function __transformValue($value, Category $category)
	{
		if(! $value)
			return [null, null];

		return [
			$value->getAttachFormByCategoryUrl($category),
			$category->getName()
		];
	}

	public function transformValue($value)
	{
        if(! $value)
            return ;

        $result = [];

        $categories = Category::getProjectClassname()::getRoots();

        foreach($categories as $category)
            $result[] = $this->__transformValue($value, $category);

        return [
            'separator' => $this->separator,
            'elements' => $result,

        ];
	}

    public function getFilterColumnDef()
    {
        return "
            let result = '';

            Object.keys(elements).forEach(key => {
                result = result + elements[key][1];
            });
        ";
    }

    public function getDisplayColumnDef()
    {
        // if(isset($this->table->modelClass))
        //     return "

        //         let result = '';
        //         let urlRelation = '" . $this->getRelationModelSprintFShowRoute() . "';

        //         urlRelation = urlRelation.replace('%f', data.father);

        //         Object.keys(elements).forEach(key => {

        //             result = result + '<a href=\"' + urlRelation.replace('%s', elements[key].id) + '\">' + elements[key].name + '</a><br />';
        //         });
        //     ";

        return "
            let result = '';

            Object.keys(elements).forEach(key => {

                result = result + '<a href=\"' + elements[key][0] + '\">' + elements[key][1] + '</a><br />';
            });
        ";
    }

}