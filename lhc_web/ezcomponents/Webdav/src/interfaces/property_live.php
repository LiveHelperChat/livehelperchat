<?php
/**
 * File containing the ezcWebdavLiveProperty class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Abstract base class for live property objects.
 *
 * All classes representing live (in the meanining of server maintained)
 * properties must extend this common base class. Live properties reside in the
 * 'DAV:' namespace.
 *
 * @version 1.1.4
 * @package Webdav
 */
abstract class ezcWebdavLiveProperty extends ezcWebdavProperty
{
    /**
     * Creates a new live property.
     *
     * Creates a new live property with the $name in the default namespace
     * "DAV:".
     * 
     * @param string $name
     * @return void
     */
    public function __construct( $name )
    {
        parent::__construct( 'DAV:', $name );
    }
}

?>
