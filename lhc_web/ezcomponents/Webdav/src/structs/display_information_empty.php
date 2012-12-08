<?php
/**
 * File containing the ezcWebdavEmptyDisplayInformation struct.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Display information with no body.
 *
 * Used by {@link ezcWebdavTransport} to transport information on displaying a
 * response to the browser. This display information does not carry a body.
 *
 * @version 1.1.4
 * @package Webdav
 */
class ezcWebdavEmptyDisplayInformation extends ezcWebdavDisplayInformation
{
    /**
     * Response object to extract headers from.
     * 
     * @var ezcWebdavResponse
     */
    public $response;
    
    /**
     * Creates a new struct.
     * 
     * @param ezcWebdavResponse $response 
     * @return void
     */
    public function __construct( ezcWebdavResponse $response )
    {
        $this->response = $response;
    }
}

?>
