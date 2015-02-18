<?php

/*
 *	abstract csg class,
 *  representing the csg framework
 */
 
abstract class
	csg

{

	
// features

	const default_script_directory = "../custom/ext/lib/";
	const default_resource_name = "csg";

	// process the resource defined by $r_name
	public static function process_session_request(
			$resource = self::default_resource_name,
			$script_directory = self::default_script_directory)
	{
		if (array_key_exists($resource, $_REQUEST))
		{
			self::$resource_name = $resource;
			self::$script_directory = $script_directory;
			$script = self::$script_directory.$_REQUEST[$resource].".php";
			require ($script);
		}
	}

	
	public static function get_script_directory()
	{
		return ((empty(self::$script_directory))
				? self::default_script_directory
				: self::$script_directory);
	}


	public static function get_resource_name()
	{
		return ((empty(self::$resource_name))
				? self::default_resource_name
				: self::$resource_name);
	}
	
	
// Implementation
	
	protected static $script_directory;
	protected static $resource_name;
	
} // Class csg	

?>