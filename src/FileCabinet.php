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

        $button->addChild($containerButton);

        $containerButton->addChild($filecabinetButton);
    }
}