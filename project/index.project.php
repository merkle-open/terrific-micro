<?php

// -------------------------------------------------------------------------------------------
// Your place for all project relevant stuff (included by main controller)
// -------------------------------------------------------------------------------------------

//error_reporting(-1);
//ini_set('display_errors', '1');

//$nocache                 = true;             // true -> disables .less/.scss caching

// ------------------------------
// global variables
// ------------------------------
//define('OFFLINE', isset($_REQUEST['online']) ? false : true);

// ------------------------------
// simple functions
// ------------------------------

// returns the project title
//if ( ! function_exists('get_my_app_title')) {
//	function get_my_app_title() {
//		return 'Terrific Micro - My App';
//	}
//}

// returns a random string of specific length
//if ( ! function_exists('get_random_string')) {
//	function get_random_string($length = 10) {
//		return substr(sha1(rand()), 0, $length);
//	}
//}

// ------------------------------
// overwrite core functions
// ------------------------------

//function get_less_parser() {
//	require_once BASE . 'app/library/lessphp/lessc.inc.php';
//	$less = new lessc;
//	//$less->setImportDir( array( '' ) ); // default
//	$less->addImportDir( 'assets/bootstrap' );
//	return $less;
//}

//function get_asset_banner( $filename = '', $filetype = '', $minified = false, $duration ) {
//	$ret = '';
//	if ( !$minified && isset( $duration ) ) {
//		$time_taken = round( $duration * 1000 );
//		$ret .= '/* time taken: ' . $time_taken . ' ms';
//		$ret .= ' */' . PHP_EOL;
//	}
//	else {
//		$ret .= '/* ' . $filename . ' */' . PHP_EOL;
//	}
//	return $ret;
//}

// ------------------------------
// extensions
// ------------------------------
//you'll find some extensions on GitHub: https://github.com/namics/terrific-micro-extensions
