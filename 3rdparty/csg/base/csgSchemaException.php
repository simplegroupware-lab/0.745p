<?php

/**
 * Exception that is thrown if Schema is invalid
 *
 * @package csg
 * @version
 */
class
	csgSchemaException extends csgException
	
{
 
    // Constructs a new csgSchemaException with an optional $message
    function __construct( $message = null )
    {
		$messagePart = $message !== null ? " ($message)" : "";
		parent::__construct( "The schema is invalid.$messagePart" );
    }
} // class csgSchemaException

?>