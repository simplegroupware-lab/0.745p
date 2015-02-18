<?php

/**
 * Exception that is thrown if a filter is invalid
 *
 * @package csg
 * @version
 */
class
	csgFilterException extends csgException
	
{
 
    // Constructs a new csgFilterException with an optional $message
    function __construct( $message = null )
    {
		$messagePart = $message !== null ? " ($message)" : "";
		parent::__construct( "The filter operation is invalid.$messagePart" );
    }
} // class csgFilterException

?>