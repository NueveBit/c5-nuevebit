<?php
/*
 * Código adaptado de:
 * http://www.lukaswhite.com/blog/post/2011/concrete5-development-tutorial-creating-quote-block
 */

defined('C5_EXECUTE') or die(_("Access Denied."));

//Loader::block('library_file');

class QuotesBlockController extends BlockController {

    protected $btTable = 'nuevebit_btQuotes';
    protected $btInterfaceWidth = "600";
    protected $btInterfaceHeight = "400";
    public $content = "";
    public $source = "";

    public function getBlockTypeDescription() {
        return t("Add simple quotes to your website.");
    }

    public function getBlockTypeName() {
        return t("Quote");
    }

    public function view() {
        $this->set('content', $this->content);
        $this->set('source', $this->source);
    }

    // TODO: es realmente necesario sanitizar las variables?
    public function save($data) {
        $args['content'] = isset($data['content']) ? 
                filter_var($data['content'], FILTER_SANITIZE_STRING) : '';
        
        $args['source'] = isset($data['source']) ? 
                filter_var($data['source'], FILTER_SANITIZE_STRING) : '';
        
        parent::save($args);
    }

}
