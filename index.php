<?php

// -------------------------------------------------------------------------------------------
// Terrific Micro v1.0.0
// https://github.com/namics/terrific-micro/blob/master/README.md
//
// Do not change this file - use project/index.project.php for your customisations
// -------------------------------------------------------------------------------------------

define( 'BASE', dirname( __FILE__ ) . '/' );
$config   = json_decode( file_get_contents( BASE . 'config.json' ) );
$nocache  = false; // true -> disables .less/.scss caching
$cachedir = ( is_writeable( sys_get_temp_dir() ) ? sys_get_temp_dir() : BASE . 'app/cache' ); // use php temp or the local cache directory

include_once( BASE . 'project/index.project.php' ); // use this file for all your customisations

// ------------------
// Base Functionality
// ------------------

if ( !function_exists( 'partial' ) ) {
	/**
	 * Outputs a partial
	 *
	 * @param $file
	 * @param array $data
	 */
	function partial( $file, $data = array() ) {
		global $config;
		$partial_template = BASE . $config->micro->view_partials_directory . '/' . $file . '.' . $config->micro->view_file_extension;
		if ( file_exists( $partial_template ) ) {
			require $partial_template;
		}
		else {
			echo '<p>Could not find the partial file: <code>' . $file . '.' . $config->micro->view_file_extension . '</code></p>';
		}
	}
}

if ( !function_exists( 'component' ) ) {
	/**
	 * Outputs component markup.
	 *
	 * @param $name
	 * @param null $template
	 * @param array $skin
	 * @param array $attr
	 * @param null $tag
	 */
	function component( $name, $template = null, $skin = array(), $attr = array(), $tag = null ) {

		global $config;

		$name              = ucfirst( $name );
		$flat              = strtolower( $name );
		$template          = empty( $template ) || $template === true ? '' : '-' . strtolower( $template );
		$component_wrapper = !empty( $skin ) || $skin === '0' || $skin === null || $skin === false || !empty( $attr ) || !empty( $tag );

		$component_template = $name . '/' . $flat . $template . '.' . $config->micro->view_file_extension;
		$component_file     = false;

		foreach ( $config->micro->components as $key => $component ) {
			$directory = $component->path;
			$component_prefix = property_exists( $component, 'component_prefix') ? $component->component_prefix : '';
			if ( file_exists( BASE . $directory . '/' . $component_template ) ) {
				$component_file = BASE . $directory . '/' . $component_template;
				break 1;
			}
			else {
				foreach ( glob( BASE . $directory . '/*/' . $component_template ) as $component_template_file ) {
					if ( file_exists( $component_template_file ) ) {
						$component_file   = $component_template_file;
						break 2;
					}
				}
			}
		}

		echo PHP_EOL;
		if ( $component_file === false ) {
			echo '<p>Could not find the component template: <code>' . $flat . $template . '.' . $config->micro->view_file_extension . '</code></p>';
		}
		else {
			if ( $component_wrapper ) {
				$dashed = strtolower( preg_replace( array(
					'/([A-Z]+)([A-Z][a-z])/',
					'/([a-z\d])([A-Z])/'
				), array( '\\1-\\2', '\\1-\\2' ), $name ) );

				$skins = '';
				if ( !empty( $skin ) || $skin === '0' ) {
					$skin_prefix = property_exists( $component, 'skin_prefix') ? $component->skin_prefix : '';
					if ( is_array( $skin ) ) {
						foreach ( $skin as $key => $value ) {
							$skins .= ' ' . $skin_prefix . '-' . $dashed . '-' . $value;
						}
					}
					else {
						$skins = !is_string( $skin ) ? '' : ' ' . $skin_prefix . '-' . $dashed . '-' . $skin;
					}
				}

				$attributes         = ' ';
				$additional_classes = '';
				if ( !empty( $attr ) && is_array( $attr ) ) {
					foreach ( $attr as $key => $value ) {
						if ( $key === 'class' && !empty( $value ) ) {
							$additional_classes .= ' ' . $value;
						}
						else {
							$attributes .= $key . '="' . $value . '" ';
						}
					}
				}
				if ( empty( $tag ) ) {
					$tag = 'div';
				}

				echo '<' . $tag . ' class="' . $component_prefix . ' ' . $component_prefix . '-' . $dashed . $skins . $additional_classes . '"' . chop( $attributes ) . '>' . PHP_EOL;
			}
			require $component_file;
			if ( $component_wrapper ) {
				echo '</' . $tag . '>';
			}
		}
		echo PHP_EOL;
	}
}

if ( !function_exists( 'module' ) ) {
	/**
	 * Wrapper function for component().
	 *
	 * This function has been deprecated in favour of component() and will be removed in a future release.
	 *
	 * @param name
	 * @param null $template
	 * @param array $skin
	 * @param array $attr
	 * @param null $tag
	 *
	 * @deprecated
	 */
	function module( $name, $template = null, $skin = array(), $attr = array(), $tag = null ) {
		component( $name, $template, $skin, $attr, $tag );
	}
}

// ------------------
// Helpers
// ------------------

if ( !function_exists( 'compile' ) ) {
	/**
	 * Compiles a CSS/LESS/SCSS/JS file.
	 *
	 * @param $filename
	 * @param $extension
	 * @param array $dependencies
	 *
	 * @return string
	 */
	function compile( $filename, $extension, $dependencies = array() ) {

		global $nocache, $cachedir;

		switch ( $extension ) {
			case 'less':
				$modified = filemtime( $filename );
				foreach ( $dependencies as $dep ) {
					if ( substr( strrchr( $dep, '.' ), 1 ) == $extension && filemtime( $dep ) > $modified ) {
						$modified = filemtime( $dep );
					}
				}

				$cachefile = $cachedir . '/terrific-' . md5( BASE . implode( '', $dependencies ) . $filename ) . '.css';
				if ( $nocache || !is_file( $cachefile ) || ( filemtime( $cachefile ) != $modified ) ) {

					$filecontents = '';
					foreach ( $dependencies as $dep ) {
						if ( substr( strrchr( $dep, '.' ), 1 ) == $extension ) {
							$filecontents .= file_get_contents( $dep );
						}
					}
					$filecontents .= file_get_contents( $filename );

					$less = get_less_parser();
					try {
						$content = $less->compile( $filecontents );
						file_put_contents( $cachefile, $content );
						touch( $cachefile, $modified );
					} catch ( Exception $e ) {
						$content = get_compile_error_css( $e, $filename, 'lessphp' );
					}
				}
				else {
					$content = file_get_contents( $cachefile );
				}
				break;

			case 'scss':
				$modified = filemtime( $filename );
				foreach ( $dependencies as $dep ) {
					if ( substr( strrchr( $dep, '.' ), 1 ) == $extension && filemtime( $dep ) > $modified ) {
						$modified = filemtime( $dep );
					}
				}

				$cachefile = $cachedir . '/terrific-' . md5( BASE . implode( '', $dependencies ) . $filename ) . '.css';
				if ( $nocache || !is_file( $cachefile ) || ( filemtime( $cachefile ) != $modified ) ) {

					$filecontents = '';
					foreach ( $dependencies as $dep ) {
						if ( substr( strrchr( $dep, '.' ), 1 ) == $extension ) {
							$filecontents .= file_get_contents( $dep );
						}
					}
					$filecontents .= file_get_contents( $filename );

					$scss = get_scss_parser();
					try {
						$content = $scss->compile( $filecontents );
						file_put_contents( $cachefile, $content );
						touch( $cachefile, $modified );
					} catch ( Exception $e ) {
						$content = get_compile_error_css( $e, $filename, 'scssphp' );
					}
				}
				else {
					$content = file_get_contents( $cachefile );
				}
				break;

			default:
				$content = file_get_contents( $filename );
				break;
		}

		return $content . PHP_EOL;
	}
}

if ( !function_exists( 'dump' ) ) {
	/**
	 * Dumps a CSS/JS file
	 *
	 * @param $name
	 * @param $mimetype
	 */
	function dump( $name, $mimetype ) {

		global $config;
		$starttime = microtime( true );

		$excludes     = array();
		$dependencies = array();
		$patterns     = array();

		$filetype = substr( strrchr( $name, '.' ), 1 );
		$output   = '';

		$minify          = isset( $_REQUEST['min'] );
		$debugjavascript = $filetype === 'js' && isset( $_REQUEST['debug'] );
		if ( $debugjavascript ) {
			$output .= '// load js files in a synchronous way' . PHP_EOL;
		}

		// collect excluded pattern & (less/scss) dependencies & patterns
		foreach ( $config->assets->$name as $pattern ) {
			$firstchar = substr( $pattern, 0, 1 );
			if ( $firstchar === '!' ) {
				$excludes[] = substr( $pattern, 1 );
			}
			else if ( $firstchar === '+' ) {
				$dependencies[] = substr( $pattern, 1 );
			}
			else {
				$patterns[] = $pattern;
			}
		}

		$dependencies = get_files( $dependencies );
		$excludes     = array_merge( $dependencies, $excludes );
		$files        = get_files( $patterns, $excludes );

		foreach ( $files as $entry ) {
			if ( !$debugjavascript ) {
				$format = substr( strrchr( $entry, '.' ), 1 );
				$output .= compile( BASE . $entry, $format, $dependencies );
			}
			else {
				$output .= "document.write('<script type=\"text/javascript\" src=\"$entry\"><\/script>');" . PHP_EOL;
			}
		}

		if ( $minify ) {
			switch ( $filetype ) {
				case 'css':
					require BASE . 'app/library/cssmin/cssmin.php';
					$output = CssMin::minify( $output );
					break;
				case 'js':
					require BASE . 'app/library/jshrink/Minifier.php';
					$output = \JShrink\Minifier::minify( $output );
					break;
			}
		}

		$time_taken = microtime( true ) - $starttime;
		$output     = get_asset_banner( $name, $filetype, $minify, $time_taken ) . $output;

		header( 'Content-Type: ' . $mimetype );
		echo $output;
	}
}

if ( !function_exists( 'get_files' ) ) {
	/**
	 * Gets an array of files with given glob patterns
	 *
	 * @param $patterns
	 * @param array $excludes
	 *
	 * @return array
	 */
	function get_files( $patterns, $excludes = array() ) {
		$files = array();
		foreach ( $patterns as $pattern ) {
			foreach ( glob( BASE . $pattern ) as $uri ) {
				$file = str_replace( BASE, '', $uri );
				if ( is_file( $uri ) && !is_excluded_file( $file, $excludes ) ) {
					$files[] = $file;
				}
			}
		}

		return array_unique( $files );
	}
}

if ( !function_exists( 'is_excluded_file' ) ) {
	/**
	 * Checks if a file matches an exclude pattern
	 *
	 * @param $filename
	 * @param array $excludes
	 *
	 * @return bool
	 */
	function is_excluded_file( $filename, $excludes = array() ) {
		foreach ( $excludes as $exclude ) {
			if ( fnmatch( $exclude, $filename ) ) {
				return true;
				break;
			}
		}

		return false;
	}
}

if ( !function_exists( 'get_asset_banner' ) ) {
	/**
	 * Gets a header string for a processed asset
	 *
	 * @param string $filename
	 * @param string $filetype
	 * @param bool $minified
	 * @param $duration
	 *
	 * @return string
	 */
	function get_asset_banner( $filename = '', $filetype = '', $minified = false, $duration ) {
		$ret = '';
		if ( isset( $duration ) ) {
			$time_taken = round( $duration * 1000 );
			$ret .= '/* time taken: ' . $time_taken . ' ms';
			$ret .= $minified ? ' (minified)' : '';
			$ret .= ' */' . PHP_EOL;
		}

		return $ret;
	}
}

if ( !function_exists( 'get_compile_error_css' ) ) {
	/**
	 * Gets a css rule for an exception message to visualize it
	 *
	 * @param Exception $e
	 * @param string $filename
	 * @param string $tool
	 *
	 * @return string
	 */
	function get_compile_error_css( $e, $filename = '', $tool = '' ) {
		$pattern   = array(
			'/ in file.*$/',
			'/ in anonymous-file.*$/',
			'/line: [0-9]+$/',
			'/[^A-Za-z0-9\.:;@$() -]/'
		);
		$error_msg = preg_replace( $pattern, '', $e->getMessage() );
		$msg       = 'body:before, body:after  {
display: block; box-sizing: border-box; position: fixed; top: 3vw; right: 3vw; bottom: 3vw; left: 3vw; z-index: 100000;
overflow: auto;
color: red; font: 20px/1.5 monospace; text-shadow: 1px 1px 1px rgba(255,255,255,.2); text-align: center;
border: 1px solid #ededff;
padding: 1.9em 2em;
background-color:#fff;
background-image: linear-gradient(0deg, #ededff 1px, transparent 1px), linear-gradient(90deg, #ededff 1px, transparent 1px);
background-size: 15px 15px, 15px 15px;
box-shadow: 0 25px 20px -20px rgba(0,0,0,.4);
white-space: pre-wrap;
content: "Ups, an error occured.\A ' . strtoupper( $tool ) . ' compile error - ' . $error_msg . ' \A in ' . addslashes( $filename ) . '";
}';

		return $msg;
	}
}

if ( !function_exists( 'get_less_parser' ) ) {
	/**
	 * Returns the less parser
	 *
	 * @return lessc
	 */
	function get_less_parser() {

		require_once BASE . 'app/library/lessphp/lessc.inc.php';
		$less = new lessc;

		//$less->setImportDir( array( '' ) ); // default
		//$less->addImportDir( 'assets/bootstrap' );

		return $less;
	}
}

if ( !function_exists( 'get_scss_parser' ) ) {
	/**
	 * Returns the scss parser
	 *
	 * @return scssc
	 */
	function get_scss_parser() {

		require_once BASE . 'app/library/scssphp/scss.inc.php';
		$scss = new scssc;

		//$scss->setImportPaths( array( '' ) ); // default
		//$scss->addImportPath( 'assets/bootstrap' );

		return $scss;
	}
}

// ------------------
// View templates
// ------------------
if ( !function_exists( 'render_view_template' ) ) {
	/**
	 * Gets / compiles the view template
	 *
	 * @param $view
	 *
	 * @throws Exception
	 */
	function render_view_template( $view ) {
		if ( file_exists( $view ) ) {
			require $view;
		}
		else {
			throw new Exception();
		}
	}
}

if ( !function_exists( 'render_view' ) ) {
	/**
	 * Renders the view
	 *
	 * @param $view
	 */
	function render_view( $view ) {
		$rendered = false;
		try {
			render_view_template( $view );
			$rendered = true;
		} catch ( Exception $e ) {
		}

		if ( !$rendered ) {
			header( 'HTTP/1.0 404 Not Found' );
			echo '<h1>404 - not found</h1>';
		}
	}
}

// ------------------
// Main Processing
// ------------------
if ( !function_exists( 'process_asset' ) ) {
	/**
	 * Processes a requested asset (from config.json)
	 */
	function process_asset() {
		global $config;

		foreach ( $config->assets as $asset => $value ) {
			if ( preg_match( '/\/' . $asset . '/', $_SERVER['REQUEST_URI'] ) ) {
				$filetype = substr( strrchr( $asset, '.' ), 1 );
				switch ( $filetype ) {
					case 'css':
						$mimetype = 'text/css';
						break;
					case 'js':
						$mimetype = 'text/javascript';
						break;
					default:
						$mimetype = '';
						break;
				}
				dump( $asset, $mimetype );
				exit();
			}
		}
	}
}

if ( !function_exists( 'process_view' ) ) {
	/**
	 * Processes a requested view
	 */
	function process_view() {
		global $config;

		$url    = str_replace( '?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI'] ); // remove query string
		$url    = preg_replace( '/\.[^.\s]{2,4}$/', '', $url ); // remove file extension
		$route  = explode( '/', $url );
		$action = end( $route );
		if ( $action == '' ) {
			$action = 'index';
		}
		$view = BASE . $config->micro->view_directory . '/' . $action . '.' . $config->micro->view_file_extension;
		if ( !is_file( $view ) ) {
			$view = BASE . $config->micro->view_directory . '/' . preg_replace( '/-/', '/', $action, 1 ) . '.' . $config->micro->view_file_extension;
		}
		render_view( $view );
	}
}

process_asset();
process_view();
