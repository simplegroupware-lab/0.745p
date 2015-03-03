<?php

/**
 * csgException is a container from which all
 * other exceptions descent.
 *
 * @package csg
 * @version
 */
abstract class
	csgException extends Exception

{
    // Original message, before escaping
    public $originalMessage;
 
    // Constructs a new csgException with $message
    public function __construct( $message )
    {
        $this->originalMessage = $message;
 
        if ( php_sapi_name() == 'cli' )
        {
            parent::__construct( $message );
        }
        else
        {
            parent::__construct( htmlspecialchars( $message ) );
        }
    }
} // class csgException

?>