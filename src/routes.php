<?php

use App\Http\Controllers\Registry\CrudDrivemodelController;
use IlBronza\FileCabinet\Facades\FileCabinet;

use IlBronza\FileCabinet\Models\Dossierrow;
use Illuminate\Support\Facades\Route;

Route::group([
	'middleware' => ['web', 'auth', 'role:administrator|documents'],
	'prefix' => 'filecabinet-manager',
	'as' => config('filecabinet.routePrefix')
	],
	function()
	{
		Route::group(['prefix' => 'filecabinet-templates'], function()
		{
			Route::get('', [FileCabinet::getController('filecabinetTemplate', 'index'), 'index'])->name('filecabinetTemplates.index');
			Route::get('create', [FileCabinet::getController('filecabinetTemplate', 'create'), 'create'])->name('filecabinetTemplates.create');
			Route::post('', [FileCabinet::getController('filecabinetTemplate', 'store'), 'store'])->name('filecabinetTemplates.store');
			Route::get('{filecabinetTemplate}', [FileCabinet::getController('filecabinetTemplate', 'show'), 'show'])->name('filecabinetTemplates.show');
			Route::get('{filecabinetTemplate}/edit', [FileCabinet::getController('filecabinetTemplate', 'edit'), 'edit'])->name('filecabinetTemplates.edit');
			Route::put('{filecabinetTemplate}', [FileCabinet::getController('filecabinetTemplate', 'edit'), 'update'])->name('filecabinetTemplates.update');

			Route::delete('{filecabinetTemplate}/delete', [FileCabinet::getController('filecabinetTemplate', 'destroy'), 'destroy'])->name('filecabinetTemplates.destroy');
			
			Route::delete('filecabinetTemplate/{filecabinetTemplate}/delete-media/{media}', [FileCabinet::getController('filecabinetTemplate', 'deleteMedia'), 'deleteMedia'])
			     ->name('filecabinetTemplates.deleteMedia');


			Route::get('{filecabinetTemplate}/manage-pdf-template-code', [FileCabinet::getController('filecabinetTemplate', 'pdfTemplate'), 'edit'])->name('filecabinetTemplates.managePdfTemplate');
			
		});


		Route::group(['prefix' => 'filecabinets'], function()
		{
			Route::get('', [FileCabinet::getController('filecabinet', 'index'), 'index'])->name('filecabinets.index');


			//FilecabinetPdfController
			Route::get('{filecabinet}/generate-total-pdf', [FileCabinet::getController('filecabinet', 'pdf'), 'generateTotalPdf'])->name('filecabinets.generateTotalPdf');

			//FilecabinetPdfController
			Route::get('{filecabinet}/generate-partial-pdf', [FileCabinet::getController('filecabinet', 'pdf'), 'generatePartialPdf'])->name('filecabinets.generatePartialPdf');

			//FilecabinetPopulateController
			Route::get('{filecabinet}/populate', [FileCabinet::getController('filecabinet', 'populate'), 'populate'])->name('filecabinets.populate')->withoutMiddleware(['role:administrator'])->middleware('role:worker|administrator');

			//FilecabinetShowController
			Route::get('{filecabinet}', [FileCabinet::getController('filecabinet', 'show'), 'show'])->name('filecabinets.show')->withoutMiddleware(['role:administrator'])->middleware('role:worker|administrator');
		});

		Route::group(['prefix' => 'dossiers'], function()
		{
			//DossierByModelCategoryController
			Route::get('model/{model}/{key}/by-category/{category}/table', [FileCabinet::getController('dossier', 'byModelCategory'), 'byModelCategory'])->name('dossiers.byModelCategory');

			//DossierByModelCategoryController
			Route::get('model/{model}/{key}/by-category/{category}/populate', [FileCabinet::getController('dossier', 'byModelCategory'), 'populateByModelCategory'])->name('dossiers.byModelCategory.populate');

			//DossierByModelFormController
			Route::get('model/{model}/{key}/by-form/{form}/table', [FileCabinet::getController('dossier', 'byModelForm'), 'byModelForm'])->name('dossiers.byModelForm');

			//DossierByFormIndexController
			Route::get('by-form/{form}', [FileCabinet::getController('dossier', 'byForm'), 'index'])->name('dossiers.byForm');

			//DossierUpdateFieldsController
			Route::get('{dossier}/update-fields', [FileCabinet::getController('dossier', 'updateFields'), 'updateFields'])->name('dossiers.updateFields');

			//DossierCreateNewInstanceController
			Route::get('{dossier}/create-new-instance', [FileCabinet::getController('dossier', 'createNewInstance'), 'createNewInstance'])->name('dossiers.createNewInstance')->withoutMiddleware(['role:administrator'])->middleware('role:worker|administrator');

			Route::get('/asd-toto-rimuovere-create-new-instance', [FileCabinet::getController('dossier', 'createNewInstance'), 'createNewInstance'])->name('dossiers.create')->withoutMiddleware(['role:administrator'])->middleware('role:worker|administrator');


			//DossierUpdateController
			Route::put('{dossier}/update', [FileCabinet::getController('dossier', 'update'), 'update'])->name('dossiers.update')->withoutMiddleware(['role:administrator'])->middleware('role:worker|administrator|documents');

			Route::get('{dossier}/populate', [FileCabinet::getController('dossier', 'populate'), 'populate'])->name('dossiers.populate')->withoutMiddleware(['role:administrator'])->middleware('role:worker|administrator|areaManager');

			Route::delete('{dossier}/delete', [FileCabinet::getController('dossier', 'destroy'), 'destroy'])->name('dossiers.destroy');


			Route::get('', [FileCabinet::getController('dossier', 'index'), 'index'])->name('dossiers.index');

			Route::get('{dossier}', [FileCabinet::getController('dossier', 'show'), 'show'])->name('dossiers.show')->withoutMiddleware(['role:administrator'])->middleware('role:worker|administrator|areaManager');

			Route::get('{dossier}/edit', [FileCabinet::getController('dossier', 'edit'), 'edit'])->name('dossiers.edit')->withoutMiddleware(['role:administrator'])->middleware('role:worker|administrator');


			//			Route::get('get-fetcher-by-form/{$form}');
		});

		Route::group(['prefix' => 'dossierrows'], function()
		{
			Route::get('{dossierrow}/create-new-instance', [FileCabinet::getController('dossierrow', 'createNewInstance'), 'createNewInstance'])->name('dossierrows.createNewInstance')->withoutMiddleware(['role:administrator'])->middleware('role:worker|administrator');


			//DossierrowShowController
			Route::get('{dossierrow}', [FileCabinet::getController('dossierrow', 'show'), 'show'])->name('dossierrows.show');

			Route::get('{dossierrow}/edit', function($dossierrow)
			{
				$dossier = Dossierrow::with('dossier')->find($dossierrow)->dossier;

				return redirect()->to($dossier->getPopulateUrl());
			})->name('dossierrows.edit')->withoutMiddleware(['role:administrator'])->middleware('role:worker|administrator');

			//DossierrowAddInstanceController
			Route::post('{dossierrow}/add-instance', [FileCabinet::getController('dossierrow', 'addInstance'), 'addInstance'])->name('dossierrows.addInstance')->withoutMiddleware(['role:administrator'])->middleware('role:worker|administrator');

			Route::delete('dossierrow/{dossierrow}/delete-media/{media}', [FileCabinet::getController('dossierrow', 'deleteMedia'), 'deleteMedia'])
				->name('dossierrows.deleteMedia')->withoutMiddleware(['role:administrator'])->middleware('role:worker|administrator');

			Route::get('', [FileCabinet::getController('dossierrow', 'index'), 'index'])->name('dossierrows.index');
		});

		Route::group(['prefix' => 'forms'], function()
		{
			Route::get('attach-to-model/model/{class}/id/{id}', [FileCabinet::getController('form', 'attachByModel'), 'index'])
			     ->name('forms.attachByModel.index')->withoutMiddleware(['role:administrator'])->middleware('role:worker|administrator');

			Route::get('{form}/clone', [FileCabinet::getController('form', 'clone'), 'clone'])
				->name('forms.clone');


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
			//FormrowReorderController
			Route::post('reorder', [FileCabinet::getController('formrow', 'reorder'), 'storeMassReorder'])->name('formrows.storeMassReorder');

			//FormrowIndexController
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
				Route::get('by-category/{category}/to-class/{class}/key/{key}', [FileCabinet::getController('form', 'attachByCategory'), 'attachByCategory'])->name('forms.attachByCategory')->withoutMiddleware(['role:administrator'])->middleware('role:worker|administrator');
			});

	}
);

