<?php

/*
 *	custom sgs filter class representing a filter
 *	a filter is a combination of an operator and a search string
 *  associated with a field name.
 *	valid operators are like,not_like,starts_with,equal,not_equal,less_than,greather_than,one_of
 */
class
	csgFilter

{
	
// creation

	public function __construct($field_name, $operator, $search_string) {
	
		if ($this-> is_valid_field_name($field_name) and
			$this-> is_valid_operator($operator)) {
			
			$this -> field_name = $field_name;
			$this -> operator = $operator;
			$this -> search_string = $search_string;

			} else {
			throw new csgFilterException ("filter is not valid");
		}
	}

// valid operators

	const like 			= "like";
	const not_like		= "nlike";
	const starts_with	= "starts";
	const equal			= "eq";
	const not_equal		= "neq";
	const less_than		= "lt";
	const greater_than	= "gt";
	const one_of		= "oneof";

// features


	// get the value of folder/view
	public function __get($name)
	{
		switch ($name)
		{
			case "field_name":
				return $this -> field_name;
			case "operator":
				return $this -> operator;
			case "search_string":
				return $this -> search_string;
			case "as_string":
				return	$this -> field_name . "|" .
						$this -> operator . "|" .
						$this -> search_string;
		}
		throw new csgPropertyNotFoundException ($name);
	}

	
// Implementation
	
	protected $field_name;
	protected $operator;
	protected $search_string;
	
	
	protected function is_valid_operator ($operator) {
		return in_array($operator,
			array(	self::like,self::not_like,
					self::starts_with,self::equal,
					self::not_equal,self::less_than,
					self::greater_than,self::one_of
				)
			);
	}
	
	protected function is_valid_field_name($name) {
		return !empty($name);
	}
}

?>