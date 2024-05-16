<?php

use IlBronza\FileCabinet\Facades\FileCabinet;

use Illuminate\Support\Facades\Route;

Route::group([
	'middleware' => ['web', 'auth'],
	'prefix' => 'filecabinet-manager',
	'as' => config('filecabinet.routePrefix')
	],
	function()
	{
		Route::group(['prefix' => 'filecabinets'], function()
		{
			//FilecabinetPdfController
			Route::get('{filecabinet}/generate-total-pdf', [FileCabinet::getController('filecabinet', 'pdf'), 'generateTotalPdf'])->name('filecabinets.generateTotalPdf');

			//FilecabinetPdfController
			Route::get('{filecabinet}/generate-partial-pdf', [FileCabinet::getController('filecabinet', 'pdf'), 'generatePartialPdf'])->name('filecabinets.generatePartialPdf');

			//FilecabinetPopulateController
			Route::get('{filecabinet}/populate', [FileCabinet::getController('filecabinet', 'populate'), 'populate'])->name('filecabinets.populate');

			//FilecabinetShowController
			Route::get('{filecabinet}', [FileCabinet::getController('filecabinet', 'show'), 'show'])->name('filecabinets.show');
		});

		Route::group(['prefix' => 'dossiers'], function()
		{
			//DossierUpdateFieldsController
			Route::get('{dossier}/update-fields', [FileCabinet::getController('dossier', 'updateFields'), 'updateFields'])->name('dossiers.updateFields');

			//DossierUpdateFieldsController
			Route::get('{dossier}/create-new-instance', [FileCabinet::getController('dossier', 'createNewInstance'), 'createNewInstance'])->name('dossiers.createNewInstance');

			//DossierUpdateController
			Route::put('{dossier}/update', [FileCabinet::getController('dossier', 'update'), 'update'])->name('dossiers.update');

			Route::delete('{dossier}/delete', [FileCabinet::getController('dossier', 'destroy'), 'destroy'])->name('dossiers.destroy');

		});

		Route::group(['prefix' => 'dossierrows'], function()
		{
			//DossierrowShowController
			Route::get('{dossierrow}', [FileCabinet::getController('dossierrow', 'show'), 'show'])->name('dossierrows.show');

			Route::post('{dossierrow}/add-instance', [FileCabinet::getController('dossierrow', 'addInstance'), 'addInstance'])->name('dossierrows.addInstance');

		});

		Route::group(['prefix' => 'forms'], function()
		{
			Route::get('{form}/formrows/create', [FileCabinet::getController('formrow', 'create'), 'createFromForm'])
				->name('forms.formrows.create');
			Route::post('{form}/formrows', [FileCabinet::getController('formrow', 'store'), 'storeFromForm'])->name('formrows.storeFromForm');

			//FormIndexController
			Route::get('', [FileCabinet::getController('form', 'index'), 'index'])->name('forms.index');

			//FormCreateStoreController
			Route::get('create', [FileCabinet::getController('form', 'create'), 'create'])->name('forms.create');
			Route::post('', [FileCabinet::getController('form', 'store'), 'store'])->name('forms.store');

			Route::get('{form}', [FileCabinet::getController('form', 'show'), 'show'])->name('forms.show');
			Route::get('{form}/edit', [FileCabinet::getController('form', 'edit'), 'edit'])->name('forms.edit');
			Route::put('{form}', [FileCabinet::getController('form', 'edit'), 'update'])->name('forms.update');

			Route::delete('{form}/delete', [FileCabinet::getController('form', 'destroy'), 'destroy'])->name('forms.destroy');
		});

		Route::group(['prefix' => 'formrows'], function()
		{
			Route::get('', [FileCabinet::getController('formrow', 'index'), 'index'])->name('formrows.index');

			Route::get('{formrow}', [FileCabinet::getController('formrow', 'show'), 'show'])->name('formrows.show');
			Route::get('{formrow}/edit', [FileCabinet::getController('formrow', 'edit'), 'edit'])->name('formrows.edit');
			Route::put('{formrow}', [FileCabinet::getController('formrow', 'edit'), 'update'])->name('formrows.update');

			Route::delete('{formrow}/delete', [FileCabinet::getController('formrow', 'destroy'), 'destroy'])->name('formrows.destroy');
		});

		Route::group([
			'middleware' => ['web', 'auth'],
			'prefix' => 'attach'
			],
			function()
			{
				//FormAttachByCategory
				Route::get('by-category/{category}/to-class/{class}/key/{key}', [FileCabinet::getController('form', 'attachByCategory'), 'attachByCategory'])->name('forms.attachByCategory');
			});

	}
);

