<?php

Loader::model("page_list");

class UrlsHelper {

    public function getPageUrl($path) {
        $nh = Loader::helper("navigation");
        return $nh->getCollectionURL(Page::getByPath($path));
    }

    public function getToolsUrl($tool, $pkg) {
        return Loader::helper("concrete/urls")->getToolsUrl($tool, $pkg);
    }

}
?>
