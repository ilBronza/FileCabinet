<?php

namespace IlBronza\FileCabinet\Http\Controllers;

use IlBronza\CRUD\BelongsToCRUDController;
use IlBronza\CRUD\Traits\CRUDBelongsToManyTrait;
use IlBronza\CRUD\Traits\CRUDCreateStoreTrait;
use IlBronza\CRUD\Traits\CRUDDeleteTrait;
use IlBronza\CRUD\Traits\CRUDDestroyTrait;
use IlBronza\CRUD\Traits\CRUDEditUpdateTrait;
use IlBronza\CRUD\Traits\CRUDIndexTrait;
use IlBronza\CRUD\Traits\CRUDRelationshipTrait;
use IlBronza\CRUD\Traits\CRUDShowTrait;
use IlBronza\FileCabinet\Http\Controllers\CRUDTraits\CRUDFilecabinetrowParametersTrait;
use IlBronza\FileCabinet\Models\Filecabinet;
use IlBronza\FileCabinet\Models\Filecabinetrow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class CrudFilecabinetrowController extends BelongsToCRUDController
{
    use CRUDFilecabinetrowParametersTrait;

    use CRUDShowTrait;
    use CRUDIndexTrait;
    use CRUDEditUpdateTrait;
    use CRUDCreateStoreTrait;

    use CRUDDeleteTrait;
    use CRUDDestroyTrait;

    use CRUDRelationshipTrait;
    use CRUDBelongsToManyTrait;

    /**
     * subject model class full path
     **/
    public $parentModelClass = Filecabinet::class;
    public $modelClass = Filecabinetrow::class;

    /**
     * http methods allowed. remove non existing methods to get a 403 when called by routes
     **/
    public $allowedMethods = [
        'index',
        'show',
        'edit',
        'update',
        'create',
        'store',
        'destroy',
        'reorder',
        'storereorder'
    ];

    public function index(Request $request, Filecabinet $filecabinet)
    {
        return $this->_index($request);
    }

    public function getIndexElements()
    {
        return $this->parentModel->filecabinetrows()->get();
    }

    public function show(Filecabinet $filecabinet, Filecabinetrow $filecabinetrow)
    {
        return $this->_show($filecabinetrow);
    }

    public function edit(Filecabinet $filecabinet, Filecabinetrow $filecabinetrow)
    {
        return $this->_edit($filecabinetrow);
    }

    public function update(Request $request, Filecabinet $filecabinet, Filecabinetrow $filecabinetrow)
    {
        return $this->_update($request, $filecabinetrow);
    }

    public function store(Request $request, Filecabinet $filecabinet)
    {
        return $this->_store($request);
    }

    public function destroy(Filecabinet $filecabinet, Filecabinetrow $filecabinetrow)
    {
        return $this->_destroy($filecabinetrow);
    }
}

