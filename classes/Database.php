<?php
class QodyDatabase extends QodyPlugin
{
	function __construct()
	{
		parent::__construct();
	}

	function GetFromDatabase( $table, $field = '', $value = '', $single = false )
	{
		global $wpdb;
		
		$value = $wpdb->escape( $value );
		
		$query = "SELECT * FROM ".$wpdb->pre.$table;
		
		if( $field && $value )
			$query .= " WHERE {$field} = '{$value}'";
		
		$results = $wpdb->get_results( $query, ARRAY_A);
		
		if( $single )
			$results = $results[0];
			
		return $results;
	}
	
	function DeleteFromDatabase( $table, $field, $value )
	{
		global $wpdb;
		
		$value = $wpdb->escape( $value );
		
		$wpdb->query( "DELETE FROM ".$wpdb->pre.$table." WHERE {$field} = '{$value}'" );
	}
	
	function UpdateDatabase( $fields, $table, $id, $option = 'id' )
	{
		global $wpdb;
		
		$first = '';
		$looper = 0;
		
		foreach( $fields as $key => $value )
		{
			$looper++;
			
			if( $looper == 1 )
				$first .= $wpdb->escape( $key )." = '".$wpdb->escape( $value )."' ";
			else
				$first .= ",".$wpdb->escape( $key )." = '".$wpdb->escape( $value )."' ";
		}
		
		$wpdb->query( "UPDATE ".$wpdb->pre.$table." SET ".$first." WHERE {$option} = '".$wpdb->escape( $id )."'" );
	}
	
	function InsertToDatabase( $fields, $table )
	{
		global $wpdb;

		$first = '';
		$second = '';
		$looper = 0;
		
		$bits = explode( '.', $table );
		
		if( count($bits) > 1 )
		{
			$table = '`'.$bits[0].'` . `'.$bits[1].'`';
		}
		else
		{
			$table = '`'.$table.'`';
		}
			
		foreach( $fields as $key => $value )
		{
			$looper++;
			
			if( $looper == 1 )
				$first .= "`".$wpdb->escape( $key )."`";
			else
				$first .= ",`".$wpdb->escape( $key )."`";
				
			if( $looper == 1 )
				$second .= "'".$wpdb->escape( $value )."'";
			else
				$second .= ",'".$wpdb->escape( $value )."'";			
		}

		$wpdb->query( "INSERT INTO ".$wpdb->pre.$table." (".$first.") VALUES (".$second.")" );
	}
}
?>