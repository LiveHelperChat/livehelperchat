<?php
/**
 * File containing the ezcWebdavInvalidRequestMethodException class.
 * 
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Thrown if an unknwon request method is received.
 * 
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavInvalidRequestMethodException extends ezcWebdavException
{
    /**
     * Initializes the exception with the given $method and sets the exception
     * message from it.
     * 
     * @param mixed $method 
     * @return void
     */
    public function __construct( $method )
    {
        parent::__construct( "The HTTP request method '$method' was not understood." );
    }
}

?>
