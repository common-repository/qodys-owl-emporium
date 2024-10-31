<?php
require_once( dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-config.php' );

$response = array();

if( $_POST )
{
	require_once(ABSPATH . 'wp-admin/includes/plugin.php'); //for plugins_api..
	require_once(ABSPATH . 'wp-admin/includes/file.php');
	require_once(ABSPATH . 'wp-admin/includes/template.php');
	require_once(ABSPATH . 'wp-admin/includes/misc.php');
	require_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
	
	if( !class_exists('Blank_Skin') )
	{
		class Blank_Skin extends Bulk_Plugin_Upgrader_Skin {
		
			function __construct($args = array()) {
				parent::__construct($args);
			}
			
			function header() {}
			function footer() {}
			function error($errors) {}
			function feedback($string) {}
			function before() {}
			function after() {}
			function bulk_header() {}
			function bulk_footer() {}
			function show_message() {}
			
			function flush_output() {
				ob_end_clean();
			}
		}
	}
	
	$plugin_url = $_POST['plugin_url'];
	
	if( !$plugin_url )
	{
		$response['errors'][] = 'Please enter a plugin url';
	}
	else
	{
		ob_get_contents();
		$upgrader = new Plugin_Upgrader( new Blank_Skin() );
		$upgrader->install( $plugin_url );
		ob_end_clean();
	
		$response['results'][] = 'Success';
	}
}
else
{
	$response['errors'][] = 'Any unexpected error occured; please try again';
}

echo json_encode( $response );
?>