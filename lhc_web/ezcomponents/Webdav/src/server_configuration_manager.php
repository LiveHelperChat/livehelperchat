<?php
/**
 * File containing the ezcWebdavServerConfigurationManager class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Manages and dispatches server configurations.
 *
 * An instance of this class is kept in the singleton instance of {@link
 * ezcWebdavServer} and keeps track of different server configurations to be
 * used with different clients.
 *
 * Several special configurations exist per default:
 * - MS InternetExplorer compatible
 * - GNOME Nautilus compatible
 *
 * In addtion, a default configuration that behaves RFC compliant is included
 * as fallback for any other client.
 *
 * Configurations can be accessed by the ArrayAccess and Iterator interfaces.
 * To insert new configurations, the method {@link insertBefore()} should be
 * used.
 * 
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavServerConfigurationManager implements ArrayAccess, Iterator
{
    /**
     * Transport configurations to dispatch. 
     * 
     * @var array(int=>ezcWebdavServerConfiguration)
     */
    protected $configurations = array();

    /**
     * Creates a new dispatcher.
     *
     * This creates a new manager object and registers the default {@link
     * ezcWebdavServerConfiguration} instances automatically. The last added
     * configuration is the RFC compliant one, which matches for every client
     * if no other configurations matched before. That means, all following
     * should be added by {@link insertBefore()} to ensure, this
     * catchall will not break the transfer layer.
     * 
     * @return void
     */
    public function __construct()
    {
        // Add MS compatible configuration
        $this[] = new ezcWebdavServerConfiguration(
            '(Microsoft\s+Data\s+Access|MSIE|MiniRedir)i',
            'ezcWebdavMicrosoftCompatibleTransport'
        );
        // Add Nautilus configuration
        $this[] = new ezcWebdavServerConfiguration(
            '(gnome-vfs/[0-9.]+ neon/[0-9.]*|gvfs/[0-9.]+)i',
            'ezcWebdavNautilusCompatibleTransport',
            'ezcWebdavXmlTool',
            'ezcWebdavNautilusPropertyHandler'
        );
        // Add Konqueror configuration
        $this[] = new ezcWebdavServerConfiguration(
            '(Konqueror)i',
            'ezcWebdavKonquerorCompatibleTransport'
        );
        // Add default RFC compliant transport as final catchall
        $this[] = new ezcWebdavServerConfiguration();
    }

    /**
     * Inserts a configuration right before a certain offset.
     *
     * This method inserts a given $config right before the given $offset. The
     * $offset must be of type integer and between 0 and the number of elements
     * in {@link $this->configurations} minus 1.
     *
     * If these preconditions do not match for the given $offset, an
     * {@link ezcBaseValueException} is thrown.
     * 
     * @param ezcWebdavServerConfiguration $config 
     * @param int $offset 
     * @return void
     *
     * @throws ezcBaseValueException
     *         if the given $offset is not an integer that is larger or equal
     *         to 0 and smaller than the number of elements in {@link
     *         $this->configurations}.
     */
    public function insertBefore( ezcWebdavServerConfiguration $config, $offset = 0 )
    {
        if ( !is_int( $offset ) || $offset < 0 || $offset > ( count( $this->configurations ) - 1 ) )
        {
            throw new ezcBaseValueException( 'index', $offset, 'int >= 0, < number of transport configurations' );
        }
        array_splice( $this->configurations, $offset, 0, array( $config ) );
    }

    /**
     * Configures the server for handling a request by the given User-Agent.
     *
     * This method is used by {@link ezcWebdavServer} to determine the correct
     * {@link ezcWebdavTransport} for the current request. It returns the
     * {@link ezcWebdavTransport} created by the {@link
     * ezcWebdavServerConfiguration} which matched the submitted User-Agent
     * header first.
     *
     * Per default, the RFC compliant default implementation {@link
     * ezcWebdavTransport} is configured to catch all User-Agent headers for
     * which no specific implementation could be found. If this configuration
     * has been removed or manipulated incorrectly, an {@link
     * ezcWebdavMissingTransportConfigurationException} might be thrown.
     * 
     * @param ezcWebdavServer $server
     * @param mixed $userAgent 
     * @return void
     *
     * @throws ezcWebdavMissingTransportConfigurationException
     *         if no {@link ezcWebdavServerConfiguration} could be found
     *         that matches the given $userAgent.
     */
    public function configure( $server, $userAgent )
    {
        foreach ( $this as $transportConfiguration )
        {
            if ( preg_match( $transportConfiguration->userAgentRegex, $userAgent ) > 0 )
            {
                $transportConfiguration->configure( $server );
                return;
            }
        }
        throw new ezcWebdavMissingTransportConfigurationException( $userAgent );
    }

    /**
     * Checks the given $offset for validity.
     *
     * This method checks if the given $offset is either of type int, then
     * larger 0 and not larger as the number of elements in {@link
     * $this->configurations}, or null.
     *
     * The method is primarily used in the {@link ArrayAccess} methods.
     * 
     * @param int|null $offset 
     * @return void
     *
     * @throws ezcBaseValueException
     *         if the given $offset is not an an int with the given criteria
     *         and not null.
     */
    protected function checkOffset( $offset )
    {
        if ( ( !is_int( $offset ) || $offset < 0 || $offset > count( $this->configurations ) ) && $offset !== null )
        {
            throw new ezcBaseValueException( 'offset', $offset, 'int >= 0, <= number of transport configurations' );
        }
    }

    /**
     * Checks the given $value for validity.
     *
     * This method checks if the given $value is either an instance of {@link
     * ezcWebdavServerConfiguration} or null.
     *
     * The method is primarily used in the {@link ArrayAccess} methods.
     * 
     * @param ezcWebdavServerConfiguration|null $value 
     * @return void
     *
     * @throws ezcBaseValueException
     *         if the given $value is not an instance of
     *         ezcWebdavServerConfiguration or null.
     */
    protected function checkValue( $value )
    {
        if ( !( $value instanceof ezcWebdavServerConfiguration ) && $value !== null )
        {
            throw new ezcBaseValueException( 'value', $value, 'ezcWebdavServerConfiguration' );
        }
    }

    // ArrayAccess

    /**
     * Array set access. 
     * 
     * @param string $offset 
     * @param string $value 
     * @return void
     */
    public function offsetSet( $offset, $value )
    {
        $this->checkOffset( $offset );
        $this->checkValue( $value );

        if ( $value === null )
        {
            return $this->offsetUnset( $offset );
        }
        if ( $offset === null )
        {
            $offset = count( $this->configurations );
        }
        $this->configurations[$offset] = $value;
    }

    /**
     * Array get access.
     * 
     * @param string $offset 
     * @return string
     * @ignore
     */
    public function offsetGet( $offset )
    {
        $this->checkOffset( $offset );
        if ( !isset( $this->configurations[$offset] ) )
        {
            return null;
        }
        return $this->configurations[$offset];
    }

    /**
     * Array unset() access.
     *
     * @param string $offset 
     * @return void
     * @ignore
     */
    public function offsetUnset( $offset )
    {
        $this->checkOffset( $offset );
        if ( $offset === null || $offset === count( $this->configurations ) )
        {
            return;
        }

        array_splice( $this->configurations, $offset, 1 );
    }

    /**
     * Array isset() access.
     * 
     * @param string $offset 
     * @return bool
     * @ignore
     */
    public function offsetExists( $offset )
    {
        return isset( $this->configurations[$offset] );
    }

    // Iterator

    /**
     * Implements current() for Iterator
     * 
     * @return mixed
     */
    public function current()
    {
        return current( $this->configurations );
    }

    /**
     * Implements key() for Iterator
     * 
     * @return int
     */
    public function key()
    {
        return key( $this->configurations );
    }

    /**
     * Implements next() for Iterator
     * 
     * @return mixed
     */
    public function next()
    {
        return next( $this->configurations );
    }

    /**
     * Implements rewind() for Iterator
     * 
     * @return void
     */
    public function rewind()
    {
        return reset( $this->configurations );
    }

    /**
     * Implements valid() for Iterator
     * 
     * @return boolean
     */
    public function valid()
    {
        return ( current( $this->configurations ) !== false );
    }

}

?>
