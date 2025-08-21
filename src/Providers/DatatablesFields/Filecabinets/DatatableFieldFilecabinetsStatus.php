<?php

namespace IlBronza\FileCabinet\Providers\DatatablesFields\Filecabinets;

use IlBronza\Datatables\DatatablesFields\Relations\DatatableFieldHasMany;

class DatatableFieldFilecabinetsStatus  extends DatatableFieldHasMany
{
    public function _transformValue($value)
    {
        return [
            'id' => $value->getKey(),
            'name' => $value->getStatusString()
        ];
    }

}