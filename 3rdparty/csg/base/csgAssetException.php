<?php

/**
 * Exception that is thrown if Asset is invalid
 *
 * @package csg
 * @version
 */
class
	csgAssetException extends csgException
	
{
 
    // Constructs a new csgAssetException with an optional $message
    function __construct( $message = null )
    {
		$messagePart = $message !== null ? " ($message)" : "";
		parent::__construct( "The asset operation is invalid.$messagePart" );
    }
} // class csgAssetException

?>