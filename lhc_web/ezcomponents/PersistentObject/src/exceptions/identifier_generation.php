<?php
/**
 * File containing the ezcPersistentIdentifierGenerationException class
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown when generating an ID for a persistent object failed.
 *
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentIdentifierGenerationException extends ezcPersistentObjectException
{

    /**
     * Constructs a new ezcPersistentIdentifierGenerationException for the class
     * $class with the optional message $msg.
     *
     * @param string $class
     * @param string $msg
     * @return void
     */
    public function __construct( $class, $msg = null )
    {
        $info = "Could not create an identifier for the object of type '$class'.";
        if ( $info != null )
        {
            $info .= " $msg";
        }
        parent::__construct( $info );
    }
}
?>
