<?php

/**
 * Exception that is thrown if a Property is not found
 *
 * @package csg
 * @version
 */
class
	csgPropertyNotFoundException extends csgException
	
{
 
    // Constructs a new csgPropertyNotFoundException with the name of the property
    function __construct( $name )
    {
		parent::__construct( "No such property name '{$name}'." );
    }
} // class csgPropertyNotFoundException

?>