<?php
require_once( dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/wp-config.php' );

$response = array();

if( $_POST )
{
	$api_key = $_POST['api_key'];
	$pre = $_POST['plugin_pre'];
	
	if( !$api_key )
	{
		$response['errors'][] = 'You must enter an O.I.N. number to begin';
	}
	else
	{
		$response = QodyPlugin::ConnectToUnlocker( $api_key );

		if( !$response['errors'] )
		{
			update_option( $pre.'api_key', $api_key );
		}
		
		//$response['errors'][] = "We couldn't verify that O.I.N. Please try again or contact support at support@qody.co";
	}
}
else
{
	$response['errors'][] = 'Any unexpected error occured; please try again';
}

$qodys_fbmeta->GetClass('postman')->SetMessage( $response );

$url = $qodys_fbmeta->GetClass('tools')->GetPreviousPage();

header( "Location: ".$url );
exit;
?>