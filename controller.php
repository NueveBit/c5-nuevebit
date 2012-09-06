<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

class NuevebitPackage extends Package {

	protected $pkgHandle = 'nuevebit';
	protected $appVersionRequired = '5.5.0';
	protected $pkgVersion = '0.3.7'; 
	
	public function getPackageName() {
		return t("9Bit"); 
	}	
	
	public function getPackageDescription() {
		return t("9Bit Package - Contains blocks, pagetypes, themes");
	}

    public function upgrade() {
        parent::upgrade();


//		$db = Loader::db();
#		$db->Execute('TRUNCATE TABLE items_Items');

#        $this->installItems();
        
        /**
         * ACTUALIZAR Artículos de ok digital (precios)

		$db = Loader::db();
		$db->Execute('TRUNCATE TABLE okdigital_Print');
		$db->Execute('TRUNCATE TABLE okdigital_Item');

        Loader::library("csv");
        $root = $this->getPackagePath() . "/install/";
        
        CSVImporter::toDatabase($root . "prints.csv", "okdigital_Print");
        CSVImporter::toDatabase($root . "items.csv", "okdigital_Item");

         */
        
        // install 'nuevebit page list' block
//		BlockType::installBlockTypeFromPackage('nuevebit_page_list', $this);
//        BlockType::getByHandle('nuevebit_gallery')->controller->uninstall();
//		BlockType::installBlockTypeFromPackage('nuevebit_gallery', $this);
    }
	
	public function install() {
		$pkg = parent::install();

//	    $sp = SinglePage::add('/dashboard/products', $pkg);
//		$sp->update(array('cName'=>t("Products"), 'cDescription'=>t("Manage products.")));

//	    $sp = SinglePage::add('/dashboard/items/import', $pkg);
//		$sp->update(array('cName'=>t("Import"), 'cDescription'=>t("Import a list of items into the db.")));
		
//	    $sp = SinglePage::add('/dashboard/items/add', $pkg);
//		$sp->update(array('cName'=>t("Add"), 'cDescription'=>t("Add a new item.")));

//	    $sp = SinglePage::add('/dashboard/products/list', $pkg);
//		$sp->update(array('cName'=>t("List"), 'cDescription'=>t("List all available products.")));

        // install 'one page' block
		BlockType::installBlockTypeFromPackage('nuevebit_page_list', $pkg);
		BlockType::installBlockTypeFromPackage('nuevebit_gallery', $pkg);

		// install 'one page' page type
//        $data = array("ctHandle" => "one_page", "ctName" => "One Page");
//        $pageType = CollectionType::add($data, $pkg);
		// install theme
//		PageTheme::add('okdigital', $pkg);
	}

	public function uninstall() {
		parent::uninstall();
	}
}
