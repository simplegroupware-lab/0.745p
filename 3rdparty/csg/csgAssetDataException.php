<?php

/**
 * Exception that is thrown if AssetData is invalid
 *
 * @package csg
 * @version
 */
class
	csgAssetDataException extends csgException
	
{
 
    // Constructs a new csgAssetDataException with an optional $message
    function __construct( $message = null )
    {
		$messagePart = $message !== null ? " ($message)" : "";
		parent::__construct( "The asset data is invalid.$messagePart" );
    }
} // class csgAssetDataException

?>