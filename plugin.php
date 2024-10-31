<?php
/**
 * Plugin Name: Qody's Owl Emporium
 * Plugin URI: http://qody.co
 * Description: Gathering place for all of Qodys hireable owls.
 * Version: 1.0.9
 * Author: Qody LLC
 * Author URI: http://qody.co
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

if( !class_exists('QodysEmporium') )
{
	class QodysEmporium
	{
		// general plugin variables
		var $m_plugin_name = 'Qodys Emporium';
		var $m_plugin_slug = 'qodys-emporium';
		var $m_plugin_file;
		var $m_plugin_folder;
		var $m_plugin_url;
		
		// owl variables
		var $m_owl_name = 'Tem';
		var $m_owl_iamge = 'http://plugins.qody.co/wp-content/uploads/2011/09/owl6a-320x320.png';
		var $m_owl_buy_url = 'http://plugins.qody.co/owls/';
		
		// current page-specific variables
		var $m_page_url;
		var $m_page_url_args;
		var $m_page_referer;
		
		var $m_pages = array();
		var $m_globals = array();
		var $m_owls = array();
		
		// Plugin-wide variables
		var $m_pre;
		var $m_plugin_version;
	
		function __construct()
		{
			$this->m_pre = 'qoe';
			$this->m_plugin_file = plugin_basename(__FILE__);
			$this->m_plugin_folder = dirname(__FILE__);
				
			// Function to run when plugin is activated
			register_activation_hook( $this->m_plugin_file, array( &$this, 'LoadDefaultOptions' ) );
			
			// Set the general class variables
			$this->SetupPluginVariables();
			$this->SetupOwlVariables();
		}
		
		function RegisterPlugin()
		{
			// Handle the plugin activation functions, like setting default options
			$this->LoadScripts();
			$this->LoadStyles();
	
			$this->SetupHooks();
		}
		
		function SetupHooks()
		{
			add_action('admin_menu', array( $this, 'LoadWordpressPages' ), 1 );
			
			//add_action( 'wp_print_scripts', 'enqueue_my_scripts' );
			//add_action( 'wp_print_styles', array( $this, 'LoadStyles' ) );
			//add_action( 'admin_print_scripts', 'enqueue_my_scripts' );
			//add_action( 'admin_print_styles', array( $this, 'LoadStyles' ) );
		}
		
		function ConnectToUnlocker( $api_key, $prefix )
		{
			$connector = new QodySystemLinker( $api_key, $prefix );
			
			return $connector->ProcessUnlock();
		}
	
		function LoadWordpressPages()
		{
			$fields = array();
			$fields['name'] = $this->m_plugin_name;
			$fields['function'] = array(&$this, 'page_home');
			$fields['image'] = $this->m_plugin_url.'/images/qody-icon.png';
			
			$this->CreatePage( 'home', $fields );
			
			$this->AttachMetaboxContent();
		}
	
		function SetupPluginVariables()
		{
			if( !$this->m_plugin_folder )
				$this->m_plugin_folder = dirname(__FILE__);
				
			$this->m_plugin_folder = rtrim( $this->m_plugin_folder, '/' );
			
			$this->m_plugin_url = rtrim(get_bloginfo('wpurl'), '/') . '/' . substr(preg_replace("/\\//si", "/", $this->m_plugin_folder), strlen(ABSPATH));
		}
		
		function SetupOwlVariables()
		{
			$owl = array();
			$owl['owl_name'] = 'Tem';
			$owl['owl_url'] = 'http://plugins.qody.co/owl/tem/';
			$owl['download_url'] = 'http://plugins.qody.co/download/?p=qodys-framework';		
			$owl['image_url'] = 'https://s3.amazonaws.com/qody/logos/owls/tem/owl7a-300x300.png';
			$owl['wordpress_url'] = '';
			$owl['plugin_name'] = 'Qody\'s Framework Plugin';
			$owl['plugin_url'] = 'qodys-framework/framework.php';
			$this->m_owls[] = $owl;
			
			$owl = array();
			$owl['owl_name'] = 'Alejandro';
			$owl['owl_url'] = 'http://plugins.qody.co/owl/alejandro/';
			$owl['download_url'] = 'http://downloads.wordpress.org/plugin/qodys-redirector.zip';
			$owl['image_url'] = 'https://s3.amazonaws.com/qody/logos/owls/alejandro/owl5a-300x300.png';
			$owl['wordpress_url'] = 'http://wordpress.org/extend/plugins/qodys-redirector/';
			$owl['plugin_name'] = 'Qody\'s Redirector';
			$owl['plugin_url'] = 'qodys-redirector/plugin.php';
			$this->m_owls[] = $owl;
			
			$owl = array();
			$owl['owl_name'] = 'Penelope';
			$owl['owl_url'] = 'http://plugins.qody.co/owl/penelope/';
			$owl['download_url'] = 'http://downloads.wordpress.org/plugin/qodys-fb-meta.zip';
			$owl['image_url'] = 'https://s3.amazonaws.com/qody/logos/owls/penelope/owl6a-300x300.png';
			$owl['wordpress_url'] = 'http://wordpress.org/extend/plugins/qodys-fb-meta/';
			$owl['plugin_name'] = 'Qody\'s FB Meta';
			$owl['plugin_url'] = 'qodys-fb-meta/plugin.php';
			$this->m_owls[] = $owl;
			
			$owl = array();
			$owl['owl_name'] = 'Odin';
			$owl['owl_url'] = 'http://plugins.qody.co/owl/odin/';
			$owl['download_url'] = 'http://downloads.wordpress.org/plugin/qodys-optiner.zip';
			$owl['image_url'] = 'https://s3.amazonaws.com/qody/logos/owls/odin/owl9a-300x300.png';
			$owl['wordpress_url'] = 'http://wordpress.org/extend/plugins/qodys-optiner/';
			$owl['plugin_name'] = 'Qody\'s Optiner';
			$owl['plugin_url'] = 'qodys-optiner/plugin.php';
			$this->m_owls[] = $owl;
		}
		
		function AttachMetaboxContent()
		{
			// Load the page-specific metaboxes as each page loads
			foreach( $this->m_pages as $key => $value )
			{
				add_action( 'admin_head-'.$value['pagehook'], array(&$this, 'Metaboxes_'.$key) );
			}
		}
		
		function CreatePage( $slug, $data, $type = 'main' )
		{
			$menu_slug = $this->m_plugin_slug.'-'.$slug.'.php';
			
			if( $type == 'main' )
			{
				$hook = add_menu_page
				(
					$data['name'],		// Plugin title
					$data['name'], 		// Menu title
					1, 					// Capability
					$menu_slug,			// Menu slug
					$data['function'], 	// Loading function
					$data['image']		// Icon url
					//1					Menu position
				);
			}
			else
			{
				$hook = add_submenu_page
				(
					$data['parent'],	// Parent menu slug
					$data['name'], 		// Page title
					$data['name'], 		// Menu title
					1, 					// Capability
					$menu_slug, 		// Menu slug
					$data['function']	// Loading function
				);
			}
			
			$this->m_pages[ $slug ]['slug'] = $menu_slug;
			$this->m_pages[ $slug ]['pagehook'] = $hook;
		}
	
		// Simple method to include the metabox content files
		function ShowMetabox( $box )
		{
			$box_file = $this->m_plugin_folder.'/metaboxes/'.$box.'.php';
			
			if( !file_exists( $box_file ) )
				$box_file = dirname( __FILE__ ).'/metaboxes/'.$box.'.php';
				
			include_once( $box_file );
		}
	
		function GetUrl()
		{
			return $this->m_plugin_url;
		}
	
		// Loads any javascript files used in the plugin - both in the admin area and user area
		function LoadScripts()
		{		
			if( is_admin() )
			{
				wp_enqueue_script('jquery', false, array(), false, true);
				//wp_enqueue_script('jquery-ui-sortable', false, array(), false, true);
				
				// These are required for metaboxes to do their fancy bits
				wp_enqueue_script('common', false, array(), false, true);
				wp_enqueue_script('wp-lists', false, array(), false, true);
				wp_enqueue_script('postbox', false, array(), false, true);	
			}
			else
			{
				// Loads up javascript files for non-admin users (site visitors?)
			}
		}
		
		// Here is where we set the starting values for all inputs / options
		function LoadDefaultOptions()
		{
			if( !$this->get_option( 'version' ) )
			{
				//$this->update_option( 'version', $this->m_plugin_version );	
			}
		}
		
		// This loads the stylesheet files used in the plugin
		function LoadStyles()
		{
			// Load framework-specific styles
			wp_enqueue_style( $this->m_pre.'styles', plugins_url( 'css/style.css' , __FILE__ ) );
	
			// Load plugin-specific styles
		}
	
		// This finds the page file to load and loads it
		function RenderPage( $file, $params = array() )
		{
			if( !empty($file) )
			{
				$file_name = $file.'.php';
				$file_path = $this->m_plugin_folder.'/pages/';
				$file_url = $file_path.$file_name;
				
				if( !file_exists($file_url) )
					$file_url = dirname( __FILE__ ).'/pages/'.$file_name;
	
				if( file_exists($file_url) )
				{
					// Set variables in the parameters to be visible individually
					if( !empty($params) )
					{
						foreach( $params as $key => $value )
						{
							${$key} = $value;
						}
					}
					
					include( $file_url );
	
					return true;
				}
			}
			
			return false;
		}
		
		function Metaboxes_home()
		{
			$the_hook = $this->m_pages['home']['pagehook'];
			$pre = $this->m_pre;
	
			// Sidebar boxes
			add_meta_box( $this->m_pre.'save', 'Announcements', array($this, 'box_save'), $the_hook, 'side', 'core');
			
			// Main area boxes
			add_meta_box( $pre.'general', 'The Owls', array($this, 'box_owl_listing'), $the_hook, 'normal','core');
	
			// Tell wordpress to load these new metaboxes when this function is run
			do_action( 'do_meta_boxes', $the_hook, 'normal', $data );
			do_action( 'do_meta_boxes', $the_hook, 'advanced', $data );
			do_action( 'do_meta_boxes', $the_hook, 'side', $data );
		}
		
		// Create the wordpress metabox-display functions
		function box_save() 			{$this->ShowMetabox( 'save' ); }
		function box_owl_listing() 		{$this->ShowMetabox( 'owl_listing' ); }
		
		// Create the wordpress page-display functions
		function page_home()	{$this->RenderPage( 'page_home' ); }
		
		// Calls wordpress' add_option function, but with customizations
		function add_option( $slug )	
		{
			add_option( $this->m_pre.$slug, $value );
		}
		
		// Calls wordpress' get_option function, but with customizations
		function get_option( $slug, $clean = false, $framework = false )	
		{
			$pre = $this->m_pre;
	
			if( $framework )
				$pre = QODYS_FRAMEWORK_PREFIX;
						
			$option = get_option( $pre.$slug );
			
			if( $clean )
				$option = $this->GetClass('tools')->Clean( $option );
				
			return $option;
		}
		
		// Calls wordpress' update_option function, but with customizations
		function update_option( $slug, $value )	
		{
			update_option( $this->m_pre.$slug, $value );
		}
		
		// Calls wordpress' delete_option function, but with customizations
		function delete_option( $slug, $framework = false )	
		{
			$pre = $this->m_pre;
			
			if( $framework )
				$pre = QODYS_FRAMEWORK_PREFIX;
				
			delete_option( $pre.$slug );
		}
		
		function SetMessage( $new_message )
		{
			$current_messages = $this->DecodeResponse( $this->get_option('qody_message') );
			
			if( !is_array( $new_message ) )
				$new_message = $this->DecodeResponse( $new_message );
	
			if( $new_message['results'] )
			{
				foreach( $new_message['results'] as $key => $value )
					$current_messages['results'][] = $value;
				
				$current_messages['results'] = array_unique( $current_messages['results'] );
			}
			
			if( $new_message['errors'] )
			{
				foreach( $new_message['errors'] as $key => $value )
					$current_messages['errors'][] = $value;
				
				$current_messages['errors'] = array_unique( $current_messages['errors'] );
			}
			
			$current_messages = $this->EncodeResponse( $current_messages );
	
			$this->update_option('qody_message', $current_messages );
		}
		
		function DecodeResponse( $response )
		{
			return $this->ObjectToArray( json_decode($response) );
		}
		
		function EncodeResponse( $response )
		{
			return json_encode($response);
		}
		
		function ObjectToArray( $object )
		{
			if( !is_object( $object ) && !is_array( $object ) )
			{
				return $object;
			}
			if( is_object( $object ) )
			{
				$object = get_object_vars( $object );
			}
			
			return array_map( array($this, 'ObjectToArray'), $object );
		}
	
		function DisplayMessages( $return = false )
		{
			$message = $this->get_option('qody_message', false, true);
			$message = $this->DecodeResponse( $message );
			
			$content = $this->MessagesStyles();
			
			if( $message )
			{
				if( $message['errors'] )
				{
					foreach( $message['errors'] as $key => $value )
					{
						$content .= <<< CONT
				<div class="qody_message error_message"><p><strong>{$value}</strong></p></div>
CONT;
					}
				}
				
				if( $message['results'] )
				{
					foreach( $message['results'] as $key => $value )
					{
						$content .= <<< CONT
				<div class="qody_message success_message"><p><strong>{$value}</strong></p></div>
CONT;
					}
				}
				
				$this->delete_option('qody_message', true);
			}
			
			if( $return )
				return $content;
			
			echo $content;
		}
		
		function GetPreviousPage()
		{
			$url = $_SERVER['HTTP_REFERER'];
			
			if( !$url )
				$url = $this->m_url;
			
			return $url;
		}
		
		function MessagesStyles()
		{
			$content = <<< CONT
		<style>
		/* message styles */
		.qody_message {position:relative;color:#565656;border:1px solid #f2eda1;background:#fefbd0 url("https://qody.s3.amazonaws.com/framework_plugin/images/notifications/notify_bg.png") 0 0 repeat-x;margin-bottom:10px;-moz-border-radius:3px;-webkit-border-radius:3px;border-radius:3px;}
		.qody_message p {padding:10px 10px 10px 35px;margin:0 !important;line-height:140%;background:url("https://qody.s3.amazonaws.com/framework_plugin/images/notifications/warning.png") 10px 50% no-repeat;}
		.qody_message.success_message {background-color:#f3fed0;border:1px solid #def2a1;}
		.qody_message.success_message p {background-image:url("https://qody.s3.amazonaws.com/framework_plugin/images/notifications/success.png");}
		.qody_message.error_message {background-color:#feeaea;border:1px solid #fadadb;}
		.qody_message.error_message p {background-image:url("https://qody.s3.amazonaws.com/framework_plugin/images/notifications/error.png");}
		.qody_message.information {background-color:#eaf8fe;border:1px solid #cde6f5;}
		.qody_message.information p {background-image:url("https://qody.s3.amazonaws.com/framework_plugin/images/notifications/info.png");}
		.qody_message.tip {border:1px solid #fdd845;background-color:#fff6bf;}
		.qody_message.tip p {background-image:url("https://qody.s3.amazonaws.com/framework_plugin/images/notifications/tooltip.png");}
		.qody_message.closeable {cursor:pointer;}
		.qody_message.closeable p {padding-right:15px;}
		.qody_message div.close {position:absolute;top:1px;right:4px;font:bold 13px Arial;/*text-indent:-999em;width:24px;height:24px;background:url("https://qody.s3.amazonaws.com/framework_plugin/images/notifications/close.png") 0 0 no-repeat;*/}
		</style>
CONT;
		
			return $content;
		}
	}
	
	// Include all the classes and required files
	require_once( ABSPATH.WPINC.'/pluggable.php' );
	
	$qodys_emporium = new QodysEmporium();
	$qodys_emporium->RegisterPlugin();
}
?>