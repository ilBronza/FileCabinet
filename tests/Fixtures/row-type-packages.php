<?php

// Standalone fixture: each invocation has a fresh autoloader and container.
// Usage: php row-type-packages.php /path/to/bootstrap.php FileCabinet,Operators,Warehouse [--cached] [--early]
$bootstrapLoader = require $argv[1];
if ($bootstrapLoader instanceof \Composer\Autoload\ClassLoader)
	$loader = $bootstrapLoader;
$packages = explode(',', $argv[2]);
$namespaces = ['FileCabinet', 'Operators', 'Warehouse'];
foreach ($namespaces as $package)
	if (! in_array($package, $packages, true))
		$loader->setPsr4("IlBronza\\{$package}\\", []);

function check(bool $condition, string $message) : void
{
	if (! $condition)
		throw new RuntimeException($message);
}

foreach ($namespaces as $package)
	check(class_exists("IlBronza\\{$package}\\{$package}ServiceProvider") === in_array($package, $packages, true), "Incorrect autoload isolation: {$package}");

class RegistryFixtureApplication extends \Illuminate\Foundation\Application
{
	public bool $cached = false;
	public function configurationIsCached() { return $this->cached; }
}

$app = new RegistryFixtureApplication(dirname(__DIR__, 2));
$app->cached = in_array('--cached', $argv, true);
\Illuminate\Container\Container::setInstance($app);
\Illuminate\Support\Facades\Facade::setFacadeApplication($app);
$app->instance('config', new \Illuminate\Config\Repository());
$translator = new \Illuminate\Translation\Translator(new \Illuminate\Translation\FileLoader(new \Illuminate\Filesystem\Filesystem(), __DIR__), 'it');
$app->instance('translator', $translator);
foreach ($packages as $package)
{
	$config = strtolower($package);
	$root = dirname(__DIR__, 3) . "/{$package}";
	$app['config']->set($config, require "{$root}/config/{$config}.php");
	$translator->addNamespace($config, "{$root}/resources/lang");
}
// Keep legacy controller construction outside this integration fixture.
$app->instance('IlBronza\\Operators\\Http\\Controllers\\CrudOperatorsController', new stdClass());
foreach ($packages as $package)
{
	$provider = "IlBronza\\{$package}\\{$package}ServiceProvider";
	(new $provider($app))->register();
	if ($package === 'FileCabinet' && in_array('--early', $argv, true))
		$app->make(\IlBronza\FileCabinet\Providers\RowTypes\RowTypeRegistry::class);
}

if (in_array('FileCabinet', $packages, true))
{
	$registry = $app->make(\IlBronza\FileCabinet\Providers\RowTypes\RowTypeRegistry::class);
	check($registry === $app->make(get_class($registry)), 'Registry must be shared.');
	check($registry->has('text'), 'Base fields missing.');
	foreach (['operatorSelect', 'operatorsSelect'] as $legacy)
		check($registry->make($legacy)->isAvailable() === in_array('Operators', $packages, true), "Incorrect legacy availability: {$legacy}");
	foreach (['Operators' => ['clientOperator', 'clientOperators', 'contracttype'], 'Warehouse' => ['room', 'rooms', 'roomType']] as $package => $types)
		foreach ($types as $id)
		{
			$present = in_array($package, $packages, true);
			check($registry->has($id) === $present, "Incorrect availability: {$id}");
			check($registry->info($id)['package'] === 'ilbronza/' . strtolower($package), "Incorrect package metadata: {$id}");
			check(array_key_exists($id, $registry->choices()) === $present, "Incorrect editor list: {$id}");
			$one = $registry->make($id);
			$two = $registry->make($id);
			check($one !== $two, "Shared row instance: {$id}");
			check($one->isAvailable() === $present, "Incorrect fallback: {$id}");
		}
}
else
{
	check(! class_exists(\IlBronza\FileCabinet\Providers\RowTypes\RowTypeRegistry::class), 'FileCabinet was unexpectedly autoloaded.');
	foreach (get_declared_classes() as $class)
		check(! str_contains($class, '\\FileCabinet\\RowTypes\\'), "Integration class loaded without FileCabinet: {$class}");
}
echo 'PASS ' . $argv[2] . ($app->cached ? ' cached' : '') . (in_array('--early', $argv, true) ? ' early' : '') . PHP_EOL;

// Let other standalone fixtures import the loader by requiring this bootstrap.
return $loader;
