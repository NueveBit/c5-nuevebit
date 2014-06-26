<?php

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * TODO: Implementar duplicate()
 */
class SearchablePageListBlockController extends BlockController {

    protected $btTable = 'nuevebit_btPageList';
    protected $btInterfaceWidth = "500";
    protected $btInterfaceHeight = "350";
    protected $btExportPageColumns = array('cParentID');
    protected $btExportPageTypeColumns = array('ctID');
    protected $btCacheBlockRecord = true;
    protected $btExportTables = array('nuevebit_btPageList', 'nuevebit_btPageListAttribute', 'nuevebit_btPageListFilter');

    public $excludes = array(); // list of page ids to exclude
    public $filters = array();
    
    /**
     * Used for localization. If we want to localize the name/description we have to include this
     */
    public function getBlockTypeDescription() {
        return t("List pages based on type, area. Allows users to filter results.");
    }

    public function getBlockTypeName() {
        return t("Searchable Page List");
    }

    public function getJavaScriptStrings() {
        return array(
            'feed-name' => t('Please give your RSS Feed a name.')
        );
    }

    function getPages($query = null) {
        Loader::model('page_list');
        $db = Loader::db();
        $bID = $this->bID;
        if ($this->bID) {
            $q = "select num, cParentID, cThis, orderBy, ctID, displayAliases, rss from nuevebit_btPageList where bID = '$bID'";
            $r = $db->query($q);
            if ($r) {
                $row = $r->fetchRow();
            }
        } else {
            $row['num'] = $this->num;
            $row['cParentID'] = $this->cParentID;
            $row['cThis'] = $this->cThis;
            $row['orderBy'] = $this->orderBy;
            $row['ctID'] = $this->ctID;
            $row['rss'] = $this->rss;
            $row['displayAliases'] = $this->displayAliases;
        }

        Loader::model("nuevebit_page_list", "c5-nuevebit");
        $pl = new NuevebitPageList();
        $pl->setNameSpace('b' . $this->bID);

        $cArray = array();

        switch ($row['orderBy']) {
            case 'display_asc':
                $pl->sortByDisplayOrder();
                break;
            case 'display_desc':
                $pl->sortByDisplayOrderDescending();
                break;
            case 'chrono_asc':
                $pl->sortByPublicDate();
                break;
            case 'alpha_asc':
                $pl->sortByName();
                break;
            case 'alpha_desc':
                $pl->sortByNameDescending();
                break;
            case 'most_viewed':
                $pl->sortByVisits();
                break;
            case 'relevance':
                $pl->sortByRelevance();
                break;
            case 'random':
                $pl->sortByRand();
                break;
            default:
                $pl->sortByPublicDateDescending();
                break;
        }

        $num = (int) $row['num'];

        if ($num > 0) {
            $pl->setItemsPerPage($num);
        }

        $c = Page::getCurrentPage();
        if (is_object($c)) {
            $this->cID = $c->getCollectionID();
        }

        Loader::model('attribute/categories/collection');
        if ($this->displayFeaturedOnly == 1) {
            $cak = CollectionAttributeKey::getByHandle('is_featured');
            if (is_object($cak)) {
                $pl->filterByIsFeatured(1);
            }
        }
        if (!$row['displayAliases']) {
            $pl->filterByIsAlias(0);
        }
        $pl->filter('cvName', '', '!=');

        if ($row['ctID']) {
            $pl->filterByCollectionTypeID($row['ctID']);
        }

        $columns = $db->MetaColumns(CollectionAttributeKey::getIndexedSearchTable());
        if (isset($columns['AK_EXCLUDE_PAGE_LIST'])) {
            $pl->filter(false, '(ak_exclude_page_list = 0 or ak_exclude_page_list is null)');
        }

        if (intval($row['cParentID']) != 0) {
            $cParentID = ($row['cThis']) ? $this->cID : $row['cParentID'];
            if ($this->includeAllDescendents) {
                $pl->filterByPath(Page::getByID($cParentID)->getCollectionPath());
            } else {
                $pl->filterByParentID($cParentID);
            }
        }

        if (count($this->excludes) > 0) {
            foreach ($this->excludes as $pageId) {
                $pl->filter("p1.cID", $pageId, "!=");
            }
        }

        // filter by stored nuevebit_PageListFilters
        $this->applyFilters($pl);

        // filter by user attributes
        $this->applyUserAttributes($pl);

        if ($num > 0) {
            $pages = $pl->getPage();
        } else {
            $pages = $pl->get();
        }
        $this->set('pl', $pl);
        return $pages;
    }

    private function sortByMostViewed($pl) {
    }

    private function getAllowedAttributes() {
        $db = Loader::db();
        $sql = "select a.akHandle from nuevebit_btPageListAttribute a where a.bID = ?";

        return $db->GetCol($sql, array($this->bID));
    }

    private function getFilters() {
        if ($this->bID) {
            $db = Loader::db();
            $sql = "select f.* from nuevebit_btPageListFilter f where f.bID = ?";

            return $db->GetAll($sql, array($this->bID));
        } else {
            return $this->filters;
        }
    }

    private function applyUserAttributes($pageList) {
        Loader::model("attribute/type");
        $allowedAttributes = $this->getAllowedAttributes();
//        Log::addEntry("bID: " . $this->bID);

        foreach ($allowedAttributes as $attribute) {
//            Log::addEntry("trying: " . $attribute);
            if (isset($_GET[$attribute]) && $_GET[$attribute]) {
//                Log::addEntry("allowed and found");
                $value = filter_var($_GET[$attribute], FILTER_SANITIZE_STRING);
                $key = CollectionAttributeKey::getByHandle($attribute);
                $type = $key->getAttributeType();

                if ($type->getAttributeTypeHandle() == "select") {
                    $pageList->filterByAttribute($attribute, "%" . $value . "%", "like");
                } else {
                    $pageList->filterByAttribute($attribute, $value);
                }
            }
        }
    }

    private function applyFilters($pageList) {
        foreach ($this->getFilters() as $filter) {
            if ($filter["type"] == "attribute") {
                $pageList->filterByAttribute(
                        $filter["col"], $filter["value"], $filter["comp"]);
            } else if ($filter["type"] == "date") {
                $pageList->filterByPublicDate($filter["value"], $filter["comp"]);
            }
        }
    }

    public function view() {
        $cArray = $this->getPages();
        $nh = Loader::helper('navigation');
        $this->set('nh', $nh);
        $this->set('cArray', $cArray); //Legacy (pre-5.4.2)
        $this->set('pages', $cArray); //More descriptive variable name (introduced in 5.4.2)
        //RSS...
        $showRss = false;
        $rssIconSrc = '';
        $rssInvisibleLink = '';
        if ($this->rss) {
            $showRss = true;
            $rssIconSrc = Loader::helper('concrete/urls')->getBlockTypeAssetsURL(BlockType::getByID($this->getBlockObject()->getBlockTypeID()), 'rss.png');
            //DEV NOTE: Ideally we'd set rssUrl here, but we can't because for some reason calling $this->getBlockObject() here doesn't load all info properly, and then the call to $this->getRssUrl() fails when it tries to get the area handle of the block.
        }
        $this->set('showRss', $showRss);
        $this->set('rssIconSrc', $rssIconSrc);

        //Pagination...
        $showPagination = false;
        $paginator = null;
        $pl = $this->get('pl'); //Terrible horrible hacky way to get the $pl object set in $this->getPages() -- we need to do it this way for backwards-compatibility reasons
        if ($this->paginate && $this->num > 0 && is_object($pl)) {
            $description = $pl->getSummary();
            if ($description->pages > 1) {
                $showPagination = true;
                $paginator = $pl->getPagination();
            }
        }
        $this->set('showPagination', $showPagination);
        $this->set('paginator', $paginator);

        $this->set("attributes", $this->getAllowedAttributes());
    }

    // this doesn't work yet
    /*
      public function on_page_view() {
      if ($this->rss) {
      $b = $this->getBlockObject();
      $this->addHeaderItem('<link href="' . $this->getRssUrl($b) . '"  rel="alternate" type="application/rss+xml" title="' . $this->rssTitle . '" />');
      }
      }
     */

    public function add() {
        Loader::model("collection_types");
        $c = Page::getCurrentPage();
        $uh = Loader::helper('concrete/urls');
        //	echo $rssUrl;
        $this->set('c', $c);
        $this->set('uh', $uh);
        $this->set('bt', BlockType::getByHandle('searchable_page_list'));
        $this->set('displayAliases', true);
        $this->set("attributes", array());
    }

    public function edit() {
        $b = $this->getBlockObject();
        $bCID = $b->getBlockCollectionID();
        $bID = $b->getBlockID();
        $this->set('bID', $bID);
        $c = Page::getCurrentPage();
        if ($c->getCollectionID() != $this->cParentID && (!$this->cThis) && ($this->cParentID != 0)) {
            $isOtherPage = true;
            $this->set('isOtherPage', true);
        }
        $uh = Loader::helper('concrete/urls');
        $this->set('uh', $uh);
        $this->set('bt', BlockType::getByHandle('searchable_page_list'));

        $this->set("attributes", $this->getAllowedAttributes());
    }

    function save($args) {
        // If we've gotten to the process() function for this class, we assume that we're in
        // the clear, as far as permissions are concerned (since we check permissions at several
        // points within the dispatcher)
        $db = Loader::db();

        $bID = $this->bID;
        $c = $this->getCollectionObject();
        if (is_object($c)) {
            $this->cID = $c->getCollectionID();
        }

        $args['num'] = ($args['num'] > 0) ? $args['num'] : 0;
        $args['cThis'] = ($args['cParentID'] == $this->cID) ? '1' : '0';
        $args['cParentID'] = ($args['cParentID'] == 'OTHER') ? $args['cParentIDValue'] : $args['cParentID'];
        if (!$args['cParentID']) {
            $args['cParentID'] = 0;
        }
        $args['includeAllDescendents'] = ($args['includeAllDescendents']) ? '1' : '0';
        $args['truncateSummaries'] = ($args['truncateSummaries']) ? '1' : '0';
        $args['displayFeaturedOnly'] = ($args['displayFeaturedOnly']) ? '1' : '0';
        $args['displayAliases'] = ($args['displayAliases']) ? '1' : '0';
        $args['truncateChars'] = intval($args['truncateChars']);
        $args['paginate'] = intval($args['paginate']);
        $args['rss'] = intval($args['rss']);
        $args['ctID'] = intval($args['ctID']);

        if (isset($args["attributes"])) {
            foreach ($args["attributes"] as $attribute) {
                $sql = "insert into nuevebit_btPageListAttribute (bID, akHandle) values (?, ?)";
                $db->Execute($sql, array($this->bID, $attribute));
            }
        }

        parent::save($args);
    }

    public function getRssUrl($b, $tool = 'rss') {
        $uh = Loader::helper('concrete/urls');
        if (!$b)
            return '';
        $btID = $b->getBlockTypeID();
        $bt = BlockType::getByID($btID);
        $c = $b->getBlockCollectionObject();
        $a = $b->getBlockAreaObject();
        $rssUrl = $uh->getBlockTypeToolsURL($bt) . "/" . $tool . "?bID=" . $b->getBlockID() . "&amp;cID=" . $c->getCollectionID() . "&amp;arHandle=" . $a->getAreaHandle();
        return $rssUrl;
    }

}

?>