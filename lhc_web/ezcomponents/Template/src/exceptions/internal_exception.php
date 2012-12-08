<?php
/**
 * File containing the ezcTemplateInternalException class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcTemplateInternalException is thrown when the Template engine comes into
 * an unstable state.
 *
 * @package Template
 * @version 1.4.2
 */
class ezcTemplateInternalException extends ezcTemplateException
{
    /**
     * Creates a template internal exception.
     *
     * @param string $msg
     */
    public function __construct( $msg )
    {
        parent::__construct( "Internal error: $msg" );
    }
}
?>
