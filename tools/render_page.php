<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

define(NUEVEBIT_AJAX_RENDER, true);

$page = null;

if (isset($_GET["cID"]) && $_GET["cID"]) {
    $cID = filter_var($_GET["cID"], FILTER_SANITIZE_NUMBER_INT);
    $page = Page::getByID($cID);
} else if (isset($_GET["url"]) && $_GET["url"]) { // lets try to guess the path...
    $url = filter_var($_GET["url"], FILTER_SANITIZE_STRING);
    $url = str_replace(BASE_URL, "", $url);
    $path = str_replace(DIR_REL, "", $url);
    
    // it's safe to try to guess the url, since we are not actually
    // hitting the filesystem, if the path guessed is not recognized by
    // concrete5 (e.g. is not safe), it'll fail to retrieve a Page instance
    $page = Page::getByPath($path);
}

if ($page) {
    global $c;

    $cp = new Permissions($page);

    if ($cp->canRead()) {
        $c = $page;
        $v = View::getInstance();
        $v->render($page);
    }
}

?>
