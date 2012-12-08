<?php
/**
 * File containing the ezcWebdavMemoryBackendOptions class
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Class containing the options for the memory backend.
 *
 * An instance of this class is created an hold in {@link ezcWebdavMemoryBackend}
 * instances. Using these options the behavior of the memory backend can be
 * changed.
 *
 * @property string $failForRegexp
 *           Let operation fail for all resource paths mathing this regular
 *           expression. The exact handling of this option depends on the
 *           operation this option is used with.
 * @property int $failingOperations
 *           Operations which should respect the failForRegexp property. May be
 *           a bitmask of webdav request type constants.
 *
 * @package Webdav
 * @version 1.1.4
 * @access private
 */
class ezcWebdavMemoryBackendOptions extends ezcBaseOptions
{
    /**
     * Constants for request types.
     */
    const REQUEST_GET       = 1;
    const REQUEST_HEAD      = 2;
    const REQUEST_PUT       = 4;
    const REQUEST_PROPFIND  = 8;
    const REQUEST_PROPPATCH = 16;
    const REQUEST_DELETE    = 32;
    const REQUEST_COPY      = 64;
    const REQUEST_MOVE      = 128;
    const REQUEST_MKCOL     = 256;

    /**
     * Constructs an object with the specified values.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if $options contains a property not defined
     * @throws ezcBaseValueException
     *         if $options contains a property with a value not allowed
     * @param array(string=>mixed) $options
     */
    public function __construct( array $options = array() )
    {
        $this->properties['failForRegexp']      = null;
        $this->properties['failingOperations']  = 0;
        $this->properties['lockFile']           = null;

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
            case 'failForRegexp':
                if ( !is_string( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'regular expression' );
                }

                $this->properties[$name] = $value;
                break;

            case 'failingOperations':
                if ( !is_int( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'integer' );
                }

                $this->properties[$name] = $value;
                break;

            case 'lockFile':
                if ( !is_string( $value ) || !is_writeable( dirname( $value ) ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'writable file name' );
                }

                $this->properties[$name] = $value;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }
}
?>
