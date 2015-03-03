<?php

/*
 *	custom sgs schema class representing a schema
 *	a schema is defined by a folder and a view
 *  sgs needs this for many asset/folder operations
 *  the folder id can be an integer id or a string (eg. '/Workspace/.../')
 */
class
	csgSchema

{
	
// create

	// the folder id can be an integer id or a string (eg. '/Workspace/.../')
	public function __construct($folder_id, $view_name = "display")
	{

		// validation of folder id
		if (!$this->is_valid_folder_id($folder_id))
		{
			throw new csgSchemaException ("folder identification is not valid");
		}

		// validation of view name
		if (!$this->is_valid_view_name($view_name))
		{
			throw new csgSchemaException ("view name is not valid");
		}

		$this -> folder_id = $folder_id;
		$this -> view_name = $view_name;

	}


// features

	// get a new folder handler
	public function get_folder (
			$autorefresh = true,
			$activate_filter = false )
	{
		return new csgFolder ($this , $autorefresh, $activate_filter);
	}
	
	
	// get the value of folder/view
	public function __get($name)
	{
		switch ($name)
		{
			case "folder_id":
				return $this->folder_id;
			case "view_name":
				return $this->view_name;
			case "field_names":
				if ( empty($this->field_names) )
				{
					$this->load_field_names_from_db();	
				}
				return $this->field_names;
		}
		throw new csgPropertyNotFoundException ($name);
	}
	
	// no setting of property is allowed
	public function __set($name, $value)
	{
		throw new csgPropertyNotFoundException ($name);
	}
	

	// set field names manually.
	// only use if available field names cannot be retrieved automatically
	// eg. when folder is empty.
	public function set_field_names (array $field_names)
	{
		$this -> field_names = $field_names;
	}
	
// Implementation

	protected $folder_id;
	protected $view_name;
	protected $field_names;

	
	// folder id must not be empty
	protected function is_valid_folder_id($id)
	{
		return !empty($id);
	}
	

	// view names must not be empty
	protected function is_valid_view_name($name) {
		return !empty($name);
	}


	// load field names from db
	protected function load_field_names_from_db()
	{
		$rows =
			ajax::asset_get_rows(
				$this->folder_id,
				$this->view_name,
				$fields = '*', 
				$order = '',
				$limit = 1
			);
		if (count($rows)==1)
		{
			$asset = new csgAssetData(current($rows));
			$this->field_names = $asset -> field_names;
		}
	}

} // class csgSchema

?>