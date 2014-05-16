<?php

/*
 * This file is part of the Skeleton package.
 *
 * (c) Jonathan Buttigieg <contact@wp-media.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skeleton\CustomPostType;

class Register {

	private $slug 		= null; 	// Set $slug slug
	private $args 		= array();  // Set $args of custom post type
	private $label 		= array();  // Set $labels of custom post type
	private $roles		= null;		// Set $roles


	/**
	 * __construct function.
	 *
	 * @access public
	 * @param string $postType_slug
	 * @param array $args (default: array())
	 * @param array $labels (default: array())
	 * @param array $roles (default: array())
	 * @return void
	 */
	public function __construct( $slug, $args = array(), $labels = array(), $roles = array() )
	{

		  // Set the slug of the CPT
		  $this->setSlug( $slug );

		  // Set labels of the CPT
		  $this->setLabels( $slug, (array)$labels );

		  // Set args of the CPT
		  $this->setArgs( $args );

		  // Set roles are allowed to acces to the CPT
		  $this->setRoles( $roles );

		  // Call the register_post_type function
		  $this->register();

		  // Delete the_capacities
		  $this->deleteCapacities();

		  if( $this->roles )
			   $this->setCapacities(); // Set the_capacities
	}


	/**
	 * setSlug function.
	 *
	 * Set the slug of the custom post type
	 *
	 * @access private
	 * @param string $slug
	 * @return void
	 */
	private function setSlug( $slug )
	{
		$this->slug = sanitize_key( $slug );
	}


	/**
	 * setLabels function.
	 *
	 * Set labels of the custom post type
	 *
	 * @access private
	 * @param string $slug
	 * @param array $labels (default: array())
	 * @return void
	 */
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

	/**
	 * setArgs function.
	 *
	 * Set args of the custom post type
	 *
	 * @access private
	 * @param array $args
	 * @return void
	 */
	private function setArgs( $args )
	{
		$args = is_array( $args ) ? $args : (array)$args;
		$this->args = array_merge(
			  array(
			    'labels' 		=> $this->labels,
			    'public' 		=> true,
			    'has_archive' 	=> true,
			    'supports' 		=> array('title','editor')
			  ),
			  $args
		  );
	}


	/**
	 * setRoles function.
	 *
	 * Set roles are allowed to acces of the custom post type
	 *
	 * @access private
	 * @param mixed (array|string) $roles
	 * @return void
	 */
	private function setRoles( $roles )
	{
		if( !empty( $roles ) ) {
			$roles = is_array( $roles ) ? $roles : (array)$roles;
			$this->roles = $roles;
		}
		else {
			$this->roles = false;
		}
	}


	/**
	 * setCapacities function.
	 *
	 * @access private
	 * @return void
	 */
	private function setCapacities()
	{

		$slug = $this->slug;
		$capsToAdd = array();

  		foreach( $this->roles as $role )
  		{

  			$capsToAdd[] = 'read_' . $slug;

			if( $role == 'administrator' || $role == 'editor' || $role == 'author' || $role == 'contributor' )
			{
				array_push($capsToAdd, 'edit_' . $slug,
										 'edit_' . $slug . 's',
										 'delete_' . $slug,
										 'delete_' . $slug . 's');
			}


			if( $role == 'administrator' || $role == 'editor' || $role == 'author' )
			{
				array_push($capsToAdd, 'edit_others_' . $slug . 's',
										 'publish_' . $slug . 's');


			}

			if( $role == 'administrator' || $role == 'editor' )
			{
				array_push($capsToAdd, 'delete_others_' . $slug . 's',
										 'read_private_' . $slug . 's');
			}

			// Get informations of the role
			$r = get_role( $role );

			// Add cap for the good roles
			foreach( $capsToAdd as $cap )
			    $r->add_cap( $cap );


			$capsToAdd = array();
  		}

  		add_filter('map_meta_cap',
  				   function ( $caps, $cap, $userID, $args ) use ( $slug )
  				   {

	  				    /* If editing, deleting, or reading a client, get the post and post type object. */
						if ( 'edit_' . $slug == $cap || 'delete_' . $slug == $cap || 'read_' . $slug == $cap ) {
							$post = get_post( $args[0] );
							$postType = get_post_type_object( $post->post_type );

							/* Set an empty array for the caps. */
							$caps = array();

						} // if

						switch( $cap ) {

							/* If editing a post, assign the required capability. */
							case 'edit_' . $slug :
								$caps[] = ($userID == $post->post_author) ? $postType->cap->edit_posts : $postType->cap->edit_others_posts;
								break;

							/* If deleting a post, assign the required capability. */
							case 'delete_' . $slug :
								$caps[] = ($userID == $post->post_author) ? $postType->cap->delete_posts : $postType->cap->delete_others_posts;
								break;

							case 'read_' . $slug :
								$caps[] = ( 'private' != $post->post_status || $userID == $post->post_author ) ? 'read' : $postType->cap->read_private_posts;
								break;


						} // switch

						/* Return the capabilities required by the user. */
						return $caps;

  				   } // function
  				   ,10
  				   ,4 ); // add_filter

	} // private function


	/**
	 * deleteCapacities function.
	 *
	 * @access private
	 * @return void
	 */
	private function deleteCapacities() {

		// Get slug
		$slug = $this->slug;

		// Get all roles
		$r = new \WP_Roles();

  		// TO DO - DESCRIPTION
  		$all_roles = array_diff( array_keys($r->roles), (array)$this->roles);


  		// Get all capacities to remove
  		$caps_to_delete = array('read_' . $slug,
  								'edit_' . $slug,
								'edit_' . $slug . 's',
								'delete_' . $slug,
								'delete_' . $slug . 's',
								'edit_others_' . $slug . 's',
								'publish_' . $slug . 's',
								'delete_others_' . $slug . 's',
								'read_private_' . $slug . 's'
  						);


  		// Delete all caps for the others roles
  		foreach ( $all_roles as $role ) {

			$r = get_role( $role );

			// Add cap for the good roles
			foreach( $caps_to_delete as $cap )
			    $r->remove_cap( $cap );

		} // foreach

	} // private function


	/**
	 * register_post_type function.
	 *
	 * Declare and configure a new post type
	 *
	 * @access private
	 * @return void
	 */
	private function register()
	{
		  $slug 	= $this->slug;
		  $roles 	= $this->roles;
		  $args 	= $this->args;

		  if( !post_type_exists( $slug ) )
		  {
			  add_action('init', function() use( $slug, $args, $roles )
			  {

					if( $roles )
					{

				  		$args['capability_type'] = $slug;
				  		$args['capabilities'] = array(
				  				'publish_posts' => 'publish_' . $slug. 's',
								'edit_posts' => 'edit_' . $slug . 's',
								'edit_others_posts' => 'edit_others_' . $slug . 's',
								'delete_posts' => 'delete_' . $slug . 's',
								'delete_others_posts' => 'delete_others_' . $slug . 's',
								'read_private_posts' => 'read_private_' . $slug . 's',
								'edit_post' => 'edit_' . $slug,
								'delete_post' => 'delete_' . $slug,
								'read_post' => 'read_' . $slug,
								'edit_page' => 'edit_' . $slug,
				  		);
				  	} // if

				  	// Call the register_post_type function
				 	register_post_type( $slug, $args );

			  }); // add_action

		  } // if

	} // private function

}