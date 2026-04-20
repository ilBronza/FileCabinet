<?php

namespace IlBronza\FileCabinet\Helpers;

use IlBronza\Category\Helpers\HierarchicalCategoryExtractorHelper;
use IlBronza\FileCabinet\Models\Filecabinet;
use IlBronza\FileCabinet\Models\FilecabinetTemplate;
use IlBronza\FileCabinet\Models\Form;
use Illuminate\Database\Eloquent\Model;

class FilecabinetTemplateSyncHelper
{
	static function sync(Filecabinet $filecabinet) : bool
	{
		$rootFilecabinet = $filecabinet->isRoot() ? $filecabinet : $filecabinet->getRoot();

		if(! $filecabinetTemplate = $rootFilecabinet->getFilecabinetTemplate())
			return false;

		if(! $model = $rootFilecabinet->getFilecabinetable())
			return false;

		$desiredTree = HierarchicalCategoryExtractorHelper::extractRecursiveCategorizables(
			$filecabinetTemplate->getCategory(),
			[
				'forms',
				'directForms'
			]
		);

		return static::syncNode($desiredTree, $rootFilecabinet, $model, $filecabinetTemplate);
	}

	private static function syncNode(
		FilecabinetNodeCollection $node,
		Filecabinet $filecabinet,
		Model $model,
		FilecabinetTemplate $filecabinetTemplate
	) : bool
	{
		$changed = false;

		if(($filecabinet->filecabinet_template_id ?? null) !== $filecabinetTemplate->getKey())
		{
			$filecabinet->filecabinetTemplate()->associate($filecabinetTemplate);
			$filecabinet->save();
			$changed = true;
		}

		foreach($node->getFormElements() as $form)
			$changed = static::syncForm($filecabinet, $model, $form) || $changed;

		foreach($node->getChildrenCategories()->sortBy('category.sorting_index') as $childNode)
			$changed = static::syncChildCategoryNode($childNode, $filecabinet, $model, $filecabinetTemplate) || $changed;

		return $changed;
	}

	private static function syncForm(Filecabinet $filecabinet, Model $model, Form $form) : bool
	{
		$dossier = DossierCreatorHelper::getOrCreateByForm($model, $form);

		if($filecabinet->dossiers()->whereKey($dossier->getKey())->exists())
			return false;

		$filecabinet->dossiers()->syncWithoutDetaching([$dossier->getKey()]);

		return true;
	}

	private static function syncChildCategoryNode(
		FilecabinetNodeCollection $childNode,
		Filecabinet $parentFilecabinet,
		Model $model,
		FilecabinetTemplate $filecabinetTemplate
	) : bool
	{
		$category = $childNode->getCategory();

		$childFilecabinet = $model->filecabinets()
			->where('parent_id', $parentFilecabinet->getKey())
			->where('category_id', $category->getKey())
			->first();

		$changed = false;

		if(! $childFilecabinet)
		{
			$childFilecabinet = FilecabinetCreatorHelper::createByCategoryAndParent(
				$model,
				$category,
				$parentFilecabinet
			);

			$childFilecabinet->filecabinetTemplate()->associate($filecabinetTemplate);
			$childFilecabinet->save();

			$changed = true;
		}

		return static::syncNode($childNode, $childFilecabinet, $model, $filecabinetTemplate) || $changed;
	}
}

