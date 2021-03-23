<?php
/**
 * Autoloader
 *
 * @param   string  $class
 *
 * @return  boolean
 */
function whp_plugin_autoloader( $class ) {
    $dir = '/inc';
    $type = 'class';

	switch ( $class ) {
		case false !== strpos( $class, 'MartinCV\\WHP\\Admin\\' ):
										$class = strtolower( str_replace( 'MartinCV\\WHP\\Admin', '', $class ) );
										$dir .= '/admin';
										break;
		case false !== strpos( $class, 'MartinCV\\WHP\\Traits\\' ):
                                        $class = strtolower( str_replace( 'MartinCV\\WHP\\Traits', '', $class ) );
                                        $dir .= '/traits';
                                        $type = 'trait';
										break;
		case false !== strpos( $class, 'MartinCV\\WHP\\Core\\' ):
                                        $class = strtolower( str_replace( 'MartinCV\\WHP\\Core', '', $class ) );
                                        $dir .= '/core';
										break;
		case false !== strpos( $class, 'MartinCV\\WHP\\' ):
                                        $class = strtolower( str_replace( 'MartinCV\\WHP', '', $class ) );
										break;
		default: return;
	}

	$filename = WHP_PLUGIN_DIR . $dir . str_replace( '_', '-', str_replace( '\\', '/' . $type . '-', $class ) ) . '.php';

	if ( file_exists( $filename ) ) {
		require_once $filename;

		if ( class_exists( $class ) ) {
			return true;
		}
	}

	return false;
}

spl_autoload_register( 'whp_plugin_autoloader' );
