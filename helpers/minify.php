<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

defined('C5_EXECUTE') or die("Access Denied.");

class MinifyHelper {

    private $includedItems = array();

    public function __construct() {
    }

    /**
     * 
     * @param type $sources HeaderOutputItems
     */
    public function outputItems($sources, $type) {
        $this->includeItems($sources, $type);
    }

    public function getFileInfo($source) {
        $type = null; //default

        if ($source instanceof CSSOutputObject) {
            $type = "css";
        } else if ($source instanceof JavaScriptOutputObject) {
            $type = "js";
        }

        $pkg = null;
        $packagesRel = DIR_REL . '/packages';
        $packagesCoreRel = ASSETS_URL . '/packages';

        $posRel = strpos($source->file, $packagesRel);
        $posCore = strpos($source->file, $packagesCoreRel);

        if ($posRel !== false || $posCore !== false) {
            list($prefix, $pkg) = explode('packages/', $source->file, 2);
            $pkg = substr($pkg, 0, strpos($pkg, '/'));
        }

        list($name) = explode('?v=', $source->file, 2);
        $name = $this->getFileName($name, $type, $pkg);

        return array($name, $type, $pkg);
    }

    private function getFileName($source, $type, $pkgHandle) {
		$v = View::getInstance();
        $replace = "";

        if ($type == "css") {
            $dirname = DIRNAME_CSS;
            $assetsUrl = ASSETS_URL_CSS;
        } else {
            $dirname = DIRNAME_JAVASCRIPT;
            $assetsUrl = ASSETS_URL_JAVASCRIPT;
        }
        
        if ($v->getThemeDirectory() != '' && strpos($source, $v->getThemePath()) !== false) {
            $replace = $v->getThemePath() . '/';
        } else if (strpos($source, DIR_REL . '/' . $dirname) !== false) {
            $replace = DIR_REL . '/' . $dirname . '/';
        } else if ($pkgHandle) {
            if (strpos($source, DIR_BASE . '/' . DIRNAME_PACKAGE . '/') !== false) {
                $replace = DIR_REL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . $dirname . '/';
            } else if (strpos($source, ASSETS_URL . '/' . DIRNAME_PACKAGES) !== false) {
                $replace = ASSETS_URL . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . $dirname . '/';
            }
        }

        if (!$replace) {
            $replace = $assetsUrl . '/';
        }

        return str_replace($replace, "", $source);
    }

    private function includeItems($sources, $type) {
        if (defined('MINIFY_ENABLE') && MINIFY_ENABLE) {
            list($cssUrl, $jsUrl) = $this->minifyUrl($sources, $type);

            if ($cssUrl != null && $type == "css") {
                print "<link rel='stylesheet' type='text/css' href='$cssUrl' />";
            } else if ($jsUrl != null && $type == "js") {
                print "<script src='$jsUrl' type='text/javascript'></script> ";
            }
        } else {
            foreach ($sources as $source) {
                print $source;
            }
        }
    }

    private function minifyUrl($sources, $urlType) {
        $targetFiles = array();
        $targetFiles["css"] = "";
        $targetFiles["js"] = "";

        foreach ($sources as $source) {
            list($name, $type, $pkg) = self::getFileInfo($source);

            // if no type can be assumed we fail
            // also, we do not minify tiny_mce or we get errors!
            if (!$type || $name == "tiny_mce/tiny_mce.js") {
                print $source;
                continue;
            }

            if ($type != $urlType) {
                continue;
            }

            // avoid including items more than once
            if (array_search($name, $this->includedItems)) {
                continue;
            }

            if (defined('MINIFY_USE_CDN') && MINIFY_USE_CDN) {
                // exclude CDN items
                if ($name == "jquery.js") {
                    continue;
                }
            }

            // since we compile less files, we can avoid including the js compiler
            if (preg_match("/\/less(-.*)?(.min)?.js$/", $name) == 1) {
                continue;
            }

            $this->includedItems[] = $name;
            
            if ($pkg) {
                $name .= ";$pkg";
            }

            $this->addTargetFile($targetFiles, $name, $type);
        }

        if (defined('MINIFY_SCRIPT')) {
            $url = DIR_REL . '/' . MINIFY_SCRIPT . '?f=';
        } else {
            $uh = Loader::helper("concrete/urls");
            $url = $uh->getToolsUrl("minify", "nuevebit") . "?f=";
        }
        
        $currentTheme = View::getInstance()->getThemeHandle();

        if ($targetFiles["css"]) {
            $cssUrl = $url . $targetFiles["css"] . "&amp;t=css&amp;v=$currentTheme";
        } else {
            $cssUrl = null;
        }
        
        if ($targetFiles["js"]) {
            $jsUrl = $url . $targetFiles["js"] . "&amp;t=js&amp;v=$currentTheme";
        } else {
            $jsUrl = null;
        }
        
        return array($cssUrl, $jsUrl);
    }

    private function addTargetFile(&$targetFiles, $file, $type) {
        if (!$targetFiles[$type]) {
            $targetFiles[$type] = $file;
        } else {
            $targetFiles[$type] .= ",$file";
        }
    }
}

?>
