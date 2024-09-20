<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Form;
use IlBronza\FileCabinet\Models\Formrow;
use Illuminate\Database\Eloquent\Model;

class DossierCreatorHelper
{
    static function makeDossierrowFromFormrow(Formrow $formrow) : Dossierrow
    {
        $dossierrow = Dossierrow::make();
        $dossierrow->formrow()->associate($formrow);

        return $dossierrow;
    }

    static function addFormrowToDossier(Dossier $dossier, Formrow $formrow) : Dossierrow
    {
        $dossierrow = $dossier->dossierrows()->save(
            static::makeDossierrowFromFormrow(
                $formrow
            )
        );

        return $dossierrow;
    }


    static function replicateDossierrow(Dossierrow $dossierrow)
    {
        $dossier = $dossierrow->getDossier();
        $formrow = $dossierrow->getFormrow();

        $newDossierrow = static::addFormrowToDossier($dossier, $formrow);

        return $newDossierrow;
    }

    static function dossierHasFormRow(Dossier $dossier, Formrow $formrow) : bool
    {
        return $dossier->getDossierrows()->contains('formrow_id', $formrow->getKey());
    }

    static function formHasFormRow(Form $form, Dossierrow $dossierrow) : bool
    {
        return $form->getFormrows()->contains('id', $dossierrow->formrow_id);
    }

    static function updateDossierRowsByForm(Dossier $dossier)
    {
        if(! $dossier->getForm())
            throw new \Exception('Dossier ' . $this->gretKey() . ' has not form anymore');

        foreach($dossier->getForm()->getFormrows() as $formrow)
            if(! static::dossierHasFormRow($dossier, $formrow))
                static::addFormrowToDossier($dossier, $formrow);
                // $dossier->dossierrows()->save(
                //     static::makeDossierrowFromFormrow(
                //         $formrow
                //     )
                // );

        foreach($dossier->getDossierrows(true) as $dossierrow)
            if(! static::formHasFormRow($dossier->getForm(), $dossierrow))
                $dossierrow->delete();

        $dossier->setFieldsUpdated();

        return $dossier;        
    }

    static function makeByForm(Form $form, bool $withDossierrows = false) : Dossier
    {
        $dossier = Dossier::make();
        $dossier->form()->associate($form);
        $dossier->sorting_index = $form->getSortingIndex();
        $dossier->save();

        foreach($form->getFormrows() as $formrow)
            $dossier->dossierrows()->save(
                static::makeDossierrowFromFormrow(
                    $formrow
                )
            );

		if($withDossierrows)
			$dossier->dossierrows()->get();

        return $dossier;
    }

    static function getOrCreateByForm(Model $model, Form $form, bool $withDossierrows = false) : Dossier
    {
		$query = $model->dossiers()->byForm($form);

		if($withDossierrows)
			$query->with('dossierrows');

        if($dossier = $query->first())
            return $dossier;

        return static::createByForm($model, $form, null, $withDossierrows);
    }

	static function automaticallyCreateByForm(Model $model, Form $form, Dossier $parentDossier = null) : ? Dossier
	{
		if(! $form->canAutomaticallyBeCreatedBy($model))
			return null;

		return static::createByForm($model, $form, $parentDossier);
	}

    static function createByForm(Model $model, Form $form, Dossier $parentDossier = null, bool $withDossierrows = false) : Dossier
    {
        $dossier = static::makeByForm($form, $withDossierrows);

        $dossier = $model->dossiers()->save($dossier);


        if($parentDossier)
            $parentDossier->children()->save($dossier);

        foreach($form->getChildren() as $childForm)
            static::createByForm($model, $childForm, $dossier);

        return $dossier;
    }

    static function createByDossier(Dossier $dossier) : Dossier
    {
        $newDossier = static::createByForm(
            $dossier->getDossierable(),
            $dossier->getForm()
        );

        foreach($dossier->getFilecabinets() as $filecabinet)
            $filecabinet->dossiers()->save($newDossier);

        return $newDossier;
    }
}