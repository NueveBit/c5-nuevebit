<?php

if ((isset($_GET["f"]) && $_GET["f"]) &&
        (isset($_GET["t"]) && $_GET["t"])) {

    Loader::library("Minify/Minify", "nuevebit");
    $libPath = Package::getByHandle("nuevebit")->getPackagePath() . "/libraries/Minify";

    // we need to add this directory to the include path, so we don't have to change
    // the references in code
    ini_set("include_path", get_include_path() . PATH_SEPARATOR . $libPath);

    $files = explode(",", $_GET["f"]);
    $sources = array();
    $type = $_GET["t"];
    $resolvePath = ($type == "css") ? "resolveCss" : "resolveJs";
    
    foreach ($files as $file) {
        // security measures...
        while (strpos($file, "../") !== false) {
            $file = str_replace("../", "", $file);
        }
        
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

    $toolsUrl = Loader::helper("concrete/urls")->getToolsUrl("minify", "nuevebit");
    list($prefix) = explode("/index.php", $toolsUrl, 2);
    
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
    
    $currentThemeDirectory = PageTheme::getSiteTheme()->getThemeDirectory();
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
