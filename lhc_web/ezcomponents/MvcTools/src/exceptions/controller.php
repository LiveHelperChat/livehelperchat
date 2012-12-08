<?php
/**
 * File containing the ezcMvcControllerException class.
 *
 * @package MvcTools
 * @version 1.1.3
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This exception is thrown when an error in a controller occurs.
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcControllerException extends ezcMvcToolsException
{
    /**
     * Constructs an ezcMvcControllerException with $message
     *
     * @param string $message
     */
    public function __construct( $message )
    {
        parent::__construct( $message );
    }
}
?>
