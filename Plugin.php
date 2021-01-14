<?php namespace Wezeo\Websitescraper;

use Backend;
use Illuminate\Support\Facades\Event;
use System\Classes\PluginBase;
use Wezeo\CommonServices\Controllers\Classes\Servicer;

class Plugin extends PluginBase
{

    public function pluginDetails()
    {
        return [
            'name'        => 'websitescraper',
            'description' => 'No description provided yet...',
            'author'      => 'wezeo',
            'icon'        => 'icon-leaf'
        ];
    }


    public function registerSettings()
    {
        return [
            'Scraper' => [
                'label'       => 'Scraper',
                'category'    => 'Visivig',
                'description' => 'Scrap website to file',
                'icon'        => 'icon-globe',
                'url'         => Backend::url('Wezeo/Websitescraper/Scraper'),
                'order'       => 1,
            ],
            'Traverser' => [
                'label'       => 'Traverser',
                'category'    => 'Visivig',
                'description' => 'Get subpage links from page',
                'icon'        => 'icon-globe',
                'url'         => Backend::url('Wezeo/Websitescraper/Traverser'),
                'order'       => 1,
            ],
        ];
    }


}
