<?php
/**
 * @package Execution
 * @version 1.1.1
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Thrown when the Execution framework was already initialized.
 * 
 * @package Execution
 * @version 1.1.1
 */
class ezcExecutionAlreadyInitializedException extends ezcExecutionException
{
    /**
     * Constructs a new ezcExecutionAlreadyInitializedException.
     *
     * @return void
     */
    function __construct()
    {
        parent::__construct( "The Execution mechanism is already initialized." );
    }
}
?>
