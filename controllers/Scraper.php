<?php namespace Wezeo\Websitescraper\Controllers;

use Backend\Facades\BackendMenu;
use Backend\Classes\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use October\Rain\Support\Facades\Flash;
use Wezeo\Websitescraper\Controllers\Classes\DownloadUtils;
use Wezeo\Websitescraper\Controllers\Classes\DownloadUtilsMainPage;


/**
 * Scrapper Back-end Controller
 */
class Scraper extends Controller
{

    public $links = "";


    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Wezeo.Websitescraper', 'websitescraper', 'scraper');
    }

    public function index() {

    }

    public function onSubmit(){
        $links  = Input::get("link");
        $check  = Input::get("checkbox");
        $select = Input::get('downloadSelect');

        $tempSize = 0;
        if ($check == "on")
        {
            $check = 1;
        } else {
            $check = 0;
        }
        $links = explode("\r\n", $links);
        $links = array_filter(array_map('trim', $links));
        foreach ($links as $link){
            $tempSize = $tempSize + DownloadUtils::run($link,$check);
            echo "<script>window.scrollTo(document.height)</script>";
        }

        echo "<h3>Vsetky stiahnute stranky</h3>";
        foreach ($links as $link){
            $fname = DownloadUtils::_fileName($link);
            DownloadUtils::returnLink($fname, $check);
        }
        DownloadUtils::_resultTable($tempSize, $link);
        die();

    }



}
