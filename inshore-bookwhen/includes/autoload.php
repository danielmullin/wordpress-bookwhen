<?php

defined( 'ABSPATH' ) || exit;

spl_autoload_register(
	function ( $class ) {
		$prefix   = 'InShore\\Bookwhen\\';
		$base_dir = __DIR__;
		$len      = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			// Nothing found.
			return;
		}
		$relative_class = substr( $class, $len );
	
		$file           = $base_dir . '/' . str_replace( '\\', '/', $relative_class ) . '.php';
//echo $file . "\n";
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
);