<?php
/**
 * File containing the ezcWebdavStringDisplayInformation class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Display information with string body.
 *
 * Used by {@link ezcWebdavTransport} to transport information on displaying a
 * response to the browser. This display information carries a string body.
 *
 * @version 1.1.4
 * @package Webdav
 */
class ezcWebdavStringDisplayInformation extends ezcWebdavDisplayInformation
{
    /**
     * Response object to extract headers from.
     * 
     * @var ezcWebdavResponse
     */
    public $response;

    /**
     * Representation of the response body.
     * 
     * @var string
     */
    public $body;
    
    /**
     * Creates a new display information object.
     *
     * This display information must be created with a string $body.
     *
     * @param ezcWebdavResponse $response 
     * @param string $body 
     * @return void
     */
    public function __construct( ezcWebdavResponse $response, $body )
    {
        $this->response = $response;
        $this->body     = $body;
    }
}

?>
