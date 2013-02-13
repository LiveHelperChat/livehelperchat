<?php
/**
 * File containing the ezcAuthenticationSession class.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Support for session authentication and saving of authentication information
 * between requests.
 *
 * Contains the methods:
 * - start - starts the session, calling the PHP function session_start()
 * - load - returns the information stored in the session key ezcAuth_id
 * - save - saves information in the session key ezcAuth_id and also saves
 *          the current timestamp in the session key ezcAuth_timestamp
 * - destroy - deletes the information stored in the session keys ezcAuth_id
 *             and ezcAuth_timestamp
 * - regenerateId - regenerates the PHPSESSID value
 *
 * Example of use (combined with the Htpasswd filter):
 * <code>
 * // no headers should be sent before calling $session->start()
 * $session = new ezcAuthenticationSession();
 * $session->start();
 *
 * // retrieve the POST request information
 * $user = isset( $_POST['user'] ) ? $_POST['user'] : $session->load();
 * $password = isset( $_POST['password'] ) ? $_POST['password'] : null;
 * $credentials = new ezcAuthenticationPasswordCredentials( $user, $password );
 * $authentication = new ezcAuthentication( $credentials );
 * $authentication->session = $session;
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
 *                 ),
 *             'ezcAuthenticationSession' => array(
 *                 ezcAuthenticationSession::STATUS_EMPTY => '',
 *                 ezcAuthenticationSession::STATUS_EXPIRED => 'Session expired'
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
 * See {@link ezcAuthenticationSessionOptions} for options you can set to
 * session objects.
 *
 * @package Authentication
 * @version 1.3.1
 * @mainclass
 */
class ezcAuthenticationSession
{
    /**
     * Successful authentication; normal behaviour is to skip the other filters.
     *
     * This should be the same value as ezcAuthenticationFilter::STATUS_OK.
     */
    const STATUS_OK = 0;

    /**
     * The session is empty; normal behaviour is to continue with the other filters.
     */
    const STATUS_EMPTY = 1;

    /**
     * The session expired; normal behaviour is to regenerate the session ID.
     */
    const STATUS_EXPIRED = 2;

    /**
     * Options for authentication filters.
     * 
     * @var ezcAuthenticationFilterOptions
     */
    protected $options;

    /**
     * Creates a new object of this class.
     *
     * @param ezcAuthenticationSessionOptions $options Options for this class
     */
    public function __construct( ezcAuthenticationSessionOptions $options = null )
    {
        $this->options = ( $options === null ) ? new ezcAuthenticationSessionOptions() : $options;
    }

    /**
     * Runs through the session and returns a status code when finished.
     *
     * @param ezcAuthenticationCredentials $credentials Authentication credentials
     * @return int
     */
    public function run( $credentials )
    {
        $this->start();
        if ( isset( $_SESSION[$this->options->timestampKey] ) && 
             time() - $_SESSION[$this->options->timestampKey] >= $this->options->validity
           )
        {
            $this->destroy();
            $this->regenerateId();
            return self::STATUS_EXPIRED;
        }
        if ( $this->load() !== null )
        {
            return self::STATUS_OK;
        }
        return self::STATUS_EMPTY;
    }

    /**
     * Runs through the session and returns true if the session is correct.
     *
     * When using the session, it is often desirable to take advantage of the
     * fact that the authenticated state of the user is kept in the session and
     * not create and initialize the other filters (which might slow things
     * down on every request).
     *
     * The application can be structured like this:
     * <code>
     * $session = new ezcAuthenticationSession();
     * $session->start();
     *
     * $credentials = new ezcAuthenticationPasswordCredentials( $user, $pass );
     *
     * $authenticated = false;
     * if ( !$session->isValid( $credentials ) )
     * {
     *     // create the authentication object
     *     $authentication = new ezcAuthentication( $credentials );
     *     $authentication->session = $session;
     *
     *     // create filters and add them to the authentication object
     *     $authentication->addFilter( new ezcAuthenticationOpenidFilter() );
     *
     *     // run the authentication object
     *     if ( !$authentication->run() )
     *     {
     *         $status = $authentication->getStatus();
     *         // build an error message based on $status
     *     }
     *     else
     *     {
     *         $authenticated = true;
     *     }
     * }
     * else
     * {
     *     $authenticated = true;
     * }
     *
     * if ( $authenticated )
     * {
     *     // the authentication succeeded and the user can see his content
     * }
     * else
     * {
     *     // inform the user that the authentication failed (with the error
     *     // message that was created earlier)
     * }
     * </code>
     *
     * In this way, the creation and initialization of the authentication
     * filters is not performed if the credentials are stored in the session.
     *
     * @param ezcAuthenticationCredentials $credentials Authentication credentials
     * @return bool
     */
    public function isValid( $credentials )
    {
        return ( $this->run( $credentials ) === self::STATUS_OK );
    }

    /**
     * Starts the session.
     *
     * This function must be called before sending any headers to the client.
     */
    public function start()
    {
        if ( session_id() === '' && PHP_SAPI !== 'cli' )
        {
            session_set_cookie_params(0); 
            session_start();
        }
    }

    /**
     * Loads the authenticated username from the session or null if it doesn't exist.
     *
     * @return string
     */
    public function load()
    {
        return isset( $_SESSION[$this->options->idKey] ) ? $_SESSION[$this->options->idKey] :
                                                                null;
    }

    /**
     * Saves the authenticated username and the current timestamp in the session
     * variables.
     *
     * @param string $data Information to save in the session, usually username
     */
    public function save( $data )
    {
        $_SESSION[$this->options->idKey] = $data;
        $_SESSION[$this->options->timestampKey] = time();
    }

    /**
     * Removes the variables used by this class from the session variables.
     */
    public function destroy()
    {
        unset( $_SESSION[$this->options->idKey] );
        unset( $_SESSION[$this->options->timestampKey] );
    }
    
    /**
     * Regenerates the session ID.
     */
    public function regenerateId()
    {
        if ( !headers_sent() )
        {
            // ???? seems that PHPSESSID is not regenerated if session is destroyed first????
            // session_destroy();
            session_regenerate_id();
        }
    }

    /**
     * Sets the options of this class to $options.
     *
     * @param ezcAuthenticationSessionOptions $options Options for this class
     */
    public function setOptions( ezcAuthenticationSessionOptions $options )
    {
        $this->options = $options;
    }

    /**
     * Returns the options of this class.
     *
     * @return ezcAuthenticationSessionOptions
     */
    public function getOptions()
    {
        return $this->options;
    }
}
?>
