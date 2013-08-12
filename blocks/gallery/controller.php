<?php 
defined('C5_EXECUTE') or die("Access Denied.");
class GalleryBlockController extends BlockController {
	
	protected $btTable = 'nuevebit_btGallery';
	protected $btInterfaceWidth = "800";
	protected $btInterfaceHeight = "500";
	protected $btCacheBlockRecord = true;
	protected $btCacheBlockOutput = true;
	protected $btCacheBlockOutputOnPost = true;
//	protected $btCacheBlockOutputForRegisteredUsers = true;

	protected $btExportFileColumns = array('fID');
	protected $btExportTables = array('nuevebit_btGallery','nuevebit_btGallerySets');

    private $selectedMonth = null;
    private $selectedYear = null;
    private $currentPages = 0;
    private $totalCount = 0;

    const MAX_SETS_PER_PAGE = 11;

	/** 
	 * Used for localization. If we want to localize the name/description we have to include this
	 */
	public function getBlockTypeDescription() {
		return t("Nuevebit Image Gallery based on multiple FileSets");
	}
	
	public function getBlockTypeName() {
		return t("Nuevebit FileSet Gallery");
	}

    public function on_page_view() {
        $html = Loader::helper("html");
        $this->addHeaderItem($html->javascript("galleria.js", "nuevebit"));
        $this->addFooterItem($html->javascript("nuevebit.js", "nuevebit"));
    }
	
	public function getJavaScriptStrings() {
		return array(
			'choose-file' => t('Choose Image/File'),
			'choose-min-2' => t('Please choose at least two images.'),
			'choose-fileset' => t('Please choose a file set.')
		);
	}
	
	function delete(){
		$db = Loader::db();
		$db->query("DELETE FROM nuevebit_btGallerySets WHERE bID=".intval($this->bID));		
		parent::delete();
	}
	
	function view() {
        if ($this->get("month")) {
            $this->selectedMonth = filter_var($this->get("month"), FILTER_SANITIZE_NUMBER_INT);
        }
        
        if ($this->get("year")) {
            $this->selectedYear = filter_var($this->get("year"), FILTER_SANITIZE_NUMBER_INT);
        }
        
        $availableYears = $this->getAvailableYears();
        $yearInRange = $this->selectedYear >= $availableYears[0] && $this->selectedYear <= $availableYears[count($availableYears) - 1];
        
        if (!$this->selectedYear || !$yearInRange) {
            $this->selectedYear = $availableYears[count($availableYears) - 1];
        }
        
        $availableMonths = $this->getAvailableMonths($this->selectedYear);
        $monthInRange = $this->selectedMonth >= $availableMonths[0] && $this->selectedMonth <= $availableMonths[count($availableMonths) - 1];
        
        if (!$this->selectedMonth || !$monthInRange) {
            $this->selectedMonth = null;
        } 

        // pagination information
        $this->currentPages = filter_var($this->get("page"), FILTER_SANITIZE_NUMBER_INT);
        
        if ($this->currentPages < 0) {
            $this->currentPages = 0;
        }
        $this->set("currentPage", $this->currentPages);
        
        $totalSetsCount = $this->countTotalSets($this->selectedYear, $this->selectedMonth);
        $this->set("totalSetsCount", $totalSetsCount);
        $this->set("maxSetsCount", self::MAX_SETS_PER_PAGE);

        $this->set("selectedYear", $this->selectedYear);
        $this->set("selectedMonth", $this->selectedMonth);
        $this->set("availableYears", $availableYears);
        $this->set("availableMonths", $availableMonths);
        
        $this->set("thumbnails", $this->getSelectedThumbnails($this->selectedYear, $this->selectedMonth));
        $this->set("selectedFileSets", $this->getSelectedFileSets($this->selectedYear, $this->selectedMonth));

	}

    public function action_change_date() {

        $this->view();
    }

	function add() {
        $this->loadEditInformation();
	}
	
	function edit() {
        $this->loadEditInformation();
	}

    private function loadEditInformation() {
        $this->set("fileSets", $this->getAvailableFileSets());
        $this->set("selectedFileSets", $this->getSelectedFileSets());
    }

    private function getAvailableFileSets() {
        Loader::model('file_set');
        return FileSet::getMySets();
    }

    private function getSelectedFileSets($year = null, $month = null) {
        $db = Loader::db();
        $values = array();
        $sql = "select s.id, fs.fsID, fs.fsName, s.showThumbnail, UNIX_TIMESTAMP(s.sdate) as sdate from nuevebit_btGallerySets as s, FileSets as fs where s.bID = ? and fs.fsID = s.fsID ";
        $values[] = $this->bID;

        if ($year) {
            $sql .= "and YEAR(s.sdate) = ? ";
            $values[] = $year;
        }

        if ($month) {
            $sql .= "and MONTH(s.sdate) = ? ";
            $values[] = $month;
        }
        
        $sql .= "order by s.sdate, fs.fsName ";
        $count = $this->countTotalSets($year, $month);

        if ($count > self::MAX_SETS_PER_PAGE && $year) {
            $offset = $this->currentPages * self::MAX_SETS_PER_PAGE;
            $sql .= "LIMIT $offset, " . self::MAX_SETS_PER_PAGE; // usamos LIMIT por razones de rendimiento
        }

        return $db->GetAll($sql, $values);
    }

    private function getSelectedThumbnails($year = null, $month = null) {
        $db = Loader::db();
        $values = array($this->bID);
        $sql = "select s.id, f.fID from nuevebit_btGallerySets as s, Files as f, FileSets as fs, FileSetFiles as fsf where s.fsID = fs.fsID and f.fID = fsf.fID and fs.fsID = fsf.fsID and s.showThumbnail = 1 and s.bID = ? ";
        
        if ($year) {
            $sql .= "and YEAR(s.sdate) = ? ";
            $values[] = $year;
        }

        if ($month) {
            $sql .= "and MONTH(s.sdate) = ?";
            $values[] = $month;
        }
        
        $sql .= "group by s.id ";
        $count = $this->countTotalSets($year, $month);

        if ($count > self::MAX_SETS_PER_PAGE && $year) {
            $offset = $this->currentPages * self::MAX_SETS_PER_PAGE;
            $sql .= "LIMIT $offset, " . self::MAX_SETS_PER_PAGE; // usamos LIMIT por razones de rendimiento
        }

        return $db->GetAssoc($sql, $values);
    }

    private function countTotalSets($year = null, $month = null) {
        if ($this->totalCount == 0) {
            $db = Loader::db();
            $values = array($this->bID);
            $sql = "select count(*) from nuevebit_btGallerySets as s where " .
               "s.bID = ? ";

            if ($year) {
               $sql .= "and YEAR(s.sdate) = ? ";
               $values[] = $year;
            }

            if ($month) {
               $sql .= "and MONTH(s.sdate) = ? ";
               $values[] = $month;
            }

            $this->totalCount = intval($db->GetOne($sql, $values));
        }

        return $this->totalCount;
    }

    private function getAvailableYears() {
        $db = Loader::db();
        $sql = "select YEAR(s.sdate) as year from nuevebit_btGallerySets as s where s.bID = ? group by YEAR(s.sdate)";

        return $db->GetCol($sql, array($this->bID));
    }

    private function getAvailableMonths($year) {
        $db = Loader::db();
        $sql = "select MONTH(s.sdate) as month from nuevebit_btGallerySets as s where " .
            "YEAR(s.sdate) = ? and s.bID = ? group by MONTH(s.sdate)";

        return $db->GetCol($sql, array($year, $this->bID));
    }
	
	function duplicate($nbID) {
		parent::duplicate($nbID);
	}
	
	function save($data) { 
		$db = Loader::db();
		
        //delete existing sets
        $db->query("DELETE FROM nuevebit_btGallerySets WHERE bID=".intval($this->bID));

        foreach ($data["fileSets"] as $fsId) {
            $showThumbnail = $data["fileSetShowThumbnail_$fsId"] == "true";
            $date = $data["fileSetDate_$fsId"];
            
            $sql = "insert into nuevebit_btGallerySets (bID, fsID, showThumbnail, sdate) " .
                "values (?, ?, ?, FROM_UNIXTIME(?))";

            $db->Execute($sql, array($this->bID, intval($fsId), $showThumbnail, $date));
        }
		
		parent::save($data);
	}
	
}

?>
