<?php

/*
 * This file is part of the Skeleton package.
 *
 * (c) Jonathan Buttigieg <contact@wp-media.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );

function skeleton_has_favicon( $path = '' ) {
	
	if( ! $path && isset( $GLOBALS['theme'] ) ) {
		$path = $GLOBALS['theme']->favicon_uri;
	}
	
	$path = str_replace( WP_CONTENT_URL , WP_CONTENT_DIR, $path );
	return (bool) glob( $path . '/*', GLOB_NOSORT );
	
}