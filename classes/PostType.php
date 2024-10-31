<?php
class QodyPostType extends QodyPlugin
{
	var $m_type_slug 			= 'product';
	var $m_show_in_menu			= true;
	var $m_supports				= array();
	
	var $m_name 				= 'Products';
	var $m_singular_name 		= 'Product';
	var $m_add_new 				= 'Add New Product';
	var $m_add_new_item 		= 'Add New Product';
	var $m_edit_item 			= 'Edit Product';
	var $m_new_item 			= 'New Product';
	var $m_view_item 			= 'View Product';
	var $m_search_items 		= 'Search Products';
	var $m_not_found 			= 'No products found';
	var $m_not_found_in_trash 	= 'No products found in Trash';
	
	function __construct()
	{
		parent::__construct();
		
		$this->SetMassVariables( 'product', 'products' );
		
		add_action( "admin_init", array( $this, "AdminInit" ) );
		add_action( 'save_post', array( $this, "SavePost" ) );
		add_action( 'init', array( $this, "Init" ) );
	}
	
	function AdminInit()
	{
		$this->LoadMetaboxes();
		$this->BuildColumns();
	}
	
	function SetMassVariables( $singular, $plural )
	{
		$this->m_type_slug 			= strtolower( str_replace( ' ', '_', $singular ) );
	
		$this->m_name 				= ucwords( $plural );
		$this->m_singular_name 		= ucwords( $plural );
		$this->m_add_new 			= 'Add New '.ucwords( $singular );
		$this->m_add_new_item 		= 'Add New '.ucwords( $singular );
		$this->m_edit_item 			= 'Edit '.ucwords( $singular );
		$this->m_new_item 			= 'New '.ucwords( $singular );
		$this->m_view_item 			= 'View '.ucwords( $singular );
		$this->m_search_items 		= 'Search '.ucwords( $plural );
		$this->m_not_found 			= 'No '.strtolower( $plural ).' found';
		$this->m_not_found_in_trash 	= 'No '.strtolower( $plural ).' found in Trash';
	}
	
	function BuildColumns()
	{
		
	}
	
	function SavePost()
	{
		global $post;
		
		// verify if this is an auto save routine. 
		// If it is our form has not been submitted, so we dont want to do anything
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		// SHOULD PROBABLY FIX THIS
		//if( !wp_verify_nonce( $_POST['qody_noncename'] ) )
		//	return $post_id;
		
		if( !$_POST )
			return $post_id;
		
		foreach( $_POST as $key => $value )
		{
			if( strpos( $key, 'field_' ) !== false )
			{
				update_post_meta( $post->ID, str_replace( 'field_', '', $key ), $value );
			}
		}
	}
	
	function Init()
	{
		$labels = array
		(
			'name' 					=> _x( $this->m_name, 'post type general name' ),
			'singular_name' 		=> _x( $this->m_singular_name, 'post type singular name' ),
			'add_new' 				=> _x( $this->m_add_new, 'portfolio item' ),
			'add_new_item' 			=> __( $this->m_add_new_item ),
			'edit_item' 			=> __( $this->m_edit_item ),
			'new_item' 				=> __( $this->m_new_item ),
			'view_item' 			=> __( $this->m_view_item ),
			'search_items' 			=> __( $this->m_search_items ),
			'not_found' 			=> __( $this->m_not_found ),
			'not_found_in_trash' 	=> __( $this->m_not_found_in_trash ),
			'parent_item_colon' 	=> ''
		);
	 
		$args = array
		(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'menu_icon' => null,
			'rewrite' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'show_in_menu' => $this->m_show_in_menu,
			'supports' => $this->m_supports
		); 

		register_post_type( $this->m_type_slug , $args );
	}
}
?>