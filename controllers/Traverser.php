<?php namespace Wezeo\Websitescraper\Controllers;

use BackendMenu;
use Backend;
use Illuminate\Support\Facades\Session;
use Redirect;
use Backend\Classes\Controller;
use Input;
use Wezeo\Websitescraper\Controllers\Classes\DownloadUtils;

/**
 * Traverser Back-end Controller
 */
class Traverser extends Controller
{
    public $linkis = "";
    public $count = 1;


    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Wezeo.Websitescraper', 'websitescraper', 'traverser');
    }

    public function index(){}

    public function onSubmit()
    {
        $links = Input::get("link");

        $links = explode("\r\n", $links);
        $links = array_filter(array_map('trim', $links));

        $linksCount = count($links);
        if ($linksCount < Session::get("oldCount")) {
            Session::put("oldCount", $linksCount);
        }
        $linkis = [];

        /* Search all links and return in array */
        foreach ($links as $link) {
            $linkis = DownloadUtils::_linkToDownload($link, $linkis);
        }

        $linkis = array_unique($linkis);

        /* Count for flash message */
        $newCount = count($linkis); //19
        $count = Session::get("oldCount"); //4
        $count = $newCount - $count; //15

        $this->linkis = $linkis;
        Session::put("count", $count); //15
        Session::put("oldCount", $newCount); //19
    }

    public function onMove()
    {
        /* Export Link to Session and redirect to Scraper */
        $links = Input::get("link");
        Session::put('links', $links);
        return Redirect::to(Backend::url('Wezeo/Websitescraper/Scraper'));
    }


}
