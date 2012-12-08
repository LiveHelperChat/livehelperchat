<?php
/**
 * File containing the ezcSearchDefinitionNotFoundException class.
 *
 * @package Search
 * @version 1.0.9
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This exception is thrown when a definition file for a class can not be found.
 *
 * @package Search
 * @version 1.0.9
 */
class ezcSearchDefinitionNotFoundException extends ezcSearchException
{
    /**
     * Constructs an ezcSearchDefinitionNotFoundException
     *
     * @param string $type
     * @param string $class
     * @param string $location
     * @return void
     */
    public function __construct( $type, $class, $location )
    {
        $message = "Could not find the $type definition file for '$class' at '$location'.";
        parent::__construct( $message );
    }
}
?>
