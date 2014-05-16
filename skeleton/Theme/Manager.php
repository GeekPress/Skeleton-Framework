<?php

/*
 * This file is part of the Skeleton package.
 *
 * (c) Jonathan Buttigieg <contact@wp-media.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skeleton\Theme;
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );

require( 'functions.php' );

class Manager {
	
	/**
	 * Identifiant unique
	 *
	 * @var string
	 * @access protected
	 */
	protected $slug;
	
	
	protected $static_uri;

	/**
	 * Tailles des favions dans les différents formats/plateformes (Apple, Google & PNG)
	 *
	 * @var array
	 * @access protected
	 */
	protected $favicon = array(
		'google' => array( '96x96', '196x196' ),
		'apple'  => array( '57x57', '60x60', '72x72', '76x76', '120x120', '114x114', '144x144', '152x152'
		),
		'png' 	 => array( '16x16', '32x32', '96x96', '160x160' )
	);
	
	/**
	 * Chemin vers le dossier des favicons (URI)
	 *
	 * @var string
	 * @access protected
	 */
	protected $favicon_uri;
	
	/**
	 * Identifiant Google Analytics
	 *
	 * @var string
	 * @access protected
	 */
	protected $UA;

	protected $enqueue = array();
	
	protected $acf_options_sub_page;
	
	// Getters
	public function __get( $property ) {

        if ( property_exists( $this, $property ) ) {
            return $this->$property;
        }

    }

    // Setters
    public function __set( $property, $value ) {

        if ( property_exists( $this, $property ) )  {
            $this->$property = $value;
        }
    }

    // Hydratation
    public function hydrate( $data ) {

        if ( isset( $data ) ) {

            foreach ( $data as $key => $value ) {

                if ( property_exists( $this, $key ) ) {
                    $this->$key = $value;
                }

            }
        }
    }

	public function __construct( $data = null ) {

		if ( defined( 'STATIC_URI' ) && STATIC_URI ) {

			$data['static_uri'] = STATIC_URI;

		} else if( empty( $data['static_uri'] ) ) {

			$data['static_uri'] = get_template_directory_uri();
			$favicon_uri = $data['static_uri'] . '/favicons';

			if ( is_dir( $favicon_uri ) ) {
				$data['favicon_uri'] = $favicon_uri;
			}

		}

		if ( empty( $data['favicon_uri'] ) ) {
			$data['favicon_uri'] = $data['static_uri']. '/favicons';
		}

		$this->hydrate( $data );
		
		$this->init();
		
		

	}

	private function init() {
		
		if( ! is_admin() ) {	
			$this->enqueue();
		}
		
		if( is_admin() ) {
			$this->acf();	
		}
		
		$this->header();
		
	}

	public function acf() {
		
		$acf = new Admin\ACF();
		
		if( $this->acf_options_sub_page ) {
			add_filter( 'acf/options_page/settings', array( $acf, 'addOptionsSubPage' ) );
		}
		
	}

	private function header() {
		
		$header = new Front\Header();
		
		if( defined( 'APP_ENV' ) && APP_ENV == 'production' ) {
			
			if ( $this->UA ) {
				add_action( 'wp_head', array( $header, 'addGoogleAnalytics' ) );
			} else  {
				new Admin\Notice( 'google-analytics' );
			}
				
		}

		if( skeleton_has_favicon( $this->favicon_uri ) ) {
			add_action( 'wp_head', array( $header, 'addFavicon' ) );	
		}
		else {
			new Admin\Notice( 'favicon' );
		}
		
		if( apply_filters( 'skeleton_clean_wp_head', true ) ) {
			$header->clean();
		}
			
	}
	
	private function enqueue() {
		
		if( ! $this->enqueue ) {
			return false;
		}
		
		foreach ( $this->enqueue as $enqueue ) {
			new \Skeleton\Theme\Front\Enqueue( $enqueue );
		}

	}

}