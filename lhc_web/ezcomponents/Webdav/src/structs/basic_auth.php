<?php
/**
 * File containing the ezcWebdavBasicAuth struct.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Struct containing digest authentication information.
 *
 * This struct represents authentication data as provided by the HTTP Basic
 * specification.
 * 
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavBasicAuth extends ezcWebdavAuth
{
    /**
     * Plain text password. 
     * 
     * @var string
     */
    public $password;

    /**
     * Creates a new basic auth credential struct.
     * 
     * @param string $username 
     * @param string $password 
     */
    public function __construct( $username = '', $password = '' )
    {
        parent::__construct( $username );
        $this->password = $password;
    }
}

?>
