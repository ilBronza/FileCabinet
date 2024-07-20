<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\Category\Helpers\CategoryExtractorHelper;
use IlBronza\Category\Models\Category;
use IlBronza\FileCabinet\Helpers\DossierCreatorHelper;
use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\FilecabinetTemplate;
use IlBronza\FileCabinet\Models\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

abstract class AttachByCategoryHelper
{
    public array $categorizablesArray = [
        'forms',
        'directForms'
    ];

    public Model $model;
    public Category $category;
    public ? FilecabinetTemplate $filecabinetTemplate;
    public Collection $forms;
    // public Collection $dossiers;

    abstract public function prepareForms() : static;
    abstract public function attachForms() : static;

    public function __construct(Model $model, Category $category)
    {
        $this->setModel($model);
        $this->setCategory($category);
        // $this->initializeDossiers();
    }

    public function getCategorizablesArray() : array
    {
        return $this->categorizablesArray;
    }

    // public function initializeDossiers() : static
    // {
    //     $this->dossiers = collect();

    //     return $this;
    // }

    // public function pushDossier(Dossier $dossier) : static
    // {
    //     $this->dossiers->push($dossier);

    //     return $this;
    // }

    public function attachFormToModel(Form $form, Dossier $parentDossier = null) : Dossier
    {
        return AttachDossierToModelHelper::attachByFormModel(
            $form,
            $this->getModel(),
        );
    }

    public function setModel(Model $model) : static
    {
        $this->model = $model;

        app('filecabinet')->assertInteractsWithModel($model);

        return $this;
    }

    public function getModel() : Model
    {
        return $this->model;
    }

    public function getForms() : Collection
    {
        return $this->forms;
    }

    public function setCategory(Category $category) : static
    {
        $this->category = $category;

        return $this;
    }

    public function setFilecabinetTemplate(FilecabinetTemplate $filecabinetTemplate) : static
    {
        $this->filecabinetTemplate = $filecabinetTemplate;

        return $this;
    }

    public function getFilecabinetTemplate() : ? FilecabinetTemplate
    {
        if(isset($this->filecabinetTemplate))
            return $this->filecabinetTemplate;

        return null;
    }

    public function getCategory() : Category
    {
        return $this->category;
    }

    static function attachByCategory(Model $model, Category $category) : static
    {
        $helper = new static($model, $category);

        return $helper->prepareForms()
                    ->attachForms();
    }

    static function attachByFilecabinetTemplate(Model $model, FilecabinetTemplate $filecabinetTemplate) : static
    {
        $helper = new static(
            $model,
            $filecabinetTemplate->getCategory()
        );

        $helper->setFilecabinetTemplate($filecabinetTemplate);

        return $helper->prepareForms()
                    ->attachForms();
    }
}