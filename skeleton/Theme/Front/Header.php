<?php

/*
 * This file is part of the Skeleton package.
 *
 * (c) Jonathan Buttigieg <contact@wp-media.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skeleton\Theme\Front;
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );

class Header {
	
	public function addViewport() {
		
		echo '<meta name="viewport" content="' . esc_attr( $GLOBALS['theme']->viewport ) . '">' . "\n";
		
	}
	
	public function addFavicon() {

		$buffer 	 = '';
		$favicon_uri = apply_filters( 'skeleton_favicon_uri', $GLOBALS['theme']->favicon_uri );

		$apple_touch_icons = $GLOBALS['theme']->favicon['apple'];
		foreach ( $apple_touch_icons as $size ) {

			$buffer .= '<link rel="apple-touch-icon" sizes="' . $size . '" href="' . $favicon_uri . '/apple-touch-icon-' . $size . '.png">' . "\n";

		}

		$png_icons = $GLOBALS['theme']->favicon['png'];
		foreach ( $png_icons as $size ) {

			$buffer .= '<link rel="icon" type="image/png" href="' . $favicon_uri . '/favicon-' . $size . '.png" sizes="' . $size . '">' . "\n";

		}

		echo $buffer;

	}
	
	public function addGoogleAnalytics() {

		$UA     = $GLOBALS['theme']->UA;
		$buffer = '';

		if( $UA || preg_match( '/^UA-\d+-\d+$/', $UA ) == 1 ) {

			$domain = str_replace( array( 'http://', 'https://' ), '' , home_url() );

			$buffer = "<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');ga('create', '$UA', '$domain');ga('send', 'pageview');</script>";
			$buffer = apply_filters( 'skeleton_google_analytics', $buffer, $UA, $domain );

		}

		echo $buffer;

	}

	public function clean() {

		remove_action( 'wp_head',             'feed_links_extra',               3    );
		remove_action( 'wp_head',             'rsd_link'                             );
		remove_action( 'wp_head',             'wlwmanifest_link'                     );
		remove_action( 'wp_head',             'index_rel_link'                       );
		remove_action( 'wp_head',             'parent_post_rel_link',          10, 0 );
		remove_action( 'wp_head',             'start_post_rel_link',           10, 0 );
		remove_action( 'wp_head',             'adjacent_posts_rel_link',       10, 0 );
		remove_action( 'wp_head', 			  'adjacent_posts_rel_link_wp_head',10,0 );
		remove_action( 'wp_head',             'noindex',                        1    );
		remove_action( 'wp_head',             'rel_canonical'                        );
		remove_action( 'wp_head',             'wp_generator'                         );
		add_filter(    'wpseo_next_rel_link', '__return_false'					 	 );

	}

}