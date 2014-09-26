<?php
/**
 * @package Execution
 * @version 1.1.1
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Thrown when the Execution framework was not initialized when cleanExit()
 * was called.
 * 
 * @package Execution
 * @version 1.1.1
 */
class ezcExecutionNotInitializedException extends ezcExecutionException
{
    /**
     * Constructs a new ezcExecutionNotInitializedException.
     *
     * @return void
     */
    function __construct()
    {
        parent::__construct( "The Execution mechanism was not initialized." );
    }
}
?>
