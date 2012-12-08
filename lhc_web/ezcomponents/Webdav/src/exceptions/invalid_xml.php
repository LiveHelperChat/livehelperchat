<?php
/**
 * File containing the ezcWebdavInvalidXmlException class.
 * 
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Thrown if an error occured while loading an XML string.
 * 
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavInvalidXmlException extends ezcWebdavBadRequestException
{
    /**
     * Initializes the exception with the given $reason.
     * 
     * @param string $reason 
     * @return void
     */
    public function __construct( $reason )
    {
        parent::__construct(
            'Invalid XML. ' . $reason
        );
    }
}

?>
