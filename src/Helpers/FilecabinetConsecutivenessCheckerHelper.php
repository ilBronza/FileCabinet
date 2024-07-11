<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\FileCabinet\Helpers\FilecabinetCompletionCheckerHelper;
use IlBronza\FileCabinet\Models\Filecabinet;

class FilecabinetConsecutivenessCheckerHelper
{
    static function checkPreviousCompleted(Filecabinet $filecabinetNode, Filecabinet $targetFilecabinet) : null|true|Filecabinet
    {
        // dd('risolvere ordinamento qua dentro forse con un return null e bool');

        if($filecabinetNode->is($targetFilecabinet))
            return true;            

        if(! FilecabinetCompletionCheckerHelper::hasAllCompletedDossiers($filecabinetNode))
            return $filecabinetNode;

        foreach($filecabinetNode->recursiveChildren->sortBy('sorting_index') as $childFilecabinetNode)
        {
            if(is_null($result = static::checkPreviousCompleted($childFilecabinetNode, $targetFilecabinet)))
                continue;

            return $result;
        }

        return null;
    }

    static function checkConsecutiveness(Filecabinet $filecabinet) : true|Filecabinet
    {
        if($filecabinet->isroot())
            return true;

        $rootFilecabinet = $filecabinet->getRoot();

        if(! $filecabinetTemplate = $rootFilecabinet->getFilecabinetTemplate())
            return true;

        if(! $filecabinetTemplate->hasForcedConsecutiveness())
            return true;

        $tree = FilecabinetGetTreeHelper::getDescendantTreeWithForms($rootFilecabinet);

        return static::checkPreviousCompleted($tree, $filecabinet);
    }
}