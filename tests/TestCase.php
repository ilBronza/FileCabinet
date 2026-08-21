<?php

namespace IlBronza\FileCabinet\Tests;

use IlBronza\FileCabinet\Console\Commands\RecalculateSchedulesCommand;
use IlBronza\FileCabinet\Models\Dossierrow;
use IlBronza\FileCabinet\Models\Form;
use IlBronza\FileCabinet\Models\Formrow;
use IlBronza\MeasurementUnits\MeasurementUnitsServiceProvider;
use IlBronza\Schedules\SchedulesServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Spatie\Activitylog\ActivitylogServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
	protected function getPackageProviders($app) : array
	{
		return [
			ActivitylogServiceProvider::class,
			MeasurementUnitsServiceProvider::class,
			SchedulesServiceProvider::class,
		];
	}

	protected function defineEnvironment($app) : void
	{
		$app['config']->set('database.default', 'testing');
		$app['config']->set('database.connections.testing', [
			'driver' => 'sqlite',
			'database' => ':memory:',
			'prefix' => '',
			'foreign_key_constraints' => true,
		]);
		$app['config']->set('cache.default', 'array');
		$app['config']->set('activitylog.default_auth_driver', null);
		$app['config']->set('activitylog.enabled', false);
		$app['config']->set('schedules.validateConfiguration', false);
		$app['config']->set('schedules.evaluator.enabled', false);
		$app['config']->set('filecabinet.models.formrow', [
			'class' => Formrow::class,
			'table' => 'filecabinets__formrows',
		]);
		$app['config']->set('filecabinet.models.form', [
			'class' => Form::class,
			'table' => 'filecabinets__forms',
		]);
		$app['config']->set('filecabinet.models.dossierrow', [
			'class' => Dossierrow::class,
			'table' => 'filecabinets__dossierrows',
		]);
	}

	protected function setUp() : void
	{
		parent::setUp();

		$this->app->make(Kernel::class)->registerCommand(
			$this->app->make(RecalculateSchedulesCommand::class)
		);
		$this->createDatabaseSchema();
	}

	protected function createDatabaseSchema() : void
	{
		Schema::create(config('measurementunits.models.measurementUnit.table'), function (Blueprint $table) : void
		{
			$table->string('id', 16)->primary();
			$table->string('name');
			$table->string('base_measurement_unit');
			$table->decimal('proportion_toward_base_measurement_unit');
			$table->softDeletes();
			$table->timestamps();
		});

		Schema::create(config('schedules.models.type.table'), function (Blueprint $table) : void
		{
			$table->string('id')->primary();
			$table->string('name');
			$table->string('validity');
			$table->string('measurement_unit_id', 16);
			$table->boolean('allow_multiple')->default(false);
			$table->json('roles')->nullable();
			$table->json('models');
			$table->float('percentage_validity')->nullable();
			$table->softDeletes();
			$table->timestamps();
		});

		Schema::create(config('schedules.models.schedule.table'), function (Blueprint $table) : void
		{
			$table->uuid('id')->primary();
			$table->string('type_id');
			$table->string('schedulable_type');
			$table->string('schedulable_id');
			$table->string('starting_value', 32)->nullable();
			$table->string('deadline_value', 32);
			$table->string('field')->nullable();
			$table->dateTime('expired_at')->nullable();
			$table->dateTime('managed_at')->nullable();
			$table->softDeletes();
			$table->timestamps();
		});

		Schema::create(config('schedules.models.typeNotification.table'), function (Blueprint $table) : void
		{
			$table->string('id')->primary();
			$table->string('type_id');
			$table->string('before');
			$table->unsignedSmallInteger('urgency')->nullable();
			$table->string('repeat_every')->nullable();
			$table->string('repeat_every_measurement_unit_id', 16)->nullable();
			$table->string('last_repetition')->nullable();
			$table->softDeletes();
			$table->timestamps();
		});

		Schema::create(config('filecabinet.models.form.table'), function (Blueprint $table) : void
		{
			$table->string('id')->primary();
			$table->string('name');
			$table->softDeletes();
			$table->timestamps();
		});

		Schema::create(config('filecabinet.models.formrow.table'), function (Blueprint $table) : void
		{
			$table->string('id')->primary();
			$table->string('form_id')->default('form');
			$table->string('name');
			$table->string('slug');
			$table->string('type');
			$table->boolean('required')->default(false);
			$table->boolean('repeatable')->default(false);
			$table->text('parameters')->nullable();
			$table->unsignedInteger('sorting_index')->nullable();
			$table->softDeletes();
			$table->timestamps();
		});

		Schema::create(config('filecabinet.models.dossierrow.table'), function (Blueprint $table) : void
		{
			$table->string('id')->primary();
			$table->string('dossier_id')->default('dossier');
			$table->string('formrow_id');
			$table->timestamp('timestamp')->nullable();
			$table->text('text')->nullable();
			$table->string('string')->nullable();
			$table->decimal('decimal', 16, 4)->nullable();
			$table->boolean('boolean')->nullable();
			$table->softDeletes();
			$table->timestamps();
		});
	}
}
