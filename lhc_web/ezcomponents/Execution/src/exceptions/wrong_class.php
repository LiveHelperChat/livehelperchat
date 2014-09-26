<?php
/**
 * @package Execution
 * @version 1.1.1
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Thrown when the passed classname does not represent a class that
 * implements the ezcExecutionErrorHandler interface.
 * 
 * @package Execution
 * @version 1.1.1
 */
class ezcExecutionWrongClassException extends ezcExecutionException
{
    /**
     * Constructs a new ezcExecutionWrongClassException.
     *
     * @param string $callbackClassName
     * @return void
     */
    function __construct( $callbackClassName )
    {
        parent::__construct( "The class '{$callbackClassName}' does not implement the 'ezcExecutionErrorHandler' interface." );
    }
}
?>
