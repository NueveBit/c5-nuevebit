<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/*
$page = Page::getByID(124);
global $c;

$cp = new Permissions($page);

if ($cp->canRead()) {
    $c = $page;
    $v = View::getInstance();
    $v->render($page);
}
*/

Loader::model("page_list");
$pageList = new PageList();

//$pageList->filterByAttribute("article_type", "\n%Featured%", "like");
$pageList->filterByAttribute("gallery_year", 2012);
//$pageList->filterByAttribute("gallery_month", "%enero%", "like");
$pages = $pageList->get();
var_dump($pages);


?>
