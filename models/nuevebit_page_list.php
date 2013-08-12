<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class NuevebitPageList extends PageList {

    private $includeStatistics;

    protected function setBaseQuery($additionalFields = "") {
        if ($this->includeStatistics) {
            $additionalFields .= ", (select count(*) from PageStatistics ps where ps.cID = p1.cID) as visits";
        }
        
        parent::setBaseQuery($additionalFields);

    }
    public function getIncludeStatistics() {
        return $this->includeStatistics;
    }

    public function setIncludeStatistics($includeStatistics) {
        $this->includeStatistics = $includeStatistics;
    }

    public function sortByRand() {
		parent::sortBy('rand()', "asc");
    }

    public function sortByVisits($dir="desc") {
        $this->includeStatistics = true;
        
        parent::sortBy("visits", $dir);
    }

}
?>
