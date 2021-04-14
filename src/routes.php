<?php

use Illuminate\Support\Facades\Route;

Route::group([
	'middleware' => ['web', 'auth'],
	'prefix' => 'filecabinet-manager',
	'namespace' => 'IlBronza\Filecabinet\Http\Controllers'
	],
	function()
	{
		Route::resource('filecabinets', 'CrudFilecabinetController');

		// //START ROUTES PER REORDERING
		// Route::get('categories-reorder', 'CrudCategoryController@reorder')->name('categories.reorder');
		// Route::post('categories-reorder', 'CrudCategoryController@stroreReorder')->name('categories.stroreReorder');
		// //STOP ROUTES PER REORDERING

		Route::prefix('filecabinets/{filecabinet}')->group(function () {
			Route::resource('filecabinetrows', 'CrudFilecabinetrowController')->names('filecabinets.filecabinetrows');
		});
	}
);

Route::resource('dossiers', 'IlBronza\Filecabinet\Http\Controllers\CrudDossierController');


// Route::get('asdasad', 'asdcontroller@masd')->name('categories.children.create');

