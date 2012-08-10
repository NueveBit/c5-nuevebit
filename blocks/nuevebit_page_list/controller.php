<?php 

	defined('C5_EXECUTE') or die("Access Denied.");
	class NuevebitPageListBlockController extends PageListBlockController {

		protected $btTable = 'btNuevebitPageList';
		protected $btInterfaceWidth = "500";
		protected $btInterfaceHeight = "350";
		protected $btExportPageColumns = array('cParentID');
		protected $btExportPageTypeColumns = array('ctID');
		protected $btCacheBlockRecord = true;

        // cache already retrieved pages for the same session
        private $cachedPages = null;
		
		/** 
		 * Used for localization. If we want to localize the name/description we have to include this
		 */
		public function getBlockTypeDescription() {
			return t("List pages based on type, area. Includes template for
                wrapping entire pages.");
		}
		
		public function getBlockTypeName() {
			return t("Nuevebit Page List");
		}

        /**
         * We need to call each pages header items on_page_view, or else
         * auto js/css won't be included.
         */
        public function on_page_view() {
            $pages = $this->getPages();
            
            foreach ($pages as $page) {
                $pageBlocks = $page->getBlocks();
                $pageBlocksGlobal = $page->getGlobalBlocks();
                $pageBlocks = array_merge($pageBlocks, $pageBlocksGlobal);

                foreach($pageBlocks as $b1) {
                    $btc = $b1->getInstance();
                    // now we inject any custom template CSS and JavaScript into the header
                    if('Controller' != get_class($btc)){
                        $btc->outputAutoHeaderItems();
                    }
                    
                }
                
                $btc->runTask('on_page_view', array($page));
            }
        }
		
		public function getPages($query = null) {
            if ($query == null && $this->cachedPages != null) {
                return $this->cachedPages;
            }

			Loader::model('page_list');
			$db = Loader::db();
			$bID = $this->bID;
			if ($this->bID) {
				$q = "select num, cParentID, cThis, orderBy, ctID, displayAliases, rss from btNuevebitPageList where bID = '$bID'";
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
			

			$pl = new PageList();
			$pl->setNameSpace('b' . $this->bID);
			
			$cArray = array();

			switch($row['orderBy']) {
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
			
			if ( intval($row['cParentID']) != 0) {
				$cParentID = ($row['cThis']) ? $this->cID : $row['cParentID'];
				if ($this->includeAllDescendents) {
					$pl->filterByPath(Page::getByID($cParentID)->getCollectionPath());
				} else {
					$pl->filterByParentID($cParentID);
				}
			}

			if ($num > 0) {
				$pages = $pl->getPage();
			} else {
				$pages = $pl->get();
			}
			$this->set('pl', $pl);

            if ($query == null) {
                $this->cachedPages = $pages;
            }
            
			return $pages;
		}
		
		public function add() {
            parent::add();
            
			$this->set('bt', BlockType::getByHandle('nuevebit_page_list'));
		}
	
		public function edit() {
            parent::edit();

			$this->set('bt', BlockType::getByHandle('nuevebit_page_list'));
		}
		
	}

?>