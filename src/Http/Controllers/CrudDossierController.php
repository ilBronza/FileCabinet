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
use IlBronza\FileCabinet\Models\Dossier;
use Illuminate\Http\Request;
use IlBronza\FileCabinet\Http\Controllers\CRUDTraits\CRUDDossierParametersTrait;

class CrudDossierController extends CRUD
{
    use CRUDDossierParametersTrait;

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
    public $modelClass = Dossier::class;

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

    /**
     * to override show view use full view name
     **/
    //public $showView = 'products.showPartial';

    // public $guardedEditDBFields = ['id', 'created_at', 'updated_at', 'deleted_at'];
    // public $guardedCreateDBFields = ['id', 'created_at', 'updated_at', 'deleted_at'];
    // public $guardedShowDBFields = ['id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * relations called to be automatically shown on 'show' method
     **/
    //public $showMethodRelationships = ['posts', 'users', 'operations'];

    /**
        protected $relationshipsControllers = [
        'permissions' => '\IlBronza\AccountManager\Http\Controllers\PermissionController'
    ];
    **/


    /**
     * getter method for 'index' method.
     *
     * is declared here to force the developer to rationally choose which elements to be shown
     *
     * @return Collection
     **/
    public function getIndexElements()
    {
        return Dossier::all();
    }

    /**
     * START base methods declared in extended controller to correctly perform dependency injection
     *
     * these methods are compulsorily needed to execute CRUD base functions
     **/
    public function show(Dossier $dossier)
    {
        //$this->addExtraView('top', 'folder.subFolder.viewName', ['some' => $thing]);

        return $this->_show($dossier);
    }

    public function edit(Dossier $dossier)
    {
        return $this->_edit($dossier);
    }

    public function update(Request $request, Dossier $dossier)
    {
        return $this->_update($request, $dossier);
    }

    public function destroy(Dossier $dossier)
    {
        return $this->_destroy($dossier);
    }

    /**
     * END base methods
     **/





}

