<?php
/**
 * File containing the ezcMvcToolsException class.
 *
 * @package MvcTools
 * @version 1.1.3
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This class provides the base exception for exception in the MvcTools component.
 *
 * @package MvcTools
 * @version 1.1.3
 */
abstract class ezcMvcToolsException extends ezcBaseException
{
    /**
     * Constructs an ezcMvcToolsException
     *
     * @param string $message
     * @return void
     */
    public function __construct( $message )
    {
        parent::__construct( $message );
    }
}
?>
