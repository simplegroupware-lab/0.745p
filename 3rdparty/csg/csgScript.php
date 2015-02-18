<?php

/*
 *	abstract csg Script class, representing the script 
 */
 
abstract class
	csgScript

{

	
// features

	
	public static function exists_argument($name)
	{
		return csgScriptArgument::exists($name);
	}

	public static function get_argument($name)
	{
		return csgScriptArgument::get($name);
	}
	
	public static function get_arguments()
	{
		return csgScriptArgument::arguments();
	}
	
// Implementation
	
	
} // Class csgScript	

?>