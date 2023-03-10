<?php

namespace IlBronza\FileCabinet\Http\Controllers;

use IlBronza\CRUD\CRUD;
use IlBronza\CRUD\Traits\CRUDBelongsToManyTrait;
use IlBronza\CRUD\Traits\CRUDCreateStoreTrait;
use IlBronza\CRUD\Traits\CRUDDeleteTrait;
use IlBronza\CRUD\Traits\CRUDDestroyTrait;
use IlBronza\CRUD\Traits\CRUDEditUpdateTrait;
use IlBronza\CRUD\Traits\CRUDIndexTrait;
use IlBronza\CRUD\Traits\CRUDPlainIndexTrait;
use IlBronza\CRUD\Traits\CRUDRelationshipTrait;
use IlBronza\CRUD\Traits\CRUDShowTrait;
use IlBronza\CRUD\Traits\CRUDUpdateEditorTrait;
use IlBronza\FileCabinet\Http\Controllers\CRUDTraits\CRUDFilecabinetParametersTrait;
use IlBronza\FileCabinet\Models\Filecabinet;
use IlBronza\FileCabinet\Providers\RelationshipsManagers\FilecabinetRelationManager;
use Illuminate\Http\Request;

class CrudFilecabinetController extends CRUD
{
    use CRUDFilecabinetParametersTrait;

    use CRUDShowTrait;
    use CRUDPlainIndexTrait;
    use CRUDIndexTrait;
    use CRUDEditUpdateTrait;
    use CRUDUpdateEditorTrait;
    use CRUDCreateStoreTrait;

    use CRUDDeleteTrait;
    use CRUDDestroyTrait;

    use CRUDRelationshipTrait;
    use CRUDBelongsToManyTrait;

    /**
     * subject model class full path
     **/
    public $modelClass = Filecabinet::class;

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
        'destroy'
    ];

    public $relationshipsManagerClass = FilecabinetRelationManager::class;

    /**
     * getter method for 'index' method.
     *
     * is declared here to force the developer to rationally choose which elements to be shown
     *
     * @return Collection
     **/
    public function getIndexElements()
    {
        return Filecabinet::all();
    }

    /**
     * START base methods declared in extended controller to correctly perform dependency injection
     *
     * these methods are compulsorily needed to execute CRUD base functions
     **/
    public function show(Filecabinet $filecabinet)
    {
        //$this->addExtraView('top', 'folder.subFolder.viewName', ['some' => $thing]);

        return $this->_show($filecabinet);
    }

    public function edit(Filecabinet $filecabinet)
    {
        return $this->_edit($filecabinet);
    }

    public function update(Request $request, Filecabinet $filecabinet)
    {
        return $this->_update($request, $filecabinet);
    }

    public function destroy(Filecabinet $filecabinet)
    {
        return $this->_destroy($filecabinet);
    }

}

