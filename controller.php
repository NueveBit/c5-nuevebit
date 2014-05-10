<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

class C5NuevebitPackage extends Package {

	protected $pkgHandle = 'nuevebit';
	protected $appVersionRequired = '5.5.0';
	protected $pkgVersion = '0.6'; 
	
	public function getPackageName() {
		return t("9Bit"); 
	}	
	
	public function getPackageDescription() {
		return t("9Bit Package - Contains blocks, pagetypes, themes, etc.");
	}

    public function upgrade() {
        parent::upgrade();

//        $args = array("akHandle" => "gallery_thumbnail", "akName" => "Gallery Thumbnail", "akIsSearchable" => "1");
//        $type = AttributeType::getByHandle("image_file");
//        CollectionAttributeKey::add($type, $args, $this);
        // install 'one page' block
        
//        Loader::library('content/importer');
//        $ci = new ContentImporter();
//        $ci->importContentFile($this->getPackagePath() . '/content.xml');

        
//		BlockType::installBlockTypeFromPackage('nuevebit_search', $this);

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
//        $quotesBlock = BlockType::getByHandle("quote", $this);
//        if ($quotesBlock) {
//            $quotesBlock->controller->uninstall();
//        }
        
//		BlockType::installBlockTypeFromPackage('quotes', $this);
    }
	
	public function install() {
		$pkg = parent::install();

        $args = array("akHandle" => "gallery_thumbnail", "akName" => "Gallery Thumbnail", "akIsSearchable" => "1");
        $type = AttributeType::getByHandle("image_file");
        CollectionAttributeKey::add($type, $args, $this);
        
        // install 'one page' block
		BlockType::installBlockTypeFromPackage('nuevebit_page_list', $pkg);
		BlockType::installBlockTypeFromPackage('nuevebit_gallery', $pkg);
//		BlockType::installBlockTypeFromPackage('gallery', $pkg);
		BlockType::installBlockTypeFromPackage('searchable_page_list', $pkg);
		BlockType::installBlockTypeFromPackage('quote', $pkg);
		BlockType::installBlockTypeFromPackage('nuevebit_search', $pkg);

        Loader::library('content/importer');
        $ci = new ContentImporter();
//        $ci->importContentFile($this->getPackagePath() . '/content.xml');

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
