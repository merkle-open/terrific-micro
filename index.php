<?php

define('BASE', dirname(__FILE__) . '/');

global $nocache;
$nocache = false;

function partial($file, $data = array()) {
    require BASE . '/views/partials/' . $file . '.html';
}

/**
 * Output module markup.
 */
function module($name, $template = null, $skin = null, $attr = array()) {
    $flat = strtolower($name);
    $dashed = strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), array('\\1-\\2', '\\1-\\2'), $name));
    $template = $template == null ? '' : '-' . strtolower($template);
    $skin = $skin == null ? '' : ' skin-' . $dashed . '-' . $skin;
    $attributes = ' ';
    $additionalClasses = '';
    foreach ($attr as $key => $value) {
        if ($key === 'class' && $value !== '') {
            $additionalClasses .= ' ' . $value;
        }
        else {
            $attributes .= $key . '="' . $value . '" ';
        }
    }
    echo "<div class=\"mod mod-" . $dashed . $skin . $additionalClasses . "\"" . chop($attributes) . ">" . "\n";
    require dirname(__FILE__) . '/modules/' . $name . '/' . $flat . $template . '.html';
    echo "\n</div>";
}

/**
 * Compile a CSS/LESS/SCSS file.
 */
function compile($filename, $extension, $base = false) {
    global $nocache;
    switch ($extension) {
        case 'less':
            $modified = filemtime($filename);
            $cachefile = sys_get_temp_dir() . '/terrific-' . md5(BASE.$filename) . '.css';
            if ($nocache || !is_file($cachefile) || (filemtime($cachefile) != $modified)) {
                require_once BASE . 'library/lessphp/lessc.inc.php';
                $less = new lessc;
                $content = $less->compileFile($filename);
                file_put_contents($cachefile, $content);
                touch($cachefile, $modified);
                if ($base) {
                    $nocache = true;
                }
            } else {
                $content = file_get_contents($cachefile);
            }
            break;

        case 'scss':
            $modified = filemtime($filename);
            $cachefile = sys_get_temp_dir() . '/terrific-' . md5(BASE.$filename) . '.css';
            if ($nocache || !is_file($cachefile) || (filemtime($cachefile) != $modified)) {
                require_once BASE . 'library/phpsass/SassParser.php';
                $sass = new SassParser(array('style'=>'nested', 'cache' => false));
                $content = $sass->toCss($filename);
                file_put_contents($cachefile, $content);
                touch($cachefile, $modified);
                if ($base) {
                    $nocache = true;
                }
            } else {
                $content = file_get_contents($cachefile);
            }
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
                $output .= compile($entry, $format, true);
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
            foreach (glob($dir . '/' . $extension . '/*-*.' . $format) as $entry) {
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

if (preg_match("/\/app.css/",$_SERVER['REQUEST_URI'])) { dump('css', 'text/css'); exit(); }
if (preg_match("/\/app.js/",$_SERVER['REQUEST_URI'])) { dump('js', 'text/javascript'); exit(); }

$url = str_replace('?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
$url = preg_replace('/\.[^.\s]{2,4}$/', '', $url);	// remove file extension
$route = explode('/', $url);
$action = end($route);
if ($action == "") { $action = 'index'; }
$view = dirname(__FILE__) . '/views/' . $action . '.html';

if (is_file($view)) {
    require $view;
} else {
    header('HTTP/1.0 404 Not Found');
    exit();
}