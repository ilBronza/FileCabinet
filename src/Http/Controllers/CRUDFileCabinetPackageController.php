<?php

namespace IlBronza\FileCabinet\Http\Controllers;

use IlBronza\CRUD\CRUD;
use IlBronza\CRUD\Http\Controllers\BasePackageTrait;

class CRUDFileCabinetPackageController extends CRUD
{
    use BasePackageTrait;

    static $packageConfigPrefix = 'filecabinet';
}
