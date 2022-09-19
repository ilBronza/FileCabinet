<?php

namespace IlBronza\FileCabinet;

class FileCabinet
{
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
            'icon' => 'box-archive',
            'text' => 'filecabinets.manage'
        ]);

        $filecabinetButton = $menu->createButton([
            'name' => 'filecabinets',
            'icon' => 'box-archive',
            'text' => 'filecabinets.index',
            'href' => route('filecabinets.index'),
            'permissions' => ['filecabinets.index']
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
        // $containerButton->addChild($rolesButton);
        // $containerButton->addChild($permissionsButton);
    }
}