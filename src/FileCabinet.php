<?php

namespace IlBronza\FileCabinet;

use IlBronza\CRUD\Providers\RouterProvider\RoutedObjectInterface;
use IlBronza\CRUD\Traits\IlBronzaPackages\IlBronzaPackagesTrait;
use IlBronza\FileCabinet\Traits\InteractsWithFormTrait;
use Illuminate\Database\Eloquent\Model;

use function app;

class FileCabinet implements RoutedObjectInterface
{
    use IlBronzaPackagesTrait;

    static $packageConfigPrefix = 'filecabinet';

    public function manageMenuButtons()
    {
        if(! $menu = app('menu'))
            return;

        $button = $menu->provideButton([
                'text' => 'generals.settings',
                'name' => 'settings',
                'icon' => 'gear',
                'roles' => ['administrator']
            ]);

        $button->setFirst();

        $containerButton = $menu->createButton([
            'name' => 'file-cabinet-manager',
            'icon' => 'file-invoice',
            'text' => 'filecabinet::filecabinet.manage'
        ]);

        $filecabinetTemplateButton = $menu->createButton([
            'name' => 'filecabinetTemplates',
            'icon' => 'box-archive',
            'text' => 'filecabinet::filecabinetTemplates.index',
            'href' => app('filecabinet')->route('filecabinetTemplates.index'),
        ]);

        $filecabinetButton = $menu->createButton([
            'name' => 'filecabinets',
            'icon' => 'box-archive',
            'text' => 'filecabinet::filecabinets.index',
            'href' => app('filecabinet')->route('filecabinets.index'),
        ]);

	    $formsButton = $menu->createButton([
		    'name' => 'forms',
		    'icon' => 'box-archive',
		    'text' => 'filecabinet::forms.index',
		    'href' => app('filecabinet')->route('forms.index'),
	    ]);

	    $formrowsButton = $menu->createButton([
		    'name' => 'formrows',
		    'icon' => 'box-archive',
		    'text' => 'filecabinet::formrows.index',
		    'href' => app('filecabinet')->route('formrows.index'),
	    ]);

	    // $rolesButton = $menu->createButton([
        //     'name' => 'roles.index',
        //     'text' => 'account-manager.roles',
        //     'icon' => 'graduation-cap',
        //     'href' => route('roles.index'),
        //     'permissions' => ['roles.index']
        // ]);

        // $permissionsButton = $menu->createButton([
        //     'name' => 'permissions.index',
        //     'text' => 'account-manager.permissions',
        //     'icon' => 'user-lock',
        //     'href' => route('permissions.index'),
        //     'permissions' => ['permissions.index']
        // ]);

        $button->addChild($containerButton);

        $containerButton->addChild($filecabinetButton);
        $containerButton->addChild($filecabinetTemplateButton);
	    $containerButton->addChild($formsButton);
	    $containerButton->addChild($formrowsButton);

        // $containerButton->addChild($rolesButton);
        // $containerButton->addChild($permissionsButton);
    }

    public function assertInteractsWithModel(Model $model)
    {
        if(! in_array(InteractsWithFormTrait::class, class_uses_recursive($model)))
            throw new \Exception('Il model ' . get_class($model) . ' non usa filecabinet');
    }

    static function getController(string $target, string $controllerPrefix) : string
    {
        try
        {
            return config("filecabinet.models.{$target}.controllers.{$controllerPrefix}");
        }
        catch(\Throwable $e)
        {
            dd([$e->getMessage(), 'dichiara ' . "filecabinet.models.{$target}.controllers.{$controllerPrefix}"]);
        }
    }
}