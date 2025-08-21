<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\Category\Helpers\FlattenedCategoryExtractorHelper;
use IlBronza\FileCabinet\Helpers\AttachByCategoryHelper;

class AttachByFlattenedCategoryHelper extends AttachByCategoryHelper
{
    public function prepareForms() : static
    {
        $this->forms = FlattenedCategoryExtractorHelper::extractRecursiveCategorizables(
                $this->getCategory(),
                $this->getCategorizablesArray()
            );

        return $this;
    }

    public function attachForms() : static
    {
        foreach($this->getForms() as $form)
            $this->attachFormToModel($form);

        // foreach($this->getForms() as $form)
        //     $this->pushDossier(
        //         $this->attachForm($form)
        //     );

        return $this;
    }
}