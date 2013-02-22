<?php

define('BASE', dirname(__FILE__) . '/');

/**
 * Output module markup.
 */
function module($name, $template = null, $skin = null, $attr = array()) {
    $flat = strtolower($name);
    $template = $template == null ? '' : '.' . $template;
    $skin = $skin == null ? '' : ' skin-' . $flat . '-' . $skin;
    $attributes = " ";
    foreach ($attr as $key => $value) {
        $attributes .= $key . '="' . $value . '" ';
    }
    echo "<div class=\"mod mod-" . $flat . $skin . "\"" . chop($attributes) . ">" . "\n";
    require dirname(__FILE__) . '/modules/' . $name . '/' . $flat . $template . '.html';
    echo "\n</div>";
}

/**
 * Compile a CSS/LESS/SCSS file.
 */
function compile($filename, $extension) {
    switch ($extension) {
        case 'less':
            require_once BASE . 'library/lessphp/lessc.inc.php';
            $less = new lessc;
            $content = $less->compileFile($filename);
            break;

        case 'scss':
            require_once BASE . 'library/phpsass/SassParser.php';
            $sass = new SassParser(array('style'=>'nested', 'cache' => false));
            $content = $sass->toCss($filename);
            break;

        default:
            $content = file_get_contents($filename);
            break;
    }
    return $content;
}

/**
 * Dump CSS/JS.
 */
function dump($extension, $mimetype) {
    $formats = array(
        'js' => array('js'),
        'css' => array('less', 'scss', 'css')
    );
    $files = array();
    $output = "";
    $assets = json_decode(file_get_contents(BASE . '/assets/assets.json'));
    foreach ($assets->$extension as $pattern) {
        foreach (glob(BASE . 'assets/' . $extension . '/' . $pattern) as $entry) {
            if (is_file($entry) && !array_key_exists($entry, $files)) {
                $format = substr(strrchr($entry, '.'), 1);
                $output .= compile($entry, $format);
                $files[$entry] = true;
            }
        }
    }
    foreach (glob(BASE . 'modules/*', GLOB_ONLYDIR) as $dir) {
        $module = basename($dir);
        foreach ($formats[$extension] as $format) {
            $entry = $dir . '/' . $extension . '/' . strtolower($module) . '.' . $format;
            if (is_file($entry) && !array_key_exists($entry, $files)) {
                $output .= compile($entry, $format);
                $files[$entry] = true;
            }
            foreach (glob($dir . '/' . $extension . '/*.*.' . $format) as $entry) {
                if (is_file($entry) && !array_key_exists($entry, $files)) {
                    $output .= compile($entry, $format);
                    $files[$entry] = true;
                }
            }
        }
    }

    if (isset($_REQUEST['min'])) {
        switch ($extension) {
            case 'css':
                require BASE . 'library/cssmin/cssmin.php';
                $output = CssMin::minify($output);
                break;
            case 'js':
                require BASE . 'library/jsmin/jsmin.php';
                $output = JsMin::minify($output);
                break;
        }
    }
    header("Content-Type: " . $mimetype);
    echo $output;
}

if (substr($_SERVER['REQUEST_URI'],0,8) == '/app.css') { dump('css', 'text/css'); exit(); }
if (substr($_SERVER['REQUEST_URI'],0,7) == '/app.js') { dump('js', 'text/javascript'); exit(); }

$route = str_replace('?' . $_SERVER['QUERY_STRING'], '', explode('/', $_SERVER['REQUEST_URI']));
if ($route[1] == "") { $route[1] = 'index'; }
$view = dirname(__FILE__) . '/views/' . $route[1] . '.html';

if (is_file($view)) {
    require $view;
} else {
    die('view does not exist');
}