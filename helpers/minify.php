<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

defined('C5_EXECUTE') or die("Access Denied.");

class MinifyHelper {

    const CACHE_DIR = "";

    public function __construct() {
        $this->css = array();
        $this->js = array();
        $this->uh = Loader::helper("concrete/urls");
    }

    public function getCssUrl() {
        return $this->minifyUrl($this->css, "css");
    }

    public function getJsUrl() {
        return $this->minifyUrl($this->js, "js");
    }

    public function includeCss($enabled = true) {
        $url = $this->getCssUrl();
        echo "<link rel='stylesheet' type='text/css' href='$url' />";
    }

    public function includeJs($enable = true) {
        $url = $this->getJsUrl();
        echo "<script src='$url' type='text/javascript'></script> ";
    }
    
    private function minifyUrl($sources, $type, $enabled = true) {
        $targetFiles = "";

        foreach ($sources as $source) {
            $f = ($source->pkg) ? $source->name . ";" . $source->pkg : $source->name;
            
            if (!$targetFiles) {
                $targetFiles = $f;
            } else {
                $targetFiles .= "$targetFiles,$f";
            }
        }

        $url = $this->uh->getToolsUrl("minify", "nuevebit") . "?f=$targetFiles";
        return $url . "&amp;t=$type";
    }

    public function css($filename, $pkgHandle = NULL) {
        $this->css[] = new TargetFile($filename, $pkgHandle);
    }

    public function javascript($filename, $pkgHandle = NULL) {
        $this->js[] = new TargetFile($filename, $pkgHandle);
    }
}

class TargetFile {

    public $name = '';
    public $pkg = NULL;
    
    public function __construct($name, $pkg="") {
        $this->name = $name;
        $this->pkg = $pkg;
    }

}

?>
