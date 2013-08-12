<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$block = null;
$page = null;
$view = "view";

if (isset($_GET["bID"]) && $_GET["bID"]) {
    $bID = filter_var($_GET["bID"], FILTER_SANITIZE_NUMBER_INT);
    $areaHandle = "Main";

    if (isset($_GET["view"]) && $_GET["view"]) {
        $view = filter_var($_GET["view"], FILTER_SANITIZE_STRING);
        $view = str_replace("../", "", $view); //security measures
    }

    if (isset($_GET["cID"]) && $_GET["cID"]) {
        $cID = filter_var($_GET["cID"], FILTER_SANITIZE_NUMBER_INT);
        $page = Page::getByID($cID);

    } 
    
    if (isset($_GET["aHandle"]) && $_GET["aHandle"]) {
        $areaHandle = filter_var($_GET["aHandle"], FILTER_SANITIZE_STRING);
    }
    
    $block = Block::getByID($bID, $page, $areaHandle);
} 

if ($block) {
    $bp = new Permissions($block);

    if ($bp->canRead()) {
        // makes Page::getCurrentPage() and friends work
        global $c;
        $c = $page;
        
        if ($view != "view") {
            // if we don't do this, $block->bFilename will always override
            // the template passed in $view
            $block->bFilename = null;
        }
        
        $block->display($view);
    }
}

?>
