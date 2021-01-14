<?php


namespace Wezeo\Websitescraper\Controllers\Classes;

use Wezeo\Websitescraper\Controllers\Classes\ManageAssetsAndPages;
use Wezeo\Websitescraper\Controllers\Classes\DownloadUtils;

class DownloadAssets
{
    //This find assets
#--------------------------------------------------------------------------------------------------------------------------------------
    static function _findAssetsInHtml($html, $link, $fname, $path, $tempSize)
    {
        // Find all images
        $tempSize = AssetsToDownload::_dataToDownload($html, $fname, $link, $path, $tempSize);

        return $tempSize;
    }
#--------------------------------------------------------------------------------------------------------------------------------------

    //Get asset content
#--------------------------------------------------------------------------------------------------------------------------------------
    static function _saveAsset($sourceLink, $fname, $link, $path, $tempSize, $type)
    {
        if (substr($sourceLink, 0, 1) == "/" or substr($sourceLink, 0, 1) == ".") {
            if (substr($sourceLink, 0, 1) == ".") {
                $sourceLink = str_replace(".", "", $sourceLink);
            }

            if (substr($sourceLink, 0, 2) == "/.") {
                $sourceLink = str_replace("/.", "", $sourceLink);
            }

            if (substr($sourceLink, 0, 2) == "//") {
                $sourceLink = str_replace("//", "", $sourceLink);
                $content    = file_get_contents("https://".$sourceLink);
                $sourceLink = "https://".$sourceLink;
                $echo = "https://".$sourceLink;
            } else {
                try {
                    if (!is_array($fname)) {
                        $content    = file_get_contents('https://'.$fname.$sourceLink);
                        $sourceLink = 'https://'.$fname.$sourceLink;
                        $echo       = 'https://'.$fname.$sourceLink;
                    }
                    if (is_array($fname)) {
                        $content    = file_get_contents('https://'.$fname[0].$sourceLink);
                        $sourceLink = 'https://'.$fname.$sourceLink;
                        $echo       = 'https://'.$fname[0].$sourceLink;
                    }
                } catch (\Exception $e) {
                    if (!is_array($fname)) {
                        $content    = file_get_contents('http://'.$fname.$sourceLink);
                        $sourceLink = 'http://'.$fname.$sourceLink;
                        $echo       = 'http://'.$fname.$sourceLink;
                    }
                    if (is_array($fname)) {
                        $content     = file_get_contents('http://'.$fname[0].$sourceLink);
                        $sourceLink  = 'http://'.$fname[0].$sourceLink;
                        $echo        = 'http://'.$fname[0].$sourceLink;
                    }
                }
            }
        } else {
            $content = file_get_contents($sourceLink);
            $echo = $sourceLink;
        }

        self::_saveAssetContentInFolder($path, $fname, $content, $sourceLink);
        $fileSize = strlen($content)/1024;
        $fileSize = round($fileSize);
        $tempSize = $tempSize + $fileSize;

        echo '<span><b>' . $type . ':</b> (' . $fileSize . 'kB) ' . $echo . "</span><br>";

        return $tempSize;
    }
#--------------------------------------------------------------------------------------------------------------------------------------

    //This save assets
#--------------------------------------------------------------------------------------------------------------------------------------
    static function _saveAssetContentInFolder($path, $fname, $content, $sourceLink)
    {
        $sourceLink = DownloadUtils::_fileName($sourceLink);
        if (!is_array($fname)) {
            try {
                $pos = in_array($fname, $sourceLink);
            } catch (\Exception $e) {
                $pos = strpos($sourceLink[0], $fname);
            }
        }

        if (is_array($fname)) {
            try {
                $pos = in_array($fname[0], $sourceLink);
            } catch (\Exception $e) {
                $pos = strpos($sourceLink[0], $fname[0]);
            }
        }

        if ($pos) {
            if (is_array($sourceLink)) {
                $lastElementOfSourceLink     = end($sourceLink);
                $pos                         = strpos($lastElementOfSourceLink, '?');
                if ($pos) {
                    $lastElementOfSourceLink = substr_replace($lastElementOfSourceLink, "", $pos, 1000);
                }
                array_pop($sourceLink);
                $sourceLink = implode('/', $sourceLink);
                if (!is_dir($path."/assets/{$sourceLink}")) {
                    \File::makeDirectory($path."/assets/{$sourceLink}", 0755, true, true);
                }

                if (!is_file("{$path}/assets/{$sourceLink}/{$lastElementOfSourceLink}")) {
                    try {
                        file_put_contents("{$path}/assets/{$sourceLink}/{$lastElementOfSourceLink}", $content);
                    } catch (\Exception $e) {
                        dump('Nepodarilo sa ulozit tvoj subor');
                    }
                }
            } else {
                if (!is_dir($path."/assets/{$sourceLink}")) {
                    \File::makeDirectory($path."/assets/{$sourceLink}", 0755, true, true);
                }
                if (!is_file("{$path}/assets/{$sourceLink}")) {
                    try {
                        file_put_contents("{$path}/assets/{$sourceLink}", $content);
                    } catch (\Exception $e) {
                        dump('Nepodarilo sa ulozit tvoj subor');
                    }
                }
            }
        }
    }
#--------------------------------------------------------------------------------------------------------------------------------------
}
