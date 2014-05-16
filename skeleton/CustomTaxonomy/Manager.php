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

class Manager {

	protected $postType;
	protected $slug;
	protected $args;
	protected $labels;
	protected $config;
	
	//
	function __construct( $slug, $postType, $labels = array(), $args = array(), $config = array() ) 
	{
		
		$this->slug     = $slug;
		$this->postType = $postType;
		$this->labels   = $labels;
		$this->args     = $args;
		$this->config   = $config;
		$this->load();
		
	}
	

	//
	protected function load()
	{
		$this->register();
		
		add_filter( 'wp_terms_checklist_args', array( $this, 'setCheckListInRadio' ) );

	}



	//
	private function register()
	{
		
		new Register( $this->slug, $this->postType, $this->labels, $this->args );
		
	}



	//
	public function setCheckListInRadio( $args )
	{
	
		if( isset( $this->config['checkListInRadio'] ) && $this->config['checkListInRadio'] )
			$args['walker'] = new Walker\CheckListInRadio;
		return $args;
		
	}

}