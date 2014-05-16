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

class Enqueue {

	protected $enqueue;

	function __construct( $enqueue ) {

		$this->enqueue = $enqueue;
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );

	}

	public function enqueue() {

		global $wp_scripts;

		$handle = $this->enqueue['file'][0];
		$src    = $this->enqueue['file'][1];
		$src    = ! parse_url( $src, PHP_URL_HOST ) ? $GLOBALS['theme']->static_uri . $src : $src;
		$ext    = pathinfo( $src , PATHINFO_EXTENSION );
		$deps   = isset ( $this->enqueue['file'][2] ) ? $this->enqueue['file'][2] : false;
		$ver    = isset ( $this->enqueue['file'][3] ) ? $this->enqueue['file'][3] : false;
		$media  = isset ( $this->enqueue['file'][4] ) ? $this->enqueue['file'][4] : null;

		$wp_register = $ext == 'css' ? 'wp_register_style' : 'wp_register_script';
		$wp_enqueue  = $ext == 'css' ? 'wp_enqueue_style' : 'wp_enqueue_script';

		// Register file
		$wp_register( $handle, $src, $deps, $ver, $media );

		if( isset( $this->enqueue['yep'] ) ) {

			foreach( $this->enqueue['yep'] as $tag => $args ) {

				if( is_string( $tag ) ) {

					if( $tag($args) ) {
						$wp_enqueue( $handle );
					}

				} else {

					if( $args() ) {
						$wp_enqueue( $handle );
					}

				}

			}

		}

		if( isset( $this->enqueue['nope'] ) ) {

			foreach( $this->enqueue['nope'] as $tag => $args ) {

				if( is_string( $tag ) ) {

					if( ! $tag($args) ) {
						$wp_enqueue( $handle );
					}

				} else {

					if( ! $args() ) {
						$wp_enqueue( $handle );
					}

				}

			}

		}

		if( ! isset( $this->enqueue['yep'] ) && ! isset( $this->enqueue['nope'] ) ) {
			$wp_enqueue( $handle );
		}

		if( isset( $this->enqueue['conditional'] ) && $ext == 'css' ) {
			wp_style_add_data( $handle, 'conditional', $this->enqueue['conditional'] );
		}

	}

}