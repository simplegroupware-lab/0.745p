<?php

/*
 *	custom csg script arguments class 
 */
 
class
	csgScriptArgument

{


// static features

	// get a new argument object
	public static function get($name)
	{
		return new csgScriptArgument($name);
	}

	// returns a list of arguments
	public static function __callStatic($name, $arguments)
	{
		if ($name =="arguments")
		{
			$names = array_keys( $_REQUEST );
			foreach ($names as $name)
			{
				$args[$name] = new csgScriptArgument($name);
			}
			return $args;
		}
		elseif (self::exists($name))
		{
			return $_REQUEST[$name];
		}
		throw new csgScriptArgumentException ($name);
	}

	
	public static function exists($name)
	{
		return array_key_exists($name, $_REQUEST);
	}

	
// features

	// get the name/value of the argument
	public function __get($property)
	{
		switch ($property)
		{
			case "name":
				return $this -> name;
			case "value":
				return $this -> value;
		}
		throw new csgPropertyNotFoundException ($name);
	}


// Implementation

	protected $name;
	protected $value;

	protected function __construct( $name )
	{
		if (self::exists($name))
		{
			$this -> name = $name;
			$this -> value = $_REQUEST[$name];
		}
		else
		{
			throw new csgScriptArgumentException ($name);
		}
	}
	

} // Class csgScriptArgument	

?>