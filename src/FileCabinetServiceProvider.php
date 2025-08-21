<?php

namespace IlBronza\FileCabinet;

use IlBronza\CRUD\Traits\IlBronzaPackages\IlBronzaServiceProviderPackagesTrait;
use IlBronza\FileCabinet\Models\Dossier;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Filecabinet as FilecabinetModel;
use IlBronza\FileCabinet\Models\FilecabinetTemplate;
use IlBronza\FileCabinet\Models\Form;
use IlBronza\FileCabinet\Models\Formrow;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class FileCabinetServiceProvider extends ServiceProvider
{
	use IlBronzaServiceProviderPackagesTrait;
	
	/**
	 * Perform post-registration booting of services.
	 *
	 * @return void
	 */
	public function boot() : void
	{
		Relation::morphMap([
			'Filecabinet' => FilecabinetModel::gpc(),
			'FilecabinetTemplate' => FilecabinetTemplate::gpc(),
			'Dossier' => Dossier::gpc(),
			'Dossierrow' => Dossierrow::gpc(),
			'Form' => Form::gpc(),
			'Formrow' => Formrow::gpc(),
		]);

		$this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filecabinet');
		$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
		$this->loadViewsFrom(__DIR__ . '/../resources/views', 'filecabinet');
		$this->loadRoutesFrom(__DIR__ . '/routes.php');

		// Dossier::observe(DossierObserver::class);

		// Publishing is only necessary when using the CLI.
		if ($this->app->runningInConsole())
		{
			$this->bootForConsole();
		}
	}

	/**
	 * Register any package services.
	 *
	 * @return void
	 */
	public function register() : void
	{
		$this->mergeConfigFrom(__DIR__ . '/../config/filecabinet.php', 'filecabinet');

		// Register the service the package provides.
		$this->app->singleton('filecabinet', function ($app)
		{
			return new FileCabinet;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['filecabinet'];
	}

	/**
	 * Console-specific booting.
	 *
	 * @return void
	 */
	protected function bootForConsole() : void
	{
		// Publishing the configuration file.
		$this->publishes([
			__DIR__ . '/../config/filecabinet.php' => config_path('filecabinet.php'),
		], 'filecabinet.config');

		$this->publishes([
			__DIR__ . '/../database/migrations/' => database_path('migrations')
		], 'filecabinet.migrations');

		// Publishing the views.
		/*$this->publishes([
			__DIR__.'/../resources/views' => base_path('resources/views/vendor/ilbronza'),
		], 'filecabinet.views');*/

		// Publishing assets.
		/*$this->publishes([
			__DIR__.'/../resources/assets' => public_path('vendor/ilbronza'),
		], 'filecabinet.views');*/

		// Publishing the translation files.
		/*$this->publishes([
			__DIR__.'/../resources/lang' => resource_path('lang/vendor/ilbronza'),
		], 'filecabinet.views');*/

		// Registering package commands.
		// $this->commands([]);
	}
}
