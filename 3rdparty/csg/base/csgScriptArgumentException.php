<?php

/**
 * Exception that is thrown if a script argument is not valid
 *
 * @package csg
 * @version
 */
class
	csgScriptArgumentException extends csgException
	
{
 
    // Constructs a new csgScriptArgumentException with the name of the argument
    function __construct( $name )
    {
		parent::__construct( "script argument is not valid '{$name}'." );
    }
} // class csgScriptArgumentException

?>