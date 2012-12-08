<?php
/**
 * File containing the ezcMvcMailTieinException class.
 *
 * @package MvcMailTiein
 * @version 1.0.1
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This class provides the base exception for exceptions in the MvcMailTiein component.
 *
 * @package MvcMailTiein
 * @version 1.0.1
 */
abstract class ezcMvcMailTieinException extends ezcBaseException
{
    /**
     * Constructs an ezcMvcMailTieinException
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
