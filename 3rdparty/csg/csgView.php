<?php

/*
 *	custom sgs view class representing a view
 */
class
	csgView

{

// creation

	public function __construct(csgSchema $schema, $activate_filter = false)
	{
		$this -> schema = $schema;
		$this -> filter_is_activated = $activate_filter;
		$this -> filters = array();
	}

// features

	// adds a new filter clause to the view
	public function add_filter(csgFilter $filter, $name = null)
	{
		if (isset($filter) and
			(in_array($filter->field_name,$this -> schema -> field_names)))
		{
			if (empty($name))
			{
				$this -> filters[] = $filter;
			} else {
				$this -> filters[$name] = $filter;
			}
		}
		else
		{
			throw new csgViewException ("filter is not valid");
		}
	}


	// removes a filter
	public function remove_filter ($name)
	{
		if (!empty($name) and array_key_exists( $name, $this -> filters ))
		{
			unset($this -> filters[$name]);
		}
	}
	
	
	// resets filter to none
	public function reset_filter() {
		$this->filters = array();
	}


	// activate filter
	public function set_filter_on() {
		$this -> filter_is_activated = true;
	}

	
	// deactivate filter
	public function set_filter_off() {
		$this -> filter_is_activated = false;
	}


	// get the value of folder/view
	public function __get($name)
	{
		switch ($name)
		{
			case "field_names":
				return $this -> schema -> field_names;
			case "schema":
				return $this -> schema;
			case "filters":
				return $this -> filters;
			case "filter_is_activated":
				return $this -> filter_is_activated;
		}
		throw new csgPropertyNotFoundException ($name);
	}

	// no direct setting of property is allowed
	public function __set($name, $value)
	{
		throw new csgViewException ("operation not valid");
	}

	// returns filters as as string
	public function filters_as_string()
	{
		$string = "";
		if ( count($this -> filters)> 0)
		{
			foreach ($this -> filters as $filter)
			{
				$string = $string . "||" . $filter -> as_string;
			}
			$string = ltrim($string,"|");
		}
		return $string;
	}
	
// Implementation

	protected $schema;
	protected $filter_is_activated;
	
	protected $filters;


} // class csgView

?>
