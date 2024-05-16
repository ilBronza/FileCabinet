<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\Category\Models\Category;
use Illuminate\Support\Collection;

class FilecabinetNodeCollection extends Collection
{
    public Collection $formElements;
    public Collection $childrenCategories;
    public Category $category;

    public function setFormElements(Collection $formElements) : static
    {
        $this->formElements = $formElements;

        return $this;
    }

    public function getFormElements() : Collection
    {
        return $this->formElements;
    }

    public function setChildrenCategories(Collection $childrenCategories) : static
    {
        $this->childrenCategories = $childrenCategories;

        return $this;
    }

    public function getChildrenCategories() : Collection
    {
        return $this->childrenCategories;
    }

    public function setCategory(Category $category) : static
    {
        $this->category = $category;

        return $this;
    }

    public function getCategory() : Category
    {
        return $this->category;
    }
}