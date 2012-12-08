<?php
/**
 * File containing the ezcSearchDoesNotProvideDefinitionException class.
 *
 * @package Search
 * @version 1.0.9
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This exception is thrown when the embedded manager can not find a definition
 * for a class because it doesn't implement the interface.
 *
 * @package Search
 * @version 1.0.9
 */
class ezcSearchDoesNotProvideDefinitionException extends ezcSearchException
{
    /**
     * Constructs an ezcSearchDoesNotProvideDefinitionException for document type $type
     *
     * @param string $type
     * @return void
     */
    public function __construct( $type )
    {
        $message = "The class '$type' does not implement the ezcSearchDefinitionProvider interface.";
        parent::__construct( $message );
    }
}
?>
