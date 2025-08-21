<?php

namespace IlBronza\FileCabinet\Http\Controllers\Formrows;

use IlBronza\CRUD\Traits\CRUDFlatSortingTrait;
use Illuminate\Http\Request;

class FormrowReorderController extends FormrowCRUD
{
	use CRUDFlatSortingTrait;

    public $allowedMethods = ['storeMassReorder'];
}
