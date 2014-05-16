<?php

/*
 * Plugin Name: Skeleton Autoloader
 * Description: Register the autoloader for all our Skeleton classes, all of which must use the Skeleton top level namespace
 * Author: Jonathan Buttigieg
 */

defined( 'ABSPATH' ) or	die( 'Cheatin\' uh?' );

spl_autoload_register(function ($class) {

	$segments = array_filter( explode( "\\", $class ) );
	$first = array_shift( $segments );
	
	if ( $first === "Skeleton" ) {
		
		$path = dirname(__DIR__) . "/skeleton/" . implode("/", $segments) . ".php";
		
		if ( file_exists( $path ) ) {
			require $path;
		}
		
	}
	 
});