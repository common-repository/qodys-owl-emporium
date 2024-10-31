<?php
class QodyWordpress extends QodyPlugin
{
	function __construct()
	{
		parent::__construct();
	}
	
	function GetPostData( $postID )
	{
		global $wpdb;
		
		$queryString = "SELECT * FROM ".$wpdb->posts." WHERE ID = '$postID'";
		$data = $wpdb->get_row( $queryString );
		
		return $data;
	}
	
	function GetAllUsers()
	{
		global $wpdb;
		
		$data = $wpdb->get_results("SELECT * FROM $wpdb->users ORDER BY display_name ASC");
		
		return $this->GetClass('tools')->ObjectToArray( $data );
	}
	
	function ManualWidgetShow( $slug, $options = array() )
	{
		if( $options['no_title'] == 1 )
		{
			$bw = '';
			$aw = '';
		}
		
		switch( $slug )
		{
			case 'recent_posts':
				$postsWidget = new WP_Widget_Recent_Posts();
				
				$args = array();
				$args['name'] = 'Footer 2';
				$args['id'] = 'sidebar-4';
				$args['description'] = '';
				$args['before_widget'] = '<div class="menu">';
				$args['after_widget'] = '</div>'; 
				$args['before_title'] = '<h3>';		
				$args['after_title'] = '</h3>'; 		
				$args['widget_id'] = 'recent-posts-3';
				$args['widget_name'] = 'Recent Posts';
				
				$instance = array();
				$instance['title'] = 'Recent Posts';
				$instance['number'] = 7;
			
				$postsWidget->widget( $args, $instance );
				break;
			case 'pages':
				$pageWidget = new WP_Widget_Pages();
				
				$args = array();
				$args['name'] = 'Sidebar';
				$args['id'] = 'sidebar-1';
				$args['description'] = '';
				$args['before_widget'] = '<div class="menu">';
				$args['after_widget'] = '</div>'; 
				$args['before_title'] = '<h3>';		
				$args['after_title'] = '</h3>'; 		
				$args['widget_id'] = 'pages-3';
				$args['widget_name'] = 'Pages';
				
				$instance = array();
				$instance['title'] = 'Pages';
				$instance['sortby'] = 'post_title';
				$instance['exclude'] = '';
				
				$pageWidget->widget( $args, $instance );
				break;
			
			case 'text':
				$textWidget = new WP_Widget_Text();
				
				$args = array();
				$args['name'] = 'Footer 2';
				$args['id'] = 'sidebar-4';
				$args['description'] = '';
				$args['before_widget'] = '<div class="text">';
				$args['after_widget'] = '</div>'; 
				$args['before_title'] = '<h3>';		
				$args['after_title'] = '</h3>'; 		
				$args['widget_id'] = 'text-1';
				$args['widget_name'] = 'Text';
				
				$siteUrl = str_replace( 'http://', '', get_bloginfo('url') );
				
				$maxLength = 45;
				if( strlen($siteUrl) > $maxLength )
					$siteUrl = substr( $siteUrl, 0, $maxLength ).'...';
					
				$instance = array();
				$instance['title'] = 'We sell through Amazon!';
				$instance['text'] = "<p>".$siteUrl." is a participant in the Amazon Services LLC Associates Program, 
				an affiliate advertising program designed to provide a means for sites to earn advertising fees by 
				advertising and linking to amazon.com.</p>";
				
				$textWidget->widget( $args, $instance );
				
				break;
				
			case 'links':
				$linksWidget = new WP_Widget_Links();
				
				$args = array();
				$args['name'] = 'Footer 2';
				$args['id'] = 'sidebar-4';
				$args['description'] = '';
				$args['before_widget'] = '<div class="menu">';
				$args['after_widget'] = '</div>'; 
				$args['before_title'] = '<h3>';		
				$args['after_title'] = '</h3>'; 		
				$args['widget_id'] = 'links-3';
				$args['widget_name'] = 'Links';
				
				$instance = array();
				$instance['images'] = 0;
				$instance['name'] = 1;
				$instance['description'] = 0;
				$instance['rating'] = 0;
				$instance['category'] = 0;
			
				$linksWidget->widget( $args, $instance );
				break;
			case 'brand_list':
				$productWidget = new Qody_Product_Widget();
				
				$args = array();
				$args['name'] = 'Footer 2';
				$args['id'] = 'sidebar-4';
				$args['description'] = '';
				$args['before_widget'] = '<div class="menu">';
				$args['after_widget'] = '</div>'; 
				$args['before_title'] = '<h3>';		
				$args['after_title'] = '</h3>'; 		
				$args['widget_id'] = 'everniche-products-7';
				$args['widget_name'] = 'EverNiche Products';
				
				$instance = array();
				$instance['title'] = 'Bestselling Brands';
				$instance['name'] = '';
				$instance['product_size'] = 'large';
				$instance['type'] = 'best_sellers_brand_list';
				$instance['product_count'] = 6;
				$instance['display_direction'] = 'verticle';
				
				$productWidget->widget( $args, $instance );
				break;
			case 'brand_nav':
				$widget = new Qody_Product_Widget();
	
				$args = array();
				$args['name'] = 'Navigation Bar';
				$args['id'] = 'sidebar-1';
				$args['description'] = '';
				$args['before_widget'] = '<div id="categories-bg">';
				$args['after_widget'] = '</div>'; 
				$args['before_title'] = '';		
				$args['after_title'] = ''; 		
				$args['widget_id'] = 'everniche-products-3';
				$args['widget_name'] = 'EverNiche Products';
				
				$instance = array();
				$instance['title'] = '';
				$instance['name'] = '';
				$instance['product_size'] = 'large';
				$instance['type'] = 'best_sellers_brand_list';
				$instance['product_count'] = 6;
				$instance['display_direction'] = 'verticle';
				
				$widget->widget( $args, $instance );
				break;
			case 'long_product_list':
				$widget = new Qody_Product_Widget();
		
				$args = array();
				$args['name'] = 'Sidebar';
				$args['id'] = 'sidebar-2';
				$args['description'] = '';
				$args['before_widget'] = '<div class="products">';
				$args['after_widget'] = '</div>'; 
				$args['before_title'] = '<h3 class="widgettitle">';		
				$args['after_title'] = '</h3>'; 		
				$args['widget_id'] = 'everniche-products-4';
				$args['widget_name'] = 'EverNiche Products';
				
				$instance = array();
				$instance['title'] = 'Recommended Items';
				$instance['name'] = '';
				$instance['product_size'] = 'large';
				$instance['type'] = 'random';
				$instance['product_count'] = 10;
				$instance['display_direction'] = 'verticle';
				
				$widget->widget( $args, $instance );
				break;
			case 'horizontal_products_medium':
				$widget = new Qody_Product_Widget();
	
				$args = array();
				$args['name'] = 'Footer 1';
				$args['id'] = 'sidebar-3';
				$args['description'] = '';
				$args['before_widget'] = '<div class="post box">';
				$args['after_widget'] = '</div>'; 
				$args['before_title'] = '<h3 class="post-title">';		
				$args['after_title'] = '</h3>'; 		
				$args['widget_id'] = 'everniche-products-5';
				$args['widget_name'] = 'EverNiche Products';
				
				$instance = array();
				$instance['title'] = 'Recommended Items';
				$instance['name'] = '';
				$instance['type'] = 'random';
				$instance['product_size'] = 'large';
				$instance['product_count'] = 8;
				$instance['display_direction'] = 'horizontal';
				
				$widget->widget( $args, $instance );
				break;
			case 'horizontal_products_small':
				$widget = new Qody_Product_Widget();
	
				$args = array();
				$args['name'] = 'Footer 1';
				$args['id'] = 'sidebar-3';
				$args['description'] = '';
				$args['before_widget'] = '<div class="post box">';
				$args['after_widget'] = '</div>'; 
				$args['before_title'] = '<h3 class="post-title">';		
				$args['after_title'] = '</h3>'; 		
				$args['widget_id'] = 'everniche-products-6';
				$args['widget_name'] = 'EverNiche Products';
				
				$instance = array();
				$instance['title'] = 'Other Related Items';
				$instance['name'] = '';
				$instance['product_size'] = 'large';
				$instance['type'] = 'random';
				$instance['product_count'] = 4;
				$instance['display_direction'] = 'horizontal';
				
				$widget->widget( $args, $instance );
				break;
		}
	}
	
	function GetPostContent( $size = 'full', $stripImages = false )
	{
		global $post;
		
		$content = $post->post_content;
		$content = apply_filters('the_content', $content);
		$content = str_replace(']]>', ']]&gt;', $content);
		
		if( $stripImages || $post->post_category == 4 )
			$content = preg_replace("/<img[^>]+\>/i", "", $content);
		
		if( $size != 'full' )
		{
			$bits = explode( '<!--more-->', $content );
			$content = str_replace( '<p></p>', '', $bits[0] );
			$content = $this->SafeSubstr( $content, $size );
		}
		
		
		return $content;
	}
}
?>