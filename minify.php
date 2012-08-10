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
    define('DIR_FILES_THEMES', DIR_BASE . '/themes');
    define('DIR_FILES_THEMES_CORE', DIR_BASE_CORE . '/themes');
    define('DIR_FILES_THEMES_CORE_ADMIN', DIR_BASE_CORE . '/themes/core');

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

        $source = $resolvePath($name, $pkg);
        if ($source) {
            $sources[] = $source;
        }
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
    if (substr($file, 0, 1) == '/') {

        // let's try to guess the path
        if ($pkgHandle == null && strpos($file, "packages/") !== false) {
            $path = substr($file, strpos($file, "packages/") + 9);
            
            if (file_exists(DIR_BASE . '/' . DIRNAME_PACKAGES . '/' . $path)) {
                $path = DIR_BASE . '/' . DIRNAME_PACKAGES . '/' . $path;
            } else {
                $path = DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $path;
            }
            
            return $path;
        }
    }

    if (substr($file, 0, 4) == 'http' || strpos($file, "index.php") > -1) {
        return null;
    }
    
    if (substr($file, 0, 1) == '/') {

        // let's try to guess the path
        if (strpos($file, "packages/") !== false) {
            $path = substr($file, strpos($file, "packages/"));
            $path = DIRNAME_PACKAGES . $path;
//            /~emerino/shintai.com.mx/packages/nuevebit/blocks/slideshow/templates/galleria/js/galleria.js&t=js&v=shintai
            return $path;
        }
    }

    if (substr($file, 0, 4) == 'http' || strpos($file, "index.php") > -1) {
        return null;
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
    if (substr($file, 0, 1) == '/') {

        // let's try to guess the path
        if ($pkgHandle == null && strpos($file, "packages/") !== false) {
            $path = substr($file, strpos($file, "packages/") + 9);
            
            if (file_exists(DIR_BASE . '/' . DIRNAME_PACKAGES . '/' . $path)) {
                $path = DIR_BASE . '/' . DIRNAME_PACKAGES . '/' . $path;
            } else {
                $path = DIR_BASE_CORE . '/' . DIRNAME_PACKAGES . '/' . $path;
            }
            
            return $path;
        }
    }

    if (substr($file, 0, 4) == 'http' || strpos($file, "index.php") > -1) {
        return null;
    }
    
    $path = '';
    // if the first character is a / then that means we just go right through, it's a direct path
    if (substr($file, 0, 1) == '/' || substr($file, 0, 4) == 'http' || strpos($file, "index.php") > -1) {
        return null;
    }
    
    $currentTheme = $_GET["v"];
    if (isset($currentTheme) && $currentTheme != "") {
        $currentThemeDirectory = DIR_FILES_THEMES . '/' . $currentTheme;
        
        if ($currentThemeDirectory != '' && file_exists($currentThemeDirectory . '/' . $file)) {
            $path = $currentThemeDirectory . '/' . $file;
        }
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
