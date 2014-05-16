<?php

/*
 * This file is part of the Skeleton package.
 *
 * (c) Jonathan Buttigieg <contact@wp-media.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skeleton\CustomTaxonomy;

class Register {

	//
	private $postType;

	//
	private $slug;

	//
	private $args;

	//
	private $labels;



	//
	public function __construct( $slug, $postType, $labels = array(), $args = array()  )
	{
		
		$this->postType = $postType;

		// Set the slug of the taxonomy
		$this->setSlug( $slug );

		// Set labels of the taxonomy
		$this->setLabels( $slug, (array)$labels );

		// Set args of the taxonomy
		$this->setArgs( $args );

		add_action( 'init', array( $this, 'register' ) );

	}



	//
	private function setSlug( $slug )
	{

		$this->slug = sanitize_key( $slug );

	}



	//
	private function setLabels( $slug, $labels )
	{

		  //Capitilize the words and make it plural
    	  $singular   = ucwords( preg_replace( '#([_-])#', ' ', $slug ) );
    	  $plural     = $singular . 's';

		  // Default
	      $this->labels = array_merge(
		      array(
		           'name'                  => $plural,
		           'singular_name'         => $singular,
		           'menu_name'             => $plural
		       ),
		       $labels
		  );

	}



	//
	private function setArgs( $args )
	{

		$args = is_array( $args ) ? $args : (array)$args;
		$this->args = array_merge(
			  array(
			    'hierarchical' => true,
				'labels' => $this->labels
			  ),
			  $args
		  );

	}



	//
	public function register()
	{

		if( !taxonomy_exists( $this->slug ) )
			register_taxonomy( $this->slug, $this->postType, $this->args );
		else
			register_taxonomy_for_object_type( $this->slug, $this->postType );

	}

}