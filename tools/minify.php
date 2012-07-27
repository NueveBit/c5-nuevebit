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
        list($name, $pkg) = explode(";", $file);

        $sources[] = $resolvePath($name, $pkg);
    }

    $options = array(
        'files' => (array)$sources,
        'encodeMethod' => ''
    );

    $toolsUrl = Loader::helper("concrete/urls")->getToolsUrl("minify", "nuevebit");
    list($prefix) = explode("/index.php", $toolsUrl, 2);
    
    if ($prefix) {
        $options["minifierOptions"][Minify::TYPE_CSS] = array();
        $options["minifierOptions"][Minify::TYPE_CSS]["symlinks"] = array(
            "//" . ltrim($prefix, "/") => DIR_BASE
        );
    }

    Minify::setDocRoot(DIR_BASE);
    Minify::setCache(DIR_BASE . "/files/cache");
    Minify::serve("Files", $options);
}

function resolveJs($file, $pkgHandle) {
    if (substr($file, 0, 1) == '/' || substr($file, 0, 4) == 'http' || strpos($file, DISPATCHER_FILENAME) > -1) {
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
        
    return $path;
}

function resolveCss($file, $pkgHandle) {
    // if the first character is a / then that means we just go right through, it's a direct path
    if (substr($file, 0, 1) == '/' || substr($file, 0, 4) == 'http' || strpos($file, DISPATCHER_FILENAME) > -1) {
        $path = $file;
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
        
    return $path;
}
?>
