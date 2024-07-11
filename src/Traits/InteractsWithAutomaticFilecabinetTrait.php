<?php

namespace IlBronza\FileCabinet\Traits;

use Carbon\Carbon;
use IlBronza\CRUD\Helpers\ForcedUrlData;
use IlBronza\Category\Models\Category;
use IlBronza\FileCabinet\Helpers\AttachByCategoryTreeHelper;
use IlBronza\FileCabinet\Models\FilecabinetTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait InteractsWithAutomaticFilecabinetTrait
{
    abstract public function getAutomaticFilecabinetsToCreate() : Collection;

    protected static function getAutomaticFilecabinetsToCreateByModelEvent(Model $model, string $eventName) : Collection
    {        
        $filecabinets = $model->getAutomaticFilecabinetsToCreate();

        $modelClassString = strtolower($model->getMorphClass());

        return $filecabinets->filter(function($item) use($modelClassString, $eventName)
        {
            foreach($item->models as $element)
                if(strtolower($element['model']) == $modelClassString)
                    if($element['event'] == $eventName)
                        return true;

            return false;
        });
    }

    protected static function filecabinetCanBeAdded(Model $model, Category $category) : bool
    {
        if($model->hasRootsFilecabinetByMainCategory($category))
            return false;

        return true;
    }

    protected static function addForcedUrl(string $url, FilecabinetTemplate $filecabinetTemplate)
    {
        app('crudRouting')->addForcedUrl(
            ForcedUrlData::createByParameters([
                'url' => $url,
                'message' => __('filecabinets::messages.pleasePopulateThisElement', ['element' => $filecabinetTemplate->getCategory()->getName()])
            ])
        );
    }

    protected static function checkForAutomaticFilecabinets(Model $model, string $eventName)
    {
        $filecabinetTemplates = static::getAutomaticFilecabinetsToCreateByModelEvent($model, $eventName);

        foreach($filecabinetTemplates as $filecabinetTemplate)
        {
            if(! static::filecabinetCanBeAdded($model, $filecabinetTemplate->category))
                continue;

            $helper = AttachByCategoryTreeHelper::attachByFilecabinetTemplate(
                $model,
                $filecabinetTemplate
            );

            if($filecabinetTemplate->hasForcedPopulation())
                static::addForcedUrl($helper->getFilecabinet()->getPopulateUrl(), $filecabinetTemplate);
        }
    }

    public static function bootInteractsWithAutomaticFilecabinetTrait()
    {
        static::created(function ($model)
        {
            static::checkForAutomaticFilecabinets($model, 'created');
        });
    }
}