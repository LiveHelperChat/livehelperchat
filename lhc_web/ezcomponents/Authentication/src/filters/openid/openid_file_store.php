<?php
/**
 * File containing the ezcAuthenticationOpenidFileStore class.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Class providing file storage for OpenID authentication.
 *
 * Example of use:
 * <code>
 * // create an OpenID options object
 * $options = new ezcAuthenticationOpenidOptions();
 * $options->mode = ezcAuthenticationOpenidFilter::MODE_SMART;
 *
 * // define a file store
 * $options->store = new ezcAuthenticationOpenidFileStore( '/tmp/store' );
 *
 * // create an OpenID filter based on the options object
 * $filter = new ezcAuthenticationOpenidFilter( $options );
 * </code>
 *
 * @property string $path
 *           The path where the files will be kept. It must exist and it must
 *           be writable.
 *
 * @package Authentication
 * @version 1.3.1
 */
class ezcAuthenticationOpenidFileStore extends ezcAuthenticationOpenidStore
{
    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    private $properties = array();

    /**
     * Creates a new object of this class.
     *
     * @throws ezcBaseFileNotFoundException
     *         if $path does not exist
     * @throws ezcBaseFilePermissionException
     *         if $path cannot be opened for reading and writing
     * @param string $path The path where to save the nonces
     * @param ezcAuthenticationOpenidFileStoreOptions $options Options for this class
     */
    public function __construct( $path, ezcAuthenticationOpenidFileStoreOptions $options = null )
    {
        $this->path = $path;
        $this->options = ( $options === null ) ? new ezcAuthenticationOpenidFileStoreOptions() : $options;
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     * @throws ezcBaseValueException
     *         if $value is not correct for the property $name
     * @throws ezcBaseFileNotFoundException
     *         if the $value file does not exist
     * @throws ezcBaseFilePermissionException
     *         if the $value file cannot be opened for reading and writing
     * @param string $name The name of the property to set
     * @param mixed $value The new value of the property
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'path':
                if ( !is_string( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'string' );
                }

                if ( !is_dir( $value ) )
                {
                    throw new ezcBaseFileNotFoundException( $value );
                }

                if ( !is_readable( $value ) )
                {
                    throw new ezcBaseFilePermissionException( $value, ezcBaseFileException::READ );
                }

                if ( !is_writable( $value ) )
                {
                    throw new ezcBaseFilePermissionException( $value, ezcBaseFileException::WRITE );
                }

                $this->properties[$name] = $value;
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
     * @param string $name The name of the property for which to return the value
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'path':
                return $this->properties[$name];

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns true if the property $name is set, otherwise false.
     *
     * @param string $name The name of the property to test if it is set
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        switch ( $name )
        {
            case 'path':
                return isset( $this->properties[$name] );

            default:
                return false;
        }
    }

    /**
     * Stores the nonce in the store.
     *
     * Returns true if the nonce was stored successfully, and false otherwise.
     *
     * @throws ezcBaseFilePermissionException
     *         if the nonce cannot be written in the store
     * @param string $nonce The nonce value to store
     * @return bool
     */
    public function storeNonce( $nonce )
    {
        $file = $this->path . DIRECTORY_SEPARATOR . $nonce;

        // suppress warnings caused by fopen() if $file could not be opened
        $fh = @fopen( $file, 'w' );

        if ( $fh === false )
        {
            throw new ezcBaseFilePermissionException( $file, ezcBaseFileException::WRITE );
        }

        fclose( $fh );

        return true;
    }

    /**
     * Checks if the nonce exists and afterwards deletes it.
     *
     * Returns the timestamp of the nonce if it exists, and false otherwise.
     *
     * @param string $nonce The nonce value to check and delete
     * @return bool|int
     */
    public function useNonce( $nonce )
    {
        $file = $this->path . DIRECTORY_SEPARATOR . $nonce;

        if ( !file_exists( $file ) )
        {
            return false;
        }

        $lastModified = filemtime( $file );
        unlink( $file );

        return $lastModified;
    }

    /**
     * Stores an association in the store linked to the OpenID provider URL.
     *
     * Returns true if the association was stored successfully, and false
     * otherwise.
     *
     * @throws ezcBaseFilePermissionException
     *         if the nonce cannot be written in the store
     * @param string $url The URL of the OpenID provider
     * @param ezcAuthenticationOpenidAssociation $association The association value to store
     * @return bool
     */
    public function storeAssociation( $url, $association )
    {
        $file = $this->path . DIRECTORY_SEPARATOR . $this->convertToFilename( $url );

        // suppress warnings caused by fopen() if $file could not be opened
        $fh = @fopen( $file, 'w' );

        if ( $fh === false )
        {
            throw new ezcBaseFilePermissionException( $file, ezcBaseFileException::WRITE );
        }

        $data = serialize( $association );
        fwrite( $fh, $data );
        fclose( $fh );

        return true;
    }

    /**
     * Returns the unserialized association linked to the OpenID provider URL.
     *
     * Returns false if the association could not be retrieved or if it expired.
     *
     * @param string $url The URL of the OpenID provider
     * @return ezcAuthenticationOpenidAssociation
     */
    public function getAssociation( $url )
    {
        $file = $this->path . DIRECTORY_SEPARATOR . $this->convertToFilename( $url );

        if ( !file_exists( $file ) )
        {
            return false;
        }

        $data = unserialize( file_get_contents( $file ) );
        return $data;
    }

    /**
     * Removes the association linked to the OpenID provider URL.
     *
     * Returns true if the association could be removed, and false otherwise.
     *
     * @param string $url The URL of the OpenID provider
     * @return bool
     */
    public function removeAssociation( $url )
    {
        $file = $this->path . DIRECTORY_SEPARATOR . $this->convertToFilename( $url );

        if ( !file_exists( $file ) )
        {
            return false;
        }

        unlink( $file );
        return true;
    }

    /**
     * Creates a valid filename from the provided string.
     *
     * @param string $value A string which needs to be used as a valid filename
     * @return string
     */
    protected function convertToFilename( $value )
    {
        $result = base64_encode( $value );
        $result = str_replace( '/', '_', $result );
        $result = str_replace( '+', '-', $result );
        return $result;
    }
}
?>
