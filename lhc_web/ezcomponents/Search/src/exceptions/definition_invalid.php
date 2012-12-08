<?php
/**
 * File containing the ezcSearchDefinitionInvalidException class.
 *
 * @package Search
 * @version 1.0.9
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This exception is thrown when a definition file for a class is invalid.
 *
 * @package Search
 * @version 1.0.9
 */
class ezcSearchDefinitionInvalidException extends ezcSearchException
{
    /**
     * Constructs an ezcSearchDefinitionInvalidException
     *
     * @param string $type
     * @param string $class
     * @param string $location
     * @param string $extraMsg
     * @return void
     */
    public function __construct( $type, $class, $location, $extraMsg = null )
    {
        if ( $extraMsg )
        {
            $extraMsg = " ($extraMsg)";
        }
        $locationPart = $location ? " at '$location'" : '';
        $message = "The $type definition file for '$class'$locationPart is invalid{$extraMsg}.";
        parent::__construct( $message );
    }
}
?>
