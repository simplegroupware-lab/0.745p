<?php

/*
 *	custom sgs folder class representing a folder
 */
class 
	csgFolder

{

// creation 

	public function __construct( csgSchema $schema,
			$autorefresh = true,
			$activate_filter = false)
	{

		$this -> schema = $schema;
		$this -> view = new csgView ($this, $activate_filter);
		$this -> autorefresh = $autorefresh;
		$this -> assets = array();
		
		if ($this -> autorefresh)
		{
			$this -> refresh();
		}
	}

// features

	public function __get($name)
	{
		switch ($name)
		{
			case "view":
				return $this->view;
			case "assets":
				return $this->assets;
			case "schema":
				return $this->schema;
		}
		throw new csgPropertyNotFoundException ($name);
	}
	
	// refreshes the list of assets in the view
	public function refresh()
	{

		if (!empty( $this -> assets_index ))
		{
			$this -> index_asset_array_from_db( $this->assets_index );
		}
		else
		{
			$this -> index_asset_array_from_db("id");
		}
	}
	

	// returns autorefresh mode
	public function is_autorefresh()
	{
		return $this -> autorefresh;
	}

	
	// set autorefresh mode
	public function set_autorefresh($mode = true)
	{
		$this -> autorefresh = $mode;
	}
	


// asset features


	// returns an asset object with id
	public function asset($id)
	{
		return csgAsset::get_from_db($this -> schema,$id);
	}


	// returns an array of assets
	// indexed by field_name in default order
	// index field must be unique
	public function asset_array( $index_field = null )
	{
		if ( $this -> autorefresh)
		{
			$this -> index_asset_array_from_db($index_field);
		}
		else
		{
			$this -> reindex_asset_array( $index_field );
		}
			
		return $this -> assets;
	}
	

	// returns an array of assets
	// indexed by ascending field_name
	public function asset_array_asc( $index_field )
	{
		if ( $this -> autorefresh)
		{
			$this -> index_asset_array_from_db( $index_field );
		}
		else
		{
			$this->reindex_asset_array( $index_field );
		}
		ksort($this -> assets);
		return $this -> assets;
	}


	// returns an array of assets
	// indexed by descending field_name
	public function asset_array_desc( $index_field )
	{
		if ( $this -> autorefresh)
		{
			$this -> index_asset_array_from_db( $index_field );
		}
		else
		{
			$this -> reindex_asset_array( $index_field );
		}
		krsort($this -> assets);
		return $this -> assets;
	}
	


	// TODO moves an asset to trash
	public function delete_asset($id) {
		ajax::asset_delete(
				$this -> schema -> folder_id,
				$this -> schema ->view_name,
				array($id),
				"delete");
	}

	
	// TODO moves assets to trash
	public function delete_assets( array $ids )
	{
		ajax::asset_delete(
				$this -> schema -> folder_id,
				$this -> schema -> view_name,
				$ids,
				"delete");
	}

	
	// TODO move all assets in the view to trash
	public function delete_all_assets() {
	}

	
	// TODO delete assets from database
	public function purge_asset($id) {
	}

	
	// TODO delete all assets from database
	public function purge_all_assets() {
	}

	
	// TODO moves an asset to target folder
	public function cut_paste_asset($id, $target_folder) {
	}

	
	// TODO copy an asset to target folder
	public function copy_paste_asset($id, $target_folder) {
	}

	
// Implementation
	
	protected $schema;
	protected $view;

	protected $assets;

	protected $assets_index;
	protected $autorefresh;
	
	
	// load assets from database
	protected function assets_from_db($limit = 100,array $ids=array())
	{
		return ajax::asset_get_rows(
			$this->schema->folder_id,
			$this->schema->view_name,
			$fields = '*', 
			$order = '', 
			$limit, 
			$items = $ids,
			$this -> view -> filters_as_string()
		);
	}

	// build asset array from db row indexed by $index_field
	protected function index_asset_array_from_db($index_field)
	{

		$rows = $this -> assets_from_db();
		$this -> assets = array();

		if (count($rows)>0)
		{
			// is field visible in the active view
			$exists_field = in_array( $index_field, $this -> schema -> field_names );

			// build array of assets with index_field as index
			foreach ($rows as $row) {
		
				$asset = new csgAsset ($this -> schema, new csgAssetData( $row ) );

				if ($exists_field) {
					$this -> assets[$row[$index_field]] = $asset;
				} else {
					$this -> assets[] = $asset;
				}
			}
			if ($exists_field) {
				$this -> assets_index = $index_field;
			} else {
				$this -> assets_index = null;
			}
		}

	}

	// sort current asset array
	protected function reindex_asset_array( $index_field ) {
		
		if ( in_array($index_field, $this -> schema -> field_names) )
		{
			$reindexed_assets = array();
			foreach ($this -> assets as $asset) {
				$reindexed_assets[$asset->__get($index_field)] = $asset;
			}
			$this -> assets = $reindexed_assets;
			$this -> assets_index = $index_field;
		}
		
	}

	// folder id must not be empty
	protected function is_valid_folder_id($id) {
		return !empty($id);
	}

} // class csgFolder

?>
