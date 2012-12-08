<?php
/**
 * File containing the ezcAuthenticationHtpasswdFilter class.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Filter to authenticate against an Unix htpasswd file.
 *
 * It supports files created with the htpasswd command options
 *  -m (MD5 encryption - different than the PHP md5() function)
 *  -d (CRYPT encryption)
 *  -s (SHA encryption)
 *  -p (plain text)
 *
 * The encryption used for the password field in the file will be detected
 * automatically.
 *
 * The password property can be specified as plain text or in encrypted form,
 * depending on the option 'plain' in the ezcAuthenticationHtpasswdOptions object
 * used as options.
 *
 * Example:
 * <code>
 * $credentials = new ezcAuthenticationPasswordCredentials( 'jan.modaal', 'b1b3773a05c0ed0176787a4f1574ff0075f7521e' );
 * $authentication = new ezcAuthentication( $credentials );
 * $authentication->session = new ezcAuthenticationSession();
 * $authentication->addFilter( new ezcAuthenticationHtpasswdFilter( '/etc/htpasswd' ) );
 * // add other filters if needed
 * if ( !$authentication->run() )
 * {
 *     // authentication did not succeed, so inform the user
 *     $status = $authentication->getStatus();
 *     $err = array(
 *             'ezcAuthenticationHtpasswdFilter' => array(
 *                 ezcAuthenticationHtpasswdFilter::STATUS_USERNAME_INCORRECT => 'Incorrect username',
 *                 ezcAuthenticationHtpasswdFilter::STATUS_PASSWORD_INCORRECT => 'Incorrect password'
 *                 )
 *             );
 *     foreach ( $status as $line )
 *     {
 *         list( $key, $value ) = each( $line );
 *         echo $err[$key][$value] . "\n";
 *     }
 * }
 * else
 * {
 *     // authentication succeeded, so allow the user to see his content
 * }
 * </code>
 *
 * @property string $file
 *           The path and file name of the htpasswd file to use.
 *
 * @package Authentication
 * @version 1.3.1
 * @mainclass
 */
class ezcAuthenticationHtpasswdFilter extends ezcAuthenticationFilter
{
    /**
     * Username is not found in the htpasswd file.
     */
    const STATUS_USERNAME_INCORRECT = 1;

    /**
     * Password is incorrect.
     */
    const STATUS_PASSWORD_INCORRECT = 2;

    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    private $properties = array();

    /**
     * Creates a new object of this class.
     *
     * @throws ezcBaseValueException
     *         if the value provided is not correct for the property $file
     * @throws ezcBaseFileNotFoundException
     *         if $file does not exist
     * @throws ezcBaseFilePermissionException
     *         if $file cannot be opened for reading
     * @param string $file The path and file name of the htpasswd file to use
     * @param ezcAuthenticationHtpasswdOptions $options Options for this class
     */
    public function __construct( $file, ezcAuthenticationHtpasswdOptions $options = null )
    {
        $this->file = $file;
        $this->options = ( $options === null ) ? new ezcAuthenticationHtpasswdOptions() : $options;
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
     *         if the $value file cannot be opened for reading
     * @param string $name The name of the property to set
     * @param mixed $value The new value of the property
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'file':
                if ( !is_string( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'string' );
                }

                if ( !file_exists( $value ) )
                {
                    throw new ezcBaseFileNotFoundException( $value );
                }

                if ( !is_readable( $value ) )
                {
                    throw new ezcBaseFilePermissionException( $value, ezcBaseFileException::READ );
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
            case 'file':
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
            case 'file':
                return isset( $this->properties[$name] );

            default:
                return false;
        }
    }

    /**
     * Runs the filter and returns a status code when finished.
     *
     * @param ezcAuthenticationPasswordCredentials $credentials Authentication credentials
     * @return int
     */
    public function run( $credentials )
    {
        $fh = fopen( $this->file, 'r' );
        $found = false;
        while ( $line = fgets( $fh ) )
        {
            if ( substr( $line, 0, strlen( $credentials->id ) + 1 ) === $credentials->id . ':' )
            {
                $found = true;
                break;
            }
        }
        fclose( $fh );
        if ( $found )
        {
            $parts = explode( ':', $line );
            $hashFromFile = trim( $parts[1] );
            if ( substr( $hashFromFile, 0, 6 ) === '$apr1$' )
            {
                $password = ( $this->options->plain ) ? ezcAuthenticationMath::apr1( $credentials->password, $hashFromFile ) :
                                                        '$apr1$' . $credentials->password;
            }
            elseif ( substr( $hashFromFile, 0, 5 ) === '{SHA}' )
            {
                $password = ( $this->options->plain ) ? '{SHA}' . base64_encode( pack( 'H40', sha1( $credentials->password ) ) ) :
                                                        '{SHA}' . $credentials->password;
            }
            else
            {
                $password = ( $this->options->plain ) ? crypt( $credentials->password, $hashFromFile ) :
                                                        $credentials->password;
            }
            if ( $password === $hashFromFile )
            {
                return self::STATUS_OK;
            }
            else
            {
                return self::STATUS_PASSWORD_INCORRECT;
            }
        }
        return self::STATUS_USERNAME_INCORRECT;
    }
}
?>
