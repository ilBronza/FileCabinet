<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\Category\Helpers\HierarchicalCategoryExtractorHelper;
use IlBronza\FileCabinet\Helpers\AttachByCategoryHelper;
use IlBronza\FileCabinet\Helpers\FileCabinetTreeBuilderHelper;
use IlBronza\FileCabinet\Helpers\FilecabinetCreatorHelper;
use IlBronza\FileCabinet\Helpers\FilecabinetNodeCollection;
use IlBronza\FileCabinet\Models\Filecabinet;
use IlBronza\FileCabinet\Models\Form;

class AttachByCategoryTreeHelper extends AttachByCategoryHelper
{
    public FilecabinetNodeCollection $filecabinetNode;
    public Filecabinet $filecabinet;

    public function prepareForms() : static
    {
        $this->forms = HierarchicalCategoryExtractorHelper::extractRecursiveCategorizables(
                $this->getCategory(),
                $this->getCategorizablesArray()
            );

        return $this;
    }

    public function addFormToFilecabinet(Filecabinet $filecabinet, Form $form)
    {
        $dossier = $this->attachFormToModel($form);

        $filecabinet->dossiers()->save($dossier);
    }

    public function _attachFormsByNode(FilecabinetNodeCollection $node, Filecabinet $parentFilecabinet = null) : Filecabinet
    {
        $filecabinet = FilecabinetCreatorHelper::createByCategoryAndParent(
            $this->getModel(),
            $node->getCategory(),
            $parentFilecabinet
        );

        if($filecabinetTemplate = $this->getFilecabinetTemplate())
        {
            $filecabinet->filecabinetTemplate()->associate($filecabinetTemplate);

            $filecabinet->save();
        }

        foreach($node->getFormElements() as $form)
            $this->addFormToFilecabinet(
                $filecabinet,
                $form
            );

        foreach($node->getChildrenCategories()->sortBy('category.sorting_index') as $childNode)
            $this->_attachFormsByNode($childNode, $filecabinet);

        return $filecabinet;
    }

    public function getFilecabinet() : Filecabinet
    {
        return $this->filecabinet;
    }

    public function attachForms() : static
    {
        $this->filecabinetNode = $this->getForms();

        $this->filecabinet = $this->_attachFormsByNode($this->filecabinetNode);

        return $this;
    }
}