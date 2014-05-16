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

class Manager {


	/**
	 * Identifiant unique
	 *
	 * @var string
	 * @access protected
	 */
	protected $slug;

	/**
	 * Libellés
	 *
	 * @var array
	 * @access protected
	 */
	protected $labels;

	/**
	 * Options correspondants à la variable $args de la fonction register_post_type()
	 *
	 * @var array
	 * @access protected
	 */
	protected $args;

	/**
	 * Liste des rôles qui ont les droits d'accès au CPT
	 *
	 * @var string or array
	 * @access protected
	 */
	protected $roles;

	/**
	 * Liste des taxonomies à déclarer
	 *
	 * @var array
	 * @access protected
	 */
	protected $taxonomies;

	/**
	 * Liste les différents formats d'image utilisés par le CPT
	 *
	 * @var array
	 * @access protected
	 */
	protected $imageSizes;

	/**
	 * Détermine si le CPT peut-être mis en avant via la fonctionnalité des Sticky Posts
	 *
	 * @var bool
	 * @access protected
	 */
	protected $isSticky;



	function __construct( $slug, $labels = array(), $args = array(), $roles = array() )
	{

		$this->slug            = $slug;
		$this->labels          = $labels;
		$this->args            = $args;
		$this->roles           = $roles;
		$this->load();

	}



	/**
	 * Chargement des différents modules
	 *
	 * @access protected
	 * @return void
	 */
	protected function load()
	{

		// Si la class Register n'existe pas,
		// on retourne false pour éviter d'avoir des erreurs par la suite
		if ( ! class_exists( 'Skeleton\CustomPostType\Register' ) ) {
			return false;
		}

		// Déclaration du CPT
		$this->register();

		// Chargement du module des Sticky Posts
		if( $this->isSticky ) {

			add_action( 'admin_footer-post.php', array( $this, 'loadStickyPostSupport' ) );
			add_action( 'admin_footer-post-new.php', array( $this, 'loadStickyPostSupport' ) );

		}

		// Chargement des nouveaux formats d'image
		add_action( 'init', array( $this, 'loadImageSizes' ) );

	}



	/**
	 * Déclaration du CTP et de ses taxonomies
	 *
	 * @access private
	 * @return void
	 */
	private function register()
	{

		// Si la class est utilisée pour les articles et les pages,
		// on ne doit pas lancer une déclaration d'un nouveau CPT
		if ( $this->slug != 'post' && $this->slug != 'page' ) {
			new Register( $this->slug, $this->args, $this->labels, $this->roles );
		}


		// Déclaration des taxonomies
		foreach( (array)$this->taxonomies as $t ) {
			
			extract( $t );
			
			if ( ! empty( $slug ) ) {
				
				$labels = isset( $labels ) ? $labels : false;
				$args   = isset( $args )   ? $args 	 : false;
				$config = isset( $config ) ? $config : false;
				
				new \Skeleton\CustomTaxonomy\Manager( $slug, $this->slug, $labels, $args, $config );
					
			}
			
			unset( $labels, $args, $config );

		}

	}



	/**
	 * Déclaration des nouveaux formats d'image
	 *
	 * @access public
	 * @return void
	 */
	public function loadImageSizes()
	{

		// Si on n'a pas de nouveaux formats d'image,
		// pas la peine de faire le job qui suit
		if ( ! count( $this->imageSizes ) ) {
			return false;
		}

		// Add the theme support for post thumbnails if the current theme isn't support it
		if( ! current_theme_supports( 'post-thumbnails' ) ) {
			add_theme_support( 'post-thumbnails' );
		}

		foreach ( $this->imageSizes as $name => $size ) {

			list( $width, $height, $crop ) = $size;

			// On ajoute le nouveau format d'image
			add_image_size( $name, $width, $height, $crop );

		}

	}



	/**
	 * Support des Sticky Posts
	 *
	 * L'ajout se fait en JavaScript car WordPress ne fournit aucun hook pour ajouter le support
	 * des sticky posts de la même façon que les articles
	 *
	 * @access public
	 * @return void
	 */
	public function loadStickyPostSupport()
	{

		global $post, $typenow;
		if ( $typenow == $this->slug && current_user_can( 'edit_others_posts' ) ) { ?>

			<script>
			jQuery(function($)
			{
				var sticky = "<br/><span id='sticky-span'><input id='sticky' name='sticky' type='checkbox' value='sticky' <?php checked( is_sticky( $post->ID ) ); ?> /> <label for='sticky' class='selectit'><?php _e( "Stick this post to the front page" ); ?></label><br /></span>";
				$('[for=visibility-radio-public]').append(sticky);
			});
			</script>

		<?php }

	}

}