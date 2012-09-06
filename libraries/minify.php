<?php

// To make this independent of any configuration, we don't load the lessphp
// library here!
class NB_LessSource extends Minify_Source {

    private static $compiler = null;

    private $lessFile;
    
    public function __construct($lessFile) {
        $this->lessFile = $lessFile;

        $spec = array(
            "id" => $lessFile,
            // this is needed to rewrite uris
            "minifyOptions" => array("currentDir" => dirname($lessFile)), 
            "getContentFunc" => array($this, 'parse'),
            "contentType" => Minify::TYPE_CSS,
            "lastModified" => filemtime($lessFile)
        );

//        echo "file: " . $lessFile;

        parent::__construct($spec);
    }

    public function parse() {
        if (self::$compiler == null) {
            self::$compiler = new lessc();
        }

        $content = self::$compiler->compileFile($this->lessFile);
        return $content;
    }
}

?>
