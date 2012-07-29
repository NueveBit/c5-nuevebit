<?php

ini_set("error_reporting", 0);

if ((isset($_GET["f"]) && $_GET["f"]) &&
        (isset($_GET["t"]) && $_GET["t"])) {

	define('DIR_BASE', dirname($_SERVER['SCRIPT_FILENAME']));
	define('DIR_PACKAGES', DIR_BASE . '/packages');
    define('DIR_FILES_CACHE', DIR_BASE . '/files/cache');
    define('DIRNAME_JAVASCRIPT', 'js');
    define('DIRNAME_PACKAGES', 'packages');
	define('DIR_BASE_CORE', dirname(__FILE__) . '/concrete');
    define('DIRNAME_CSS', 'css');

    require_once(DIR_BASE . '/config/site.php');
    
	$pos = stripos($_SERVER['SCRIPT_NAME'], MINIFY_SCRIPT);
	if($pos > 0) { //we do this because in CLI circumstances (and some random ones) we would end up with index.ph instead of index.php
		$pos = $pos - 1;
	}
	$uri = substr($_SERVER['SCRIPT_NAME'], 0, $pos);
	define('DIR_REL', $uri);

    $path = DIR_PACKAGES . '/nuevebit';
    $libPath =  $path . "/libraries/Minify";
    
    ini_set("include_path", get_include_path() . PATH_SEPARATOR . $libPath);
    require_once($libPath . '/Minify.php');

    // we need to add this directory to the include path, so we don't have to change
    // the references in code

    $files = explode(",", $_GET["f"]);
    $sources = array();
    $type = $_GET["t"];
    $resolvePath = ($type == "css") ? "resolveCss" : "resolveJs";
    
    foreach ($files as $file) {
        list($name, $pkg) = explode(";", $file);

        $sources[] = $resolvePath($name, $pkg);
    }

    $options = array(
        'files' => (array)$sources,
        'encodeMethod' => ''
    );

    list($prefix) = explode('/' . MINIFY_SCRIPT, DIR_REL, 2);
    
    if ($prefix) {
        $options["minifierOptions"] = array(Minify::TYPE_CSS => array(
            "symlinks" => array(
                "//" . ltrim($prefix, "/") => DIR_BASE
            )
        ));
    }

    Minify::setDocRoot(DIR_BASE);

    if (defined('MINIFY_CACHE_DISABLE') && MINIFY_CACHE_DISABLE) {
        Minify::setCache(null);
        $options['lastModifiedTime'] = 0;
    } else {
        Minify::setCache(DIR_FILES_CACHE);
    }
    
    Minify::serve("Files", $options);
}

function resolveJs($file, $pkgHandle) {
    if (substr($file, 0, 1) == '/' || substr($file, 0, 4) == 'http' || strpos($file, "index.php") > -1) {
        $path = $file;
    }

    if (file_exists(DIR_BASE . '/' . DIRNAME_JAVASCRIPT . '/' . $file)) {
        $path = DIR_BASE . '/' . DIRNAME_JAVASCRIPT . '/' . $file;
    } else if ($pkgHandle != null) {
        if (file_exists(DIR_BASE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file)) {
            $path = DIR_BASE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file;
        } else if (file_exists(DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/' . $file)) {
            $path = DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JAVASCRIPT . '/'. $file;
        }
    }

    if ($path == '') {
        $path = DIR_BASE_CORE . '/' . DIRNAME_JAVASCRIPT . '/' . $file;
    }
        
    return $path;
}

function resolveCss($file, $pkgHandle) {
    $path = '';
    // if the first character is a / then that means we just go right through, it's a direct path
    if (substr($file, 0, 1) == '/' || substr($file, 0, 4) == 'http' || strpos($file, "index.php") > -1) {
        $path = $file;
    }
    
    $currentThemeDirectory = DIR_BASE . '/themes/shintai';
    // checking the theme directory for it. It's just in the root.
    if ($currentThemeDirectory != '' && file_exists($currentThemeDirectory . '/' . $file)) {
        $path = $currentThemeDirectory . '/' . $file;
    } else if (file_exists(DIR_BASE . '/' . DIRNAME_CSS . '/' . $file)) {
        $path = DIR_BASE . '/' . DIRNAME_CSS . '/' . $file;
    } else if ($pkgHandle != null) {
        if (file_exists(DIR_BASE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file)) {
            $path = DIR_BASE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file;
        } else if (file_exists(DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file)) {
            $path = DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_CSS . '/' . $file;
        }
    }
    
    if ($path == '') {
        $path = DIR_BASE_CORE . '/' . DIRNAME_CSS . '/' . $file;
    }
        
    return $path;
}
?>
