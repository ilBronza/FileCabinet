<?php

use IlBronza\FileCabinet\Http\Controllers\CrudFilecabinetController;
use IlBronza\FileCabinet\Http\Controllers\CrudFilecabinetrowController;
use Illuminate\Support\Facades\Route;

Route::group([
	'middleware' => ['web', 'auth'],
	'prefix' => 'filecabinet-manager',
	],
	function()
	{
		Route::resource('filecabinets', CrudFilecabinetController::class);

		// //START ROUTES PER REORDERING
		// Route::get('categories-reorder', 'CrudCategoryController@reorder')->name('categories.reorder');
		// Route::post('categories-reorder', 'CrudCategoryController@storeReorder')->name('categories.storeReorder');
		// //STOP ROUTES PER REORDERING

		// Route::prefix('filecabinets/{filecabinet}')->group(function () {
		// 	Route::resource('filecabinetrows', 'CrudFilecabinetrowController')->names('filecabinets.filecabinetrows');
		// });

		Route::resource('filecabinetrows', CrudFilecabinetrowController::class);


		// Route::resource('dossiers', 'CrudDossierController');
	}
);



// Route::get('asdasad', 'asdcontroller@masd')->name('categories.children.create');

