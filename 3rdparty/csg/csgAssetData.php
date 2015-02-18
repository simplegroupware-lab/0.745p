<?php

/*
 *	custom sgs asset data class representing the data part of an asset
 *  valid properties are each field, field_names and fields
 */
class
	csgAssetData
{

// create

	// a field is an array of the form ([field_name] => value)
	public function __construct(array $fields = array())
	{
		$this -> fields = array();
		array_walk($fields, array($this,'set_field_by_name'));
	}

	
// features

	// adds a new field
	public function add_field ($name, $value)
	{	
		// tests for valid parameters
		if (empty($name))
		{
			throw new csgAssetDataException ("field name is not valid");
		}

		// test for duplicate fields
		if ($this->exists_field($name))
		{
			throw new csgAssetDataException ("duplicate fields are not allowed");
		}
		
		$this->fields[$name] = $value;
	}
	
	
	// removes a field
	public function remove_field($name)
	{
		if ($this->exists_field($name))
		{
			unset($this->fields[$name]);
		}
	}


	// boolean: exists field name
	public function exists_field($name)
	{
		return in_array($name,array_keys($this->fields));
	}
	
	// set the value of a field
	public function __set($name, $value)
	{
		if ($this->exists_field($name))
		{
			$this->fields[$name] = $value;
		} else {
			throw new csgPropertyNotFoundException ($name);
		}
	}

	// get the value of fields
	// and 'field_names' and 'fields'
	public function __get($name)
	{
		if ($this->exists_field($name))
		{
			return $this->fields[$name];
		}
		else
		{
			switch ($name)
			{
				case "field_names":
					return array_keys($this->fields);
				case "fields":
					return $this->fields;
				default:
				throw new csgPropertyNotFoundException ($name);
			}
		}
	}

	
// Implementation
	
	// an asset_data is an array of the form [field_name => value]
	protected $fields;
	
	protected function set_field_by_name($value, $field_name) {
		$this->fields[$field_name] = $value;
	}
	
} // class csgAssetData

?>