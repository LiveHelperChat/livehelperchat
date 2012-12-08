<?php
/**
 * File containing the ezcMailTransportConnection class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * ezcMailTransportConnection is an internal class used to connect to
 * a server and have line based communication with.
 *
 * @property ezcMailTransportOptions $options
 *           Holds the options you can set to the transport connection.
 *
 * @package Mail
 * @version 1.7.1
 * @access private
 */
class ezcMailTransportConnection
{
    /**
     * The line-break characters to send to the server.
     */
    const CRLF = "\r\n";

    /**
     * The connection to the server or null if there is none.
     *
     * @var resource
     */
    private $connection = null;

    /**
     * Options for a transport connection.
     *
     * @var ezcMailTransportOptions
     */
    private $options;

    /**
     * Constructs a new connection to the $server using the port $port.
     *
     * {@link ezcMailTransportOptions} for options you can specify for a
     * transport connection.
     *
     * @todo The @ should be removed when PHP doesn't throw warnings for connect problems.
     *
     * @throws ezcMailTransportException
     *         if a connection to the server could not be made
     * @throws ezcBaseExtensionNotFoundException
     *         if trying to use SSL and the extension openssl is not installed
     * @throws ezcBasePropertyNotFoundException
     *         if $options contains a property not defined
     * @throws ezcBaseValueException
     *         if $options contains a property with a value not allowed
     * @param string $server
     * @param int $port
     * @param ezcMailTransportOptions $options
     */
    public function __construct( $server, $port, ezcMailTransportOptions $options = null )
    {
        $errno = null;
        $errstr = null;
        if ( $options === null )
        {
            $this->options = new ezcMailTransportOptions();
        }
        else
        {
            $this->options = $options;
        }
        if ( $this->options->ssl )
        {
            if ( ezcBaseFeatures::hasExtensionSupport( 'openssl' ) !== true )
            {
                throw new ezcBaseExtensionNotFoundException( 'openssl', null, "PHP not configured --with-openssl." );
            }
            $this->connection = @stream_socket_client( "ssl://{$server}:{$port}",
                                                       $errno, $errstr, $this->options->timeout );
        }
        else
        {
            $this->connection = @stream_socket_client( "tcp://{$server}:{$port}",
                                                       $errno, $errstr, $this->options->timeout );
        }

        if ( is_resource( $this->connection ) )
        {
            stream_set_timeout( $this->connection, $this->options->timeout );
        }
        else
        {
            throw new ezcMailTransportException( "Failed to connect to the server: {$server}:{$port}." );
        }
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     * @throws ezcBaseValueException
     *         if $value is not accepted for the property $name
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'options':
                if ( !( $value instanceof ezcMailTransportOptions ) )
                {
                    throw new ezcBaseValueException( 'options', $value, 'instanceof ezcMailTransportOptions' );
                }
                $this->options = $value;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     * @param string $name
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'options':
                return $this->options;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns true if the property $name is set, otherwise false.
     *
     * @param string $name
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        switch ( $name )
        {
            case 'options':
                return true;

            default:
                return false;
        }
    }

    /**
     * Send $data to the server through the connection.
     *
     * This method appends one line-break at the end of $data.
     *
     * @throws ezcMailTransportException
     *         if there is no valid connection.
     * @param string $data
     */
    public function sendData( $data )
    {
        if ( is_resource( $this->connection ) )
        {
            if ( fwrite( $this->connection, $data . self::CRLF,
                        strlen( $data ) + strlen( self::CRLF  ) ) === false )
            {
                throw new ezcMailTransportException( 'Could not write to the stream. It was probably terminated by the host.' );
            }
        }
    }

    /**
     * Returns one line of data from the stream.
     *
     * The returned line will have linebreaks removed if the $trim option is set.
     *
     * @throws ezcMailTransportConnection
     *         if there is no valid connection
     * @param bool $trim
     * @return string
     */
    public function getLine( $trim = false )
    {
        $data = '';
        $line = '';

        if ( is_resource( $this->connection ) )
        {
            // in case there is a problem with the connection fgets() returns false
            while ( strpos( $data, self::CRLF ) === false )
            {
                $line = fgets( $this->connection, 512 );

                /* If the mail server aborts the connection, fgets() will
                 * return false. We need to throw an exception here to prevent
                 * the calling code from looping indefinitely. */
                if ( $line === false )
                {
                    $this->connection = null;
                    throw new ezcMailTransportException( 'Could not read from the stream. It was probably terminated by the host.' );
                }

                $data .= $line;
            }

            if ( $trim == false )
            {
                return $data;
            }
            else
            {
                return rtrim( $data, "\r\n" );
            }
        }
        throw new ezcMailTransportException( 'Could not read from the stream. It was probably terminated by the host.' );
    }

    /**
     * Returns if the connection is open.
     *
     * @return bool
     */
    public function isConnected()
    {
        return is_resource( $this->connection );
    }

    /**
     * Closes the connection to the server if it is open.
     */
    public function close()
    {
        if ( is_resource( $this->connection ) )
        {
            fclose( $this->connection );
            $this->connection = null;
        }
    }
}
?>
