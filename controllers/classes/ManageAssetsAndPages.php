<?php


namespace Wezeo\Websitescraper\Controllers\Classes;

use Cms\Classes\Theme;
use October\Rain\Support\Facades\Flash;
use Wezeo\Websitescraper\Controllers\Classes\DownloadUtils;
use Wezeo\Websitescraper\Controllers\Classes\DownloadAssets;

class ManageAssetsAndPages
{
    //Update link in assets, add ocms header to file
#--------------------------------------------------------------------------------------------------------------------------------------
    static function _replaceLink($fname, $sourceLink, $path)
    {
        if (substr($sourceLink, 0, 1) == "/" or substr($sourceLink, 0, 1) == ".") {
            if (substr($sourceLink, 0, 1) == ".") {
                $sourceLink = str_replace(".", "", $sourceLink);
                if (is_array($fname)) {
                    $sourceLink = $fname[0] . $sourceLink;
                } else {
                    $sourceLink = $fname . $sourceLink;
                }
            }

            if (substr($sourceLink, 0, 2) == "/.") {
                $sourceLink = str_replace("/.", "", $sourceLink);
            }

            if (substr($sourceLink, 0, 2) == "//") {
                return $sourceLink;
            }
            if (is_array($fname)) {
                $sourceLink = 'http://'.$fname[0].$sourceLink;
            } else {
                $sourceLink = 'http://'.$fname.$sourceLink;
            }

        }

        if (is_array($fname)) {
            $pos = strpos($sourceLink, $fname[0]);
        } else {
            $pos = strpos($sourceLink, $fname);
        }

        if (!$pos) {
            return $sourceLink;
        }

        if (!is_array($fname)) {
            $pos = strpos($sourceLink, $fname);
            if ($pos) {
                $asset = DownloadUtils::_fileName($sourceLink);

                if (is_array($asset)) {
                    $asset = implode('/', $asset);
                }
            }
        }

        //If array
        if (is_array($fname)) {
            $pos = strpos($sourceLink, $fname[0]);
            if ($pos) {

                $asset = DownloadUtils::_fileName($sourceLink);

                if (is_array($asset) == 1) {
                    $asset = implode('/', $asset);
                }

            }
        }
        return '/'.$path.'/assets/'.$asset;
    }

    static function _updateAssetsLinks($path, $fname, $title, $check, $tempSize)
    {
        $file = file_get_html($path."/tempIndex.html", "r+");

        AssetsToDownload::_dataToUpdate($file, $fname, $path);

        foreach ($file->find("a") as $element) { // vyriesit if-ka
            if (isset($element->href)) {
                //If string
                if (!is_array($fname)) {
                    $pos = strpos($element->href, $fname);
                    if ($pos) {
                        //Index - https://summergamedev.sk/login -> summergamedev.sk/login / /login
                        if ($check) {
                            $element->href     = str_replace("http://{$fname}", "", $element->href);
                            $element->href     = str_replace("https://{$fname}", "", $element->href);
                            if (substr($element->href, -1) != '/') {
                                $element->href = $element->href . '/';
                            }
                        } else {
                            $element->href     = str_replace("http://", "", $element->href);
                            $element->href     = str_replace("https://", "", $element->href);
                            $element->href     = rtrim($element->href, '/');
                        }
                    }
                }
                //If array
                if (is_array($fname)) {
                    $pos = strpos($element->href, $fname[0]);
                    if ($pos) {
                        //Others - https://summergamedev.sk/login -> Index (summergamedev.sk) -> summergamedev.sk
                        if ($check) {
                            $element->href     = str_replace("http://{$fname[0]}", "", $element->href);
                            $element->href     = str_replace("https://{$fname[0]}", "", $element->href);
                            if (substr($element->href, -1) != '/') {
                                $element->href = $element->href . '/';
                            }
                        } else {
                            $element->href     = str_replace("http:/", "", $element->href);
                            $element->href     = str_replace("https:/", "", $element->href);
                            $element->href     = rtrim($element->href, '/');
                        }
                    }
                }
            }
        }

        //Save pages
        //If fname is string
        if (!is_array($fname)) {
            file_put_contents("{$path}/pages/{$fname}/index.htm", $file);

            $file1    = file_get_contents("{$path}/pages/{$fname}/index.htm");
            $filepos  = strpos($file1, '<');
            $file1    = substr_replace($file1, "", 0, $filepos);

            file_put_contents("{$path}/pages/{$fname}/index.htm", $file1);
        }

        //If fname is array
        if (is_array($fname)) {
            $lastPage = end($fname);
            $fname0   = $fname[0];

            file_put_contents("{$path}/pages/{$fname0}/{$lastPage}.htm", $file);

            $file1    = file_get_contents("{$path}/pages/{$fname0}/{$lastPage}.htm");
            $filepos  = strpos($file1, '<');
            $file1    = substr_replace($file1, "", 0, $filepos);

            file_put_contents("{$path}/pages/{$fname0}/{$lastPage}.htm", $file1);
        }
        //Delete TempIndex file
        unlink("{$path}/tempIndex.html");

        if (!is_array($fname)) {
            $file     = fopen("{$path}/pages/{$fname}/index.htm", 'r+');
        }

        if (is_array($fname)) {
            $lastPage = end($fname);
            $file     = fopen("{$path}/pages/{$fname[0]}/{$lastPage}.htm", 'r+');
        }



        fwrite($file, "title = '{$title}'
");
        $fileSize = strlen($file1)/1024;
        $fileSize = round($fileSize);
        $tempSize = $tempSize + $fileSize;
        $tempSize = round($tempSize);
        //If fname is string
        if (!is_array($fname)) {
            if ($check) {
                fwrite($file, "url = '/'"."\n"."is_hidden = 0"."\n"."=="."\n".$file1);
                echo "<h3><a href='/' target=\"_BLANK\">{$fname}</a> - {$fileSize} kB</h3><br>
            <h5>Veľkosť stránky = {$tempSize}kB </h5>
            <br><br>";
            } else {
                fwrite($file, "url = '/{$fname}'"."\n"."is_hidden = 0"."\n"."=="."\n".$file1);
                echo "<h3>Stiahnuta stranka: <a href='/{$fname}' target=\"_BLANK\">/{$fname}</a> - {$fileSize} kB</h3><br>
            <h5>Veľkosť stiahnutej stránky = {$tempSize}kB </h5>
            <br><hr id=\"spacer\"><br><br><br><br><br>";
            }
        }

        //If fname is array
        if (is_array($fname)) {
            if ($check) {
                unset($fname[0]);
            }
            $implodedFname = implode('/', $fname);
            fwrite($file, "url = '/{$implodedFname}'"."\n"."is_hidden = 0"."\n"."=="."\n".$file1);
            echo "<h3>Stiahnuta stranka: <a href='/{$implodedFname}' target=\"_BLANK\">/{$implodedFname}</a> - {$fileSize} kB</h3><br>
            <h5>Veľkosť stiahnutej stránky = {$tempSize} kB </h5>
            <br><hr id=\"spacer\"><br><br><br><br><br>";
        }
        fclose($file);
        return $tempSize;
    }
#--------------------------------------------------------------------------------------------------------------------------------------
}
