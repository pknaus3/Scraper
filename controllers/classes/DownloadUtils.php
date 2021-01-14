<?php namespace Wezeo\Websitescraper\Controllers\Classes;

use Cms\Classes\Theme;
use October\Rain\Support\Facades\Flash;
use Wezeo\Websitescraper\Controllers\Classes\ManageAssetsAndPages;
use Wezeo\Websitescraper\Controllers\Classes\DownloadAssets;
use Twig;
use Wezeo\Websitescraper\Controllers\Scraper;

require "plugins/wezeo/websitescraper/htmlpars/SimpleHtmlDom.php";

class DownloadUtils
{
    //Caled by onSubmit()
    static function run($link, $check)
    {
        echo '

        <style>
        body{line-height: 1.9; font-family: arial;}
        *{white-space: nowrap} span{font-size: 13;}

        </style>

            ';
        if ($link == "") {
            Flash::error('Zadajte aspoň jeden link');
            return 0;
        }

        ini_set('max_execution_time', 300);

        $html = file_get_html($link);
        $fileSize = strlen($html) / 1024;
        $fileSize = round($fileSize);
        if ($fileSize < 1) {
            $fileSize = 1;
        }
        echo '<h4><b>HTML:</b> (' . $fileSize . 'kB) ' . $link . "<br></h4>";

        $fname = self::_fileName($link);

        $activeTheme = Theme::getActiveTheme()->getDirName();

        $path = "themes/" . $activeTheme;

        $tempSize = 0;

        self::_makeDirs($path, $fname);

        file_put_contents($path . "/tempIndex.html", $html);

        foreach ($html->find("title") as $element) {
            $title = $element->plaintext;
        }

        if (!isset($title)) {
            $title = "index page";
        }

        $tempSize = DownloadAssets::_findAssetsInHtml($html, $link, $fname, $path, $tempSize);

        $tempSize = ManageAssetsAndPages::_updateAssetsLinks($path, $fname, $title, $check, $tempSize);

        Flash::success('Stránka úspešne  stiahnutá');

        return $tempSize;

    }

    static function returnLink($fname, $check)
    {

        if (!is_array($fname)) {
            if ($check == 1) {
                echo "<h3><a href=\"/\" target=\"_blank\">/{$fname}</a></h3>";
            } else {
                echo "<h3><a href=\"/$fname\" target=\"_blank\">/{$fname}</a></h3>";
            }
        }

        if (is_array($fname)) {
            if ($check == 1) {
                unset($fname[0]);
            }
            $implodedFname = implode('/', $fname);
            echo "<h3><a href=\"/$implodedFname\" target=\"_blank\">/$implodedFname</a></h3>";
        }
    }

    //Parse http/s and / from end
    static function _fileName($link)
    {
        if (substr($link, 0, 5) == "https") {
            $fname = str_replace("https://", "", $link);

            if (substr($fname, -1) == "/") {
                $fname = rtrim($fname, "/");
            }

            $fname = self::_fnameSubPages($fname);

            return $fname;
        } elseif (substr($link, 0, 5) == "http:") {
            $fname = str_replace("http://", "", $link);

            if (substr($fname, -1) == "/") {
                $fname = rtrim($fname, "/");
            }

            $fname = self::_fnameSubPages($fname);

            return $fname;
        }
    }

    //Create array with subpages
    static function _fnameSubPages($fname)
    {

        if (strpos($fname, "/")) {
            $fname1 = explode('/', $fname);
            return $fname1;
        }

        return $fname;
    }

    //Create all required dirs
    static function _makeDirs($path, $fname)
    {

        //Create without subpages
        if (!is_array($fname)) {
            if (\File::makeDirectory($path . "/pages/{$fname}", 0755, true, true)) {
                \File::makeDirectory($path . "/pages/{$fname}", 0755, true, true);
            }
        }

        //Create with subpages
        if (is_array($fname) && !is_dir($path . "/pages/{$fname[0]}")) {
            mkdir($path . "/pages/{$fname[0]}", 0755);
        }
    }
    //Return result table on end
#--------------------------------------------------------------------------------------------------------------------------------------
    static function _resultTable($tempSize, $link)
    {
        $tempSize = round($tempSize / 1024);
        echo "
            <link rel=\"stylesheet\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css\" integrity=\"sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk\" crossorigin=\"anonymous\">
            <script src=\"https://code.jquery.com/jquery-3.5.1.slim.min.js\" integrity=\"sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj\" crossorigin=\"anonymous\"></script>
            <script src=\"https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js\" integrity=\"sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo\" crossorigin=\"anonymous\"></script>
            <script src=\"https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js\" integrity=\"sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI\" crossorigin=\"anonymous\"></script>
            <h5>Celkovo stiahnutých = {$tempSize} MB</h5>

            <br><br>

            <a href=\"/backend/Wezeo/Websitescraper/Scraper\" class=\"btn btn-dark\">Poď na Scraper</a>
            <!-- <h4>Všetky linky</h4> -->
        ";

        $activeTheme = Theme::getActiveTheme()->getDirName();
        $fname = self::_fileName($link);
//        if (!is_array($fname) ) {
//            $files   = scandir("themes/$activeTheme/pages/$fname");
//        }
//
//        if (is_array($fname) ) {
//            $files = scandir("themes/$activeTheme/pages/$fname[0]");
//        }
//
//        unset($files[0]);
//        unset($files[1]);
//
//        foreach ($files as $file) {
//            if (!is_array($fname) ) {
//                $file = file_get_html("themes/$activeTheme/pages/$fname/$file");
//            }
//
//            if (is_array($fname) ) {
//                $file = file_get_html("themes/$activeTheme/pages/$fname[0]/$file");
//            }
//
//            foreach ($file->find("a") as $element) {
//
//                if (isset($element->href)) {
//                    if (!is_array($fname) && !strpos($element->href, $fname) && !strpos($element->href, '@')) {
//                        echo "<a href='/{$element->href}'>{$element->href}</a><br>";
//                    }
//
//                    if (!is_array($fname) && !strpos($element->href, $fname[0]) && !strpos($element->href, '@')) {
//                        echo "<a href='/{$element->href}' target='_blank'>{$element->href}</a><br>";
//                    }
//
//                }
//            }
//
//        }
    }

#--------------------------------------------------------------------------------------------------------------------------------------

    static function _linkToDownload($link, $linkis)
    {
        ini_set('max_execution_time', 300);

        $fname = self::_fileName($link);

        $file = file_get_html($link);

        $links = [];

        foreach ($file->find("a") as $element) {

            if (isset($element->href)) {
                $linkName = self::_fileName($element->href);

                $links = array_unique($links);
                if (!in_array($element->href, $linkis)) {
                    if (is_array($linkName)) {
                        if (!is_array($fname) && $fname == $linkName[0] && !strpos($element->href, '@')) {
                            array_push($linkis, $element->href);
                        }

                        if (is_array($fname) && $fname[0] == $linkName[0] && !strpos($element->href, '@')) {
                            array_push($linkis, $element->href);
                        }
                    }

                    if (!is_array($linkName)) {
                        if (!is_array($fname) && $fname == $linkName && !strpos($element->href, '@')) {
                            array_push($linkis, $element->href);
                        }

                        if (is_array($fname) && $fname[0] == $linkName && !strpos($element->href, '@')) {
                            array_push($linkis, $element->href);
                        }
                    }
                }

            }
        }

        $linkis = array_unique($linkis);
        return $linkis;
    }

}
