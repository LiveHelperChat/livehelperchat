<?php
/**
 * File containing the ezcWebdavXmlDisplayInformation struct.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Display information.
 *
 * Used by {@link ezcWebdavTransport} to transport information on displaying a
 * response to the browser.
 *
 * @version 1.1.4
 * @package Webdav
 */
class ezcWebdavXmlDisplayInformation extends ezcWebdavDisplayInformation
{
    /**
     * Response object to extract headers from.
     * 
     * @var ezcWebdavResponse
     */
    public $response;

    /**
     * Representation of the response body.
     * Should be null, if no body is to be sent, an instance of DOMDocument to
     * send and XML body or a string representng the body if it is non-XML.
     * 
     * @var DOMDocument|string|null
     */
    public $body;
    
    /**
     * Creates a new struct.
     * 
     * This display information must be created with DOMDocument $body.
     *
     * @param ezcWebdavResponse $response 
     * @param DOMDocument $body 
     * @return void
     */
    public function __construct( ezcWebdavResponse $response, DOMDOcument $body )
    {
        $this->response = $response;
        $this->body     = $body;
    }
}

?>
