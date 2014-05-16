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

class Notice {
	
	protected $slug;
	
	protected $notices = array(
		'google-analytics' => 'Attention, le code Google Analytics n\'est pas défini.',
		'favicon'		   => 'Attention, aucun favicon n\'est présent pour votre thème.' 
	);
	
	public function __construct( $slug ) {
		
		$this->slug = $slug;
		
		add_action( 'admin_notices', array( $this, 'render' ) );
		
	}
	
	public function render() {
				
		if( current_user_can( apply_filters( 'skeleton_notices_capacity', 'manage_options' ) ) && apply_filters( 'skeleton_notices', true, $this->slug ) ) {
			
			$notice = '<p><b>Skeleton Framework</b>: ' . esc_html( $this->notices[$this->slug] ). '</p>';	
			$buffer = '<div class="error">';
				$buffer .= apply_filters( 'skeleton_notices_' . $this->slug, $notice );
			$buffer .= '</div>';
			
			echo $buffer;

		}

	}

}
