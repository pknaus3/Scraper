<?php


namespace Wezeo\Websitescraper\Controllers\Classes;

class AssetsToDownload
{
    static function _dataToUpdate($file, $fname, $path){
        //Update Images
        foreach ($file->find("img") as $element) {
            if (isset($element->src)) {
                $element->src  = ManageAssetsAndPages::_replaceLink($fname, $element->src, $path);
            }
        }

        foreach ($file->find("audio") as $element) {
            if (isset($element->src)) {
                $element->src  = ManageAssetsAndPages::_replaceLink($fname, $element->src, $path);
            }
        }

        foreach ($file->find("embed") as $element) {
            if (isset($element->src)) {
                $element->src  = ManageAssetsAndPages::_replaceLink($fname, $element->src, $path);
            }
        }

        foreach ($file->find("iframe") as $element) {
            if (isset($element->src)) {
                $element->src  = ManageAssetsAndPages::_replaceLink($fname, $element->src, $path);
            }
        }

        foreach ($file->find("input") as $element) {
            if (isset($element->src)) {
                $element->src  = ManageAssetsAndPages::_replaceLink($fname, $element->src, $path);
            }
        }

        foreach ($file->find("source") as $element) {
            if (isset($element->src)) {
                $element->src  = ManageAssetsAndPages::_replaceLink($fname, $element->src, $path);
            }
        }

        foreach ($file->find("track") as $element) {
            if (isset($element->src)) {
                $element->src  = ManageAssetsAndPages::_replaceLink($fname, $element->src, $path);
            }
        }

        foreach ($file->find("video") as $element) {
            if (isset($element->src)) {
                $element->src  = ManageAssetsAndPages::_replaceLink($fname, $element->src, $path);
            }
        }

        foreach ($file->find("script") as $element) {
            if (isset($element->src)) {
                $element->src  = ManageAssetsAndPages::_replaceLink($fname, $element->src, $path);
            }
        }

        //Update stylesheet
        foreach ($file->find("link") as $element) {
            if ($element->rel == "stylesheet" && isset($element->href)) {
                $element->href = ManageAssetsAndPages::_replaceLink($fname, $element->href, $path);
            }
            if ($element->rel == "icon" && isset($element->href)) {
                $element->href = ManageAssetsAndPages::_replaceLink($fname, $element->href, $path);
            }
        }

    }

    static function _dataToDownload($html, $fname, $link, $path, $tempSize) {

        foreach ($html->find("img") as $element) {
            $type = 'IMAGE';
            if (isset($element->src)) {
                try {
                    $tempSize = DownloadAssets::_saveAsset($element->src, $fname, $link, $path, $tempSize, $type);
                } catch (\Exception $e) {
                    dump($element->src);
                    continue;
                }
            }
        }

        foreach ($html->find("audio") as $element) {
            $type = 'AUDIO';
            if (isset($element->src)) {
                try {
                    $tempSize = DownloadAssets::_saveAsset($element->src, $fname, $link, $path, $tempSize, $type);
                } catch (\Exception $e) {
                    dump($element->src);
                    continue;
                }
            }
        }

        foreach ($html->find("script") as $element) {
            if (isset($element->src) == 1) {
                $type = 'SCRIPT';
                try {
                    $tempSize = DownloadAssets::_saveAsset($element->src, $fname, $link, $path, $tempSize, $type);
                } catch (\Exception $e) {
                    dump($element->src);
                    continue;
                }
            }
        }

        foreach ($html->find("embed") as $element) {
            if (isset($element->src) == 1) {
                $type = 'EMBED';
                try {
                    $tempSize = DownloadAssets::_saveAsset($element->src, $fname, $link, $path, $tempSize, $type);
                } catch (\Exception $e) {
                    dump($element->src);
                    continue;
                }
            }
        }

        foreach ($html->find("iframe") as $element) {
            if (isset($element->src) == 1) {
                $type = 'IFRAME';
                try {
                    $tempSize = DownloadAssets::_saveAsset($element->src, $fname, $link, $path, $tempSize, $type);
                } catch (\Exception $e) {
                    dump($element->src);
                    continue;
                }
            }
        }

        foreach ($html->find("input") as $element) {
            if (isset($element->src) == 1) {
                $type = 'INPUT';
                try {
                    $tempSize = DownloadAssets::_saveAsset($element->src, $fname, $link, $path, $tempSize, $type);
                } catch (\Exception $e) {
                    dump($element->src);
                    continue;
                }
            }
        }

        foreach ($html->find("source") as $element) {
            if (isset($element->src) == 1) {
                $type = 'SOURCE';
                try {
                    $tempSize = DownloadAssets::_saveAsset($element->src, $fname, $link, $path, $tempSize, $type);
                } catch (\Exception $e) {
                    dump($element->src);
                    continue;
                }
            }
        }

        foreach ($html->find("track") as $element) {
            if (isset($element->src) == 1) {
                $type = 'TRACK';
                try {
                    $tempSize = DownloadAssets::_saveAsset($element->src, $fname, $link, $path, $tempSize, $type);
                } catch (\Exception $e) {
                    dump($element->src);
                    continue;
                }
            }
        }

        foreach ($html->find("video") as $element) {
            if (isset($element->src) == 1) {
                $type = 'VIDEO';
                try {
                    $tempSize = DownloadAssets::_saveAsset($element->src, $fname, $link, $path, $tempSize, $type);
                } catch (\Exception $e) {
                    dump($element->src);
                    continue;
                }
            }
        }

        foreach ($html->find("link") as $element) {
            if ($element->rel == "stylesheet") {
                $type = 'CSS';
                if (isset($element->href)) {
                    try {
                        $tempSize = DownloadAssets::_saveAsset($element->href, $fname, $link, $path, $tempSize, $type);
                    } catch (\Exception $e) {
                        dump($element->href);
                        continue;
                    }
                }
            }
            if ($element->rel == "icon") {
                $type = 'ICON';
                if (isset($element->href)) {
                    try {
                        $tempSize = DownloadAssets::_saveAsset($element->href, $fname, $link, $path, $tempSize, $type);
                    } catch (\Exception $e) {
                        dump($element->href);
                        continue;
                    }
                }
            }
        }

        return $tempSize;
    }



}
