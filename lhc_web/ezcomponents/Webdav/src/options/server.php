<?php
/**
 * File containing the ezcWebdavServerOptions class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Options class for ezcWebdavServer.
 *
 * These options are used in the {@link ezcWebdavServer} instance.
 *
 * @property string $realm
 *           The realm that is used in WWW-Authorization headers. The default
 *           is "eZ Components WebDAV".
 *
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavServerOptions extends ezcBaseOptions
{
    /**
     * Constructs a new options objet with the given $options;
     *
     * @throws ezcBasePropertyNotFoundException
     *         if $options contains a property not defined
     * @throws ezcBaseValueException
     *         if $options contains a property with a value not allowed
     * @param array(string=>mixed) $options
     */
    public function __construct( array $options = array() )
    {
        $this->properties['realm'] = 'eZ Components WebDAV';

        parent::__construct( $options );
    }

    /**
     * Sets the option $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name is not defined
     * @throws ezcBaseValueException
     *         if $value is not correct for the property $name
     * @param string $name
     * @param mixed $value
     * @return void
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'realm':
                if ( !is_string( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'string' );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
        $this->properties[$name] = $value;
    }
}

?>
