<?php
//error_reporting(-1);
//ini_set('display_errors', '1');

$_server  = $_SERVER;
$_request = $_REQUEST;
$pathes  = explode( '/app/terrific/public', dirname( str_replace( '\\', '/', __FILE__ ) ) );
$uri     = 'http' . ( empty( $_server['HTTPS'] ) ? '' : 's' ) . '://' . $_server['SERVER_NAME'] . ( $_server['SERVER_PORT'] != '80' ? ':' . $_server['SERVER_PORT'] : '' ) . $_server['REQUEST_URI'];
$baseuri = substr( $uri, 0, strrpos( $uri, '/terrific/', - 1 ) );

define( 'BASE', $pathes[0] . '/' );
define( 'BASEURL', $baseuri . '/' );
define( 'TERRIFICURL', $baseuri . '/terrific/' );

$config = json_decode( file_get_contents( BASE . 'config.json' ) );

include_once( '../../../project/index.project.php' );

$parts     = isset( $_GET['uriparts'] ) ? explode( '/', $_GET['uriparts'] ) : '';
$nrOfParts = count( $parts );

if ( $nrOfParts > 1 && $parts[0] === 'create' && property_exists( $config->micro->components, $parts[1] ) ) {
	include_once( '../library/Component.php' );

	$componentConfig            = $config->micro->components->$parts[1];
	$componentConfig->component = $parts[1];
	$component                  = isset( $_request['component'] ) ? $_request['component'] : null;
	$skin                       = isset( $_request['skin'] ) ? $_request['skin'] : null;
	$username                   = isset( $_request['user'] ) ? $_request['user'] : null;
	$useremail                  = isset( $_request['email'] ) ? $_request['email'] : null;

	$page = new Component( $componentConfig, $component, $skin, $username, $useremail );
}
else {
	// index page (overview)
	include_once( '../library/Index.php' );
	$page = new Index();
}
