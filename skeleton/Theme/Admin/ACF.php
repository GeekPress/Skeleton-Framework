<?php

/*
 * This file is part of the Skeleton package.
 *
 * (c) Jonathan Buttigieg <contact@wp-media.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skeleton\Theme\Admin;
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );

class ACF {
	
	public function addOptionsSubPage( $settings ) {
		
		if( $GLOBALS['theme']->acf_options_sub_page ) {
			$settings = array_merge( $settings, $GLOBALS['theme']->acf_options_sub_page );
		}
		
		return $settings;

	}
	
}
