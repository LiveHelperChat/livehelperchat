<?php
/**
 * File containing the ezcWebdavOutputResult struct.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Struct representing a result to display.
 *
 * @version 1.1.4
 * @package Webdav
 */
class ezcWebdavOutputResult
{
    /**
     * Response code
     * 
     * @var string
     */
    public $status;

    /**
     * Response headers
     * 
     * @var array
     */
    public $headers;

    /**
     * Response body
     * 
     * @var string
     */
    public $body;
    
    /**
     * Creates a new struct.
     * 
     * @param string $status 
     * @param array $header 
     * @param string $body 
     * @return void
     */
    public function __construct( $status = '', array $header = array(), $body = '' )
    {
        $this->status = $status;
        $this->header = $header;
        $this->body   = $body;
    }
}

?>
