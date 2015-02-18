<?php

/**
 * Exception that is thrown if a view is invalid
 *
 * @package csg
 * @version
 */
class
	csgViewException extends csgException
	
{
 
    // Constructs a new csgViewException with an optional $message
    function __construct( $message = null )
    {
		$messagePart = $message !== null ? " ($message)" : "";
		parent::__construct( "The view operation is invalid.$messagePart" );
    }
} // class csgViewException

?>