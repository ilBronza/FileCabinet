<?php

use IlBronza\Category\Models\Category;
use IlBronza\FileCabinet\Models\Filecabinet;

return [
	'categories' => [
		'model' => Category::class,
		'collection' => 'filecabinet'
	],
	'filecabinet' => [
		'model' => Filecabinet::class
	] 
];