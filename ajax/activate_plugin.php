<?php
require_once( dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-config.php' );

$response = array();

if( $_POST )
{
	require_once(ABSPATH . 'wp-admin/includes/plugin.php'); //for plugins_api..

	$plugin_url = $_POST['plugin_url'];
	//$api = plugins_api('plugin_information', array('slug' => 'qodys-framework', 'fields' => array('sections' => false) ) ); //Save on a bit of bandwidth.
	
	if( !$plugin_url )
	{
		$response['errors'][] = 'Please enter a plugin url';
	}
	else
	{
		$result = activate_plugin( $plugin_url );
		//$upgrader = new Plugin_Upgrader( new Blank_Skin() );
		//$upgrader->install( $plugin_url );
	
		$response['results'][] = 'Success';
	}
}
else
{
	$response['errors'][] = 'Any unexpected error occured; please try again';
}

echo json_encode( $response );
?>