<?php
class QodySystemLinker extends QodyPlugin
{
	var $m_api_key;
	var $m_prefix;
	
	function __construct( $api_key = '', $prefix = '' )
	{
		// do nothing
		
		if( $api_key )
			$this->m_api_key = $api_key;
		else
			$this->m_api_key = $this->get_option('api_key');
		
		if( $prefix )
			$this->m_prefix = $prefix;
	}
	
	function ProcessUnlock()
	{
		$fields = array();
		$fields['action'] = 'unlock';
		
		$result = $this->SendCommand( $fields );

		$result = $this->DecodeResponse( $result );
		
		return $result;
	}
	
	function DecodeResponse( $data )
	{
		return $this->GetClass('tools')->ObjectToArray( json_decode($data) );
	}
	
	function PackArray( $data )
	{
		return base64_encode( serialize( $data ) );
	}
	
	function UnpackArray( $data )
	{
		return base64_decode( unserialize( $data ) );
	}
	
	function GetProductID()
	{
		switch( $this->m_prefix )
		{
			case 'qfm': return 336;
			case 'qrd': return 382;
			
			default: return -1;
		}
	}
	
	function SendCommand( $fields )
	{
		$fields['api_key'] = $this->m_api_key;
		$fields['product_id'] = $this->GetProductID();
		
		$url = "http://qody.co/connector/?".http_build_query( $fields );
		
		$response = file_get_contents( $url );
		
		return $response;
	}
}
?>