<?php

/*
 * custom csg asset class representing an asset
 * a csg asset must be attached to a folder/view
 * to be able to interact with the sgs database
 * also some operations need an id for correct identification
 */
class
	csgAsset extends csgAssetData
{

// create

	// folder = ID or string (/Workspace/.../), view = view name
	public function __construct(csgSchema $schema, csgAssetData $data = null)
	{
		$this-> schema = $schema;
		parent::__construct(array());

		if (!empty($data))
		{
			$this->fields = $data->fields;
		}
	}

	
// static features

	// initialize from db
	public static function get_from_db(csgSchema $schema, $id)
	{
		// creates an asset data object
		$data = self::asset_from_db($schema, $id);
	
		if (!empty($data)) {
			return new csgAsset($schema, $data);
		}
		else
		{
			throw new csgAssetException ("id is not valid");
		}
	}

	
// features: asset attributes


	// set the value of a field
	public function __set($name, $value)
	{
		switch ($name) {
			case self::folder_name:
				throw new csgAssetException ("folder cannot be set manually");
				break;
			case self::id_name:
				throw new csgAssetException ("id cannot be set manually");
				break;
			case self::view_name:
				throw new csgAssetException ("view cannot be set manually");
				break;
			default:
				parent::__set($name, $value);
		}
	}


// features: folder related operations	

	// returns true if last operations was successful, false otherwise
	public function is_success_last_operation()
	{
		return !is_array($this->error_msg);
	}
	
	// returns the error message
	public function error_msg_last_operation()
	{
		return $this->error_msg;
	}

	// inserts a new asset into the folder
	// creates a new id
	public function insert_as_new()
	{
		$this->reset_error_msg();
		$result = ajax::asset_insert(
						$this->schema->folder_id,
						$this->schema->view_name,
						$this->fields
		);
		if (is_int($result)) {
			$this->id = $result;
		} else {
			$this->set_error_msg($result);
		}
	}

	// updates asset in the folder
	// needs a valid id
	public function update()
	{
		$this->reset_error_msg();
		$result = ajax::asset_update(
						$this->schema->folder_id,
						$this->schema->view_name,
						$this->fields,
						$this->id
		);
		if (!is_int($result)) {
			$this->set_error_msg($result);
		}	
	}

	// validates the asset
	// with or without valid id
	public function validate()
	{
		$this->reset_error_msg();
		$id = ($this->id > 0) ? $this->id : -1;
		$result = ajax::asset_validate(
						$this->schema->folder_id,
						$this->schema->view_name,
						$this->fields,
						$id
		);
		if (count($result)>0) {
			$this->set_error_msg($result);
		}	
	}

	// moves asset from folder to trash
	// needs id
	public function delete()
	{
		$this->reset_error_msg();
		$result = ajax::asset_delete(
						$this -> schema -> folder_id,
						$this -> schema -> view_name,
						$this -> id,
						$mode = "delete"
		);	
	}

	// deletes asset from db
	public function purge()
	{
		$this->reset_error_msg();
		$result = ajax::asset_delete(
						$this -> schema -> folder_id,
						$this -> schema -> view_name,
						$this -> id,
						$mode = "purge"
		);	
	}

	// move to target folder
	public function move($target_folder)
	{
		$this->reset_error_msg();
		ajax::asset_ccp($this -> schema -> folder_id,
						$this -> schema -> view_name,
						$this -> id,
						$target_folder,
						$operation = "cut"
		);	
	}

	// copy to target folder
	public function copy($target_folder)
	{
		$this->reset_error_msg();
		ajax::asset_ccp($this -> schema -> folder_id,
						$this -> schema -> view_name,
						$this -> id,
						$target_folder,
						$operation = "copy"
		);	
	}

// Implementation

	const folder_name	= "folder";
	const id_name		= "id";
	const view_name		= "view";
	
	protected $schema;
	protected $error_msg;
	
	// returns a csgAssetData object loaded from from database
	protected static function asset_from_db($schema, $id)
	{
		$rows =
			ajax::asset_get_rows(
				$schema -> folder_id,
				$schema -> view_name,
				$fields = '*',
				$order = '',
				$limit = 1,
				$items = array($id)
			);
		if (count($rows)==1)
		{
			return new csgAssetData(current($rows));
		}
		else
		{
			return null;
		}
	}

	protected function set_error_msg($msg)
	{
		$this->error_msg = $msg;
	}

	protected function reset_error_msg()
	{
		$this->error_msg = null;
	}
	
} // class csgAsset

?>