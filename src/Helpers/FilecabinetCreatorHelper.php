<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\Category\Models\Category;
use IlBronza\FileCabinet\Models\Filecabinet;
use Illuminate\Database\Eloquent\Model;

class FilecabinetCreatorHelper
{
    static function makeByCategory(Model $model, Category $category) : Filecabinet
    {
        $filecabinet = Filecabinet::make();

        $filecabinet->category()->associate($category);

        return $filecabinet;
    }

    static function createByCategory(Model $model, Category $category) : Filecabinet
    {
        $filecabinet = static::makeByCategory($model, $category);

        $model->filecabinets()->save($filecabinet);

        return $filecabinet;
    }

    static function createByCategoryAndParent(Model $model, Category $category, Filecabinet $parent = null) : Filecabinet
    {
        $filecabinet = static::makeByCategory($model, $category);

        if($parent)
            $filecabinet->parent()->associate($parent);

        $model->filecabinets()->save($filecabinet);

        return $filecabinet;
    }
}