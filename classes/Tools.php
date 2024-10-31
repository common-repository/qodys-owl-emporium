<?php
class QodyTools extends QodyPlugin
{
	function __construct()
	{
		parent::__construct();
	}
	
	function close_dangling_tags($html)
	{
		#put all opened tags into an array
		preg_match_all("#<([a-z]+)( .*)?(?!/)>#iU",$html,$result);
		$openedtags=$result[1];
		
		#put all closed tags into an array
		preg_match_all("#</([a-z]+)>#iU",$html,$result);
		$closedtags=$result[1];
		$len_opened = count($openedtags);
		# all tags are closed
		if(count($closedtags) == $len_opened){
			return $html;
		}
		
		$openedtags = array_reverse($openedtags);
		# close tags
		for($i=0;$i < $len_opened;$i++) {
			if (!in_array($openedtags[$i],$closedtags)){
				$html .= '</'.$openedtags[$i].'>';
			} else {
				unset($closedtags[array_search($openedtags[$i],$closedtags)]);
			}
		}
		return $html;
	}
	
	function SafeSubstr($text, $length = 180)
	{ 
		if((mb_strlen($text) > $length)) { 
			$whitespaceposition = mb_strpos($text, ' ', $length) - 1; 
			if($whitespaceposition > 0) { 
				$chars = count_chars(mb_substr($text, 0, ($whitespaceposition + 1)), 1); 
				if ($chars[ord('<')] > $chars[ord('>')]) { 
					$whitespaceposition = mb_strpos($text, ">", $whitespaceposition) - 1; 
				} 
				$text = mb_substr($text, 0, ($whitespaceposition + 1)); 
			} 
			$text = str_replace( '<br / ', '<br>', $text ); 
			$text .= ' ...';
			
			$text = $this->close_dangling_tags( $text );
		}
		
		return $text; 
	}
	
	function Clean( $theString )
	{
		return str_replace( "\\", "", html_entity_decode($theString) );
	}
	
	function filter( $str )
	{
		if( is_array( $str ) )
			return;

		$str = addslashes( $str );
		$str = htmlentities( $str );
		$str = trim( $str );
		
		return $str;
	}
	
	function GetPreviousPage()
	{
		$url = $_SERVER['HTTP_REFERER'];
		
		if( !$url )
			$url = $this->m_url;
		
		return $url;
	}
	
	function StorePostedData()
	{
		if( $_POST )
		{
			$post_copy = $_POST;
			
			$this->ClearPostedData();
			
			foreach( $post_copy as $key => $value )
			{
				$_SESSION['post_data'][ $key ] = $value;
			}
		}
	}
	
	function ClearPostedData()
	{
		if( isset( $_SESSION['post_data'] ) )
			unset( $_SESSION['post_data'] );
	}
	
	function GetPostedData()
	{
		return $_SESSION['post_data'];
	}
	
	function ItemDebug( $data )
	{
		echo "<br>---------------- Start Debug ----------------<br>";
		echo "<pre>".print_r( $data, true )."</pre>";
		echo "----------------  End Debug  ----------------<br>";
	}
	
	function Encrypt( $data, $type = 'base64' )
	{
		switch( $type )
		{
			case 'base64':
				$result = base64_encode( $data );
				break;
			
			case 'rsa':
				$keys = $this->GetClass('rsa')->generate_keys( '3754241', '3782059' ); 

				$result = $this->GetClass('rsa')->encrypt( $data, $keys[1], $keys[0], 5 );
				break;
			
			case 'rsa_base64':
				$result = $this->Encrypt( $this->Encrypt( $data, 'rsa' ), 'base64' );
				break;
			
			case 'mcrypt':
				$key = "kitty";
				$iv_size =  mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
				$iv = mcrypt_create_iv( $iv_size, MCRYPT_RAND );
				
				$string = trim($data);
				
				$result = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $string, MCRYPT_MODE_ECB, $iv);
				break;
			
			case 'mcrypt_base64':
				$result = $this->Encrypt( $this->Encrypt( $data, 'mcrypt' ), 'base64' );
				break;
		}
		
		return $result;
	}
	
	function Decrypt( $data, $type = 'base64' )
	{
		switch( $type )
		{
			case 'base64':
				$result = base64_decode( $data );
				break;
			
			case 'rsa':
				$keys = $this->GetClass('rsa')->generate_keys( '3754241', '3782059' );

				$result = $this->GetClass('rsa')->decrypt( $data, $keys[2], $keys[0] );
				break;
			
			case 'rsa_base64':
				$result = $this->Decrypt( $this->Decrypt( $data, 'base64' ), 'rsa' );
				break;
			
			case 'mcrypt':
				$key = "kitty";
				$key = trim($key);
				$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
				$iv = mcrypt_create_iv( $iv_size, MCRYPT_RAND );
			
				$result = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256,$key,$data,MCRYPT_MODE_ECB,$iv));
				break;
			
			case 'mcrypt_base64':
				$result = $this->Decrypt( $this->Decrypt( $data, 'base64' ), 'mcrypt' );
				break;
		}
		
		return $result;
	}
	
	function AddThisBlogToSiteTracker()
	{
		// Notify Qody of your main site
		$keyword = strtolower( $this->get_option( 'keyword_to_track') );
		
		$this->ConnectWithSiteTracker( $keyword, 'enable' );
	}
	
	function Encode( $stuff )
	{
		$encoded = serialize( $stuff );
		$encoded = $this->filter( $encoded );
		
		return $encoded;
	}
	
	function Decode( $stuff )
	{
		$stuff = $this->Clean( $stuff );
		$decoded = unserialize( html_entity_decode($stuff) );		
		
		return $decoded;
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
	
	function MakeSlug( $slug )
	{
		$slug = str_replace( ' ', '_', $slug );
		$slug = strtolower( $slug );
		
		return $slug;
	}
	
	function GetFromSlug( $slug )
	{
		$slug = str_replace( '_', ' ', $slug );
		$slug = ucwords( $slug );
		
		return $slug;
	}
	
	function xmlstr_to_array($xmlstr) {
		$doc = new DOMDocument();
		$doc->loadXML($xmlstr);
		
		return $this->domnode_to_array($doc->documentElement);
	}
	
	function domnode_to_array($node)
	{
		$output = array();
		switch ($node->nodeType)
		{
			case XML_CDATA_SECTION_NODE:
			case XML_TEXT_NODE:
				$output = trim($node->textContent);
				break;
			
			case XML_ELEMENT_NODE:
			for ($i=0, $m=$node->childNodes->length; $i<$m; $i++)
			{
				$child = $node->childNodes->item($i);
				$v = Qody::domnode_to_array($child);
				
				if(isset($child->tagName))
				{
					$t = $child->tagName;
					if(!isset($output[$t]))
					{
						$output[$t] = array();
					}
					$output[$t][] = $v;
				}
				elseif($v)
				{
					$output = (string) $v;
				}
			}
			if(is_array($output))
			{
				if($node->attributes->length)
				{
					$a = array();
					foreach($node->attributes as $attrName => $attrNode)
					{
						$a[$attrName] = (string) $attrNode->value;
					}
					$output['@attributes'] = $a;
				}
				foreach ($output as $t => $v)
				{
					if(is_array($v) && count($v)==1 && $t!='@attributes')
					{
						$output[$t] = $v[0];
					}
				}
			}
			break;
		}
		return $output;
	}
	
	function NumberTimeToStringTime( $theTime, $styled = 'strong', $spotsWanted = 1 )
	{
		$oneSecond = 1;
		$oneMinute = $oneSecond * 60;
		$oneHour = $oneMinute * 60;
		$oneDay = $oneHour * 24;
		
		if( $styled && $styled != 'none' )
		{
			$start = '<'.$styled.'>';
			$end = '</'.$styled.'>';
		}
		
		$timeLeft = $theTime;
		$runningTotal = "";
		$daysLeft = (int)($timeLeft / $oneDay);
		$timeLeft -= $daysLeft * $oneDay;
		$hoursLeft = (int)($timeLeft / $oneHour);
		$timeLeft -= $hoursLeft * $oneHour;
		$minutesLeft = (int)($timeLeft / $oneMinute);
		$timeLeft -= $minutesLeft * $oneMinute;
		$secondsLeft = (int)($timeLeft / $oneSecond);
		
		$spotsTaken = 0;
		
		if( $daysLeft > 0 )
		{
			$spotsTaken++;
			$runningTotal .= ' ';
			
			$runningTotal .= $start.$daysLeft.$end." day";
			if( $daysLeft > 1 )
				$runningTotal .= "s";
		}
		
		if( $hoursLeft > 0 && $spotsTaken < $spotsWanted )
		{
			$spotsTaken++;
			$runningTotal .= ' ';
			
			$runningTotal .= $start.$hoursLeft.$end." hour";
			if( $hoursLeft > 1 )
				$runningTotal .= "s";
		}
		
		if( $minutesLeft > 0 && $spotsTaken < $spotsWanted )
		{
			$spotsTaken++;
			$runningTotal .= ' ';
			
			$runningTotal .= $start.$minutesLeft.$end." minute";
			if( $minutesLeft > 1 )
				$runningTotal .= "s";
		}
		
		if( $secondsLeft > 0 && $spotsTaken < $spotsWanted )
		{
			$spotsTaken++;
			$runningTotal .= ' ';
			
			$runningTotal .= $start.$secondsLeft.$end." second";
			if( $secondsLeft > 1 )
				$runningTotal .= "s";
		}
		
		if( $spotsTaken == 0 )
		{
			$runningTotal = "no time";
		}
		
		return trim( $runningTotal );
	}
	
	function getRealIP()
	{
		if( $_SERVER['HTTP_X_FORWARDED_FOR'] != '' )
		{
			$client_ip =
			( !empty($_SERVER['REMOTE_ADDR']) ) ?
			$_SERVER['REMOTE_ADDR']
			:
			( ( !empty($_ENV['REMOTE_ADDR']) ) ?
			$_ENV['REMOTE_ADDR']
			:
			"unknown" );
		
			// Proxies are added at the end of this header
			// Ip addresses that are "hiding". To locate the actual IP
			// User begins to look for the beginning to find
			// Ip address range that is not private. If not
			// Found none is taken as the value REMOTE_ADDR

			$entries = split('[, ]', $_SERVER['HTTP_X_FORWARDED_FOR']);

			reset($entries);
			while (list(, $entry) = each($entries))
			{
				$entry = trim($entry);
				if ( preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list) )
				{
					// http://www.faqs.org/rfcs/rfc1918.html
					$private_ip = array(
					'/^0\./',
					'/^127\.0\.0\.1/',
					'/^192\.168\..*/',
					'/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/',
					'/^10\..*/');

					$found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);

					if ($client_ip != $found_ip)
					{
						$client_ip = $found_ip;
						break;
					}
				}
			}
		}
		else
		{
			$client_ip =
			( !empty($_SERVER['REMOTE_ADDR']) ) ?
			$_SERVER['REMOTE_ADDR']
			:
			( ( !empty($_ENV['REMOTE_ADDR']) ) ?
			$_ENV['REMOTE_ADDR']
			:
			"unknown" );
		}

		return $client_ip;
	}
}
?>