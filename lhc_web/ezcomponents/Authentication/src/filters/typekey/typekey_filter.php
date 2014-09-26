<?php
/**
 * File containing the ezcAuthenticationTypekeyFilter class.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Filter to authenticate against TypeKey.
 *
 * The filter deals with the validation of information returned by the TypeKey
 * server in response to a login command.
 *
 * Specifications: {@link http://www.sixapart.com/typekey/api}
 *
 * In order to access a protected page, user logs in by using a request like:
 *  - https://www.typekey.com/t/typekey/login?
 *        t=391jbj25WAQANzJrKvb5&
 *        _return=http://example.com/login.php
 *
 * where:
 *  - t = TypeKey token generated for each TypeKey account.
 *        It is found at https://www.typekey.com/t/typekey/prefs.
 *        This value is also used as a session key, so it must be passed to the
 *        page performing the TypeKey authentication via the _return URL.
 *  - _return = the URL where to return after user logs in with his TypeKey
 *              username and password. The URL can contain query arguments, such
 *              as the value t which can be used as a session key.
 *
 * The login link can also contain these 2 optional values:
 *  - v = TypeKey version to use. Default is 1.
 *  - need_email = the mail address which was used to register with TypeKey. It
 *                 needs to be set to a value different than 0 in order to get
 *                 the email address of the user when calling fetchData() after
 *                 the authentication process has been completed.
 *
 * So the TypeKey authentication filter will run in the _return page and will
 * verify the signature and the other information in the URL.
 *
 * The application link (eg. http://example.com) must be registered in the
 * TypeKey preferences page (https://www.typekey.com/t/typekey/prefs) in one
 * of the 5 lines from "Your Weblog Preferences", otherwise TypeKey will
 * not accept the login request.
 *
 * The link returned by TypeKey after user logs in with his TypeKey username
 * and password looks like this:
 *  - http://example.com/typekey.php?
 *       ts=1177319974&email=5098f1e87a608675ded4d933f31899cae6b4f968&
 *       name=ezc&nick=ezctest&
 *       sig=I9Dop72+oahY82bpL7ymBoxdQ+k=:Vj/t7oZVL2zMSzwHzdOWop5NG/g=
 *
 * where:
 *  - ts = timestamp (in seconds) of the TypeKey server time at login.
 *         The TypeKey filter compares this timestamp with the application
 *         server's timestamp to make sure the login is in a reasonable
 *         time window (specified by the validity option). Don't use a too small
 *         value for validity, because servers are not always synchronized.
 *  - email = sha1 hash of "mailto:$mail", where $mail is the mail address
 *            used to register with TypeKey.
 *  - nick = TypeKey nickname/display name.
 *  - sig = signature which must be validated by the TypeKey filter.
 *
 * For more information on the login request and the TypeKey response link see
 * {@link http://www.sixapart.com/typekey/api}.
 *
 * Example of use (authentication + input form):
 * <code>
 * <?php
 * // no headers should be sent before calling $session->start()
 * $session = new ezcAuthenticationSession();
 * $session->start();
 *
 * // $token is used as a key in the session to store the authenticated state between requests
 * $token = isset( $_GET['token'] ) ? $_GET['token'] : $session->load();
 *
 * $credentials = new ezcAuthenticationIdCredentials( $token );
 * $authentication = new ezcAuthentication( $credentials );
 * $authentication->session = $session;
 *
 * $filter = new ezcAuthenticationTypekeyFilter();
 * $authentication->addFilter( $filter );
 * // add other filters if needed
 *
 * if ( !$authentication->run() )
 * {
 *     // authentication did not succeed, so inform the user
 *     $status = $authentication->getStatus();
 *     $err = array(
 *              'ezcAuthenticationTypekeyFilter' => array(
 *                  ezcAuthenticationTypekeyFilter::STATUS_SIGNATURE_INCORRECT => 'Signature returned by TypeKey is incorrect',
 *                  ezcAuthenticationTypekeyFilter::STATUS_SIGNATURE_EXPIRED => 'The signature returned by TypeKey expired'
 *                  ),
 *              'ezcAuthenticationSession' => array(
 *                  ezcAuthenticationSession::STATUS_EMPTY => '',
 *                  ezcAuthenticationSession::STATUS_EXPIRED => 'Session expired'
 *                  )
 *              );
 *     foreach ( $status as $line )
 *     {
 *         list( $key, $value ) = each( $line );
 *         echo $err[$key][$value] . "\n";
 *     }
 * ?>
 * <!-- OnSubmit hack to append the value of t to the _return value, to pass
 *      the TypeKey token after the TypeKey request -->
 * <form method="GET" action="https://www.typekey.com/t/typekey/login" onsubmit="document.getElementById('_return').value += '?token=' + document.getElementById('t').value;">
 * TypeKey token: <input type="text" name="t" id="t" />
 * <input type="hidden" name="_return" id="_return" value="http://localhost/typekey.php" />
 * <input type="submit" />
 * </form>
 * <?php
 * }
 * else
 * {
 *     // authentication succeeded, so allow the user to see his content
 *     echo "<b>Logged-in</b>";
 * }
 * ?>
 * </code>
 *
 * Another method, which doesn't use JavaScript, is using an intermediary page
 * which saves the token in the session, then calls the TypeKey login page:
 *
 * - original file is modified as follows:
 * <code>
 * <form method="GET" action="save_typekey.php">
 * TypeKey token: <input type="text" name="t" id="t" />
 * <input type="hidden" name="_return" id="_return" value="http://localhost/typekey.php" />
 * <input type="submit" />
 * </form>
 * </code>
 *
 * - intermediary page:
 * <code>
 * <?php
 * // no headers should be sent before calling $session->start()
 * $session = new ezcAuthenticationSession();
 * $session->start();
 *
 * // $token is used as a key in the session to store the authenticated state between requests
 * $token = isset( $_GET['t'] ) ? $_GET['t'] : $session->load();
 * if ( $token !== null )
 * {
 *     $session->save( $token );
 * }
 * $url = isset( $_GET['_return'] ) ? $_GET['_return'] : null;
 * $url .= "?token={$token}";
 * header( "Location: https://www.typekey.com/t/typekey/login?t={$token}&_return={$url}" );
 * ?>
 * </code>
 *
 * Extra data can be fetched from the TypeKey server during the authentication
 * process. Different from the other filters, for TypeKey there is no registration
 * needed for fetching the extra data, because all the possible extra data is
 * available in the response sent by the TypeKey server.
 *
 * To be able to get the email address of the user, need_email must be set
 * to a value different than 0 in the initial request sent to the TypeKey
 * server (along with the t and _return values). Example:
 *  - https://www.typekey.com/t/typekey/login?t=<token>&_return=<url>&need_email=1
 *
 * Example of fetching the extra data after the initial request has been sent:
 * <code>
 * // after run()
 * // $filter is an ezcAuthenticationTypekeyFilter object
 * $data = $filter->fetchData();
 * </code>
 *
 * The $data array contains name (TypeKey username), nick (TypeKey display name)
 * and optionally email (if the user allowed the sharing of his email address
 * in the TypeKey profile page; otherwise it is not set).
 * <code>
 * array( 'name' => array( 'john' ),
 *        'nick' => array( 'John Doe' ),
 *        'email' => array( 'john.doe@example.com' ) // or not set
 *      );
 * </code>
 *
 * @property ezcAuthenticationBignumLibrary $lib
 *           The wrapper for the PHP extension to use for big number operations.
 *           This will be autodetected in the constructor, but you can specify
 *           your own wrapper before calling run().
 *
 * @package Authentication
 * @version 1.3.1
 * @mainclass
 */
class ezcAuthenticationTypekeyFilter extends ezcAuthenticationFilter implements ezcAuthenticationDataFetch
{
    /**
     * The request does not contain the needed information (like $_GET['sig']).
     */
    const STATUS_SIGNATURE_MISSING = 1;

    /**
     * Signature verification was incorect.
     */
    const STATUS_SIGNATURE_INCORRECT = 2;

    /**
     * Login is outside of the timeframe.
     */
    const STATUS_SIGNATURE_EXPIRED = 3;

    /**
     * Holds the extra data fetched during the authentication process.
     *
     * Contains name (TypeKey username), nick (TypeKey display name) and
     * optionally email (if the user allowed the sharing of his email address
     * in the TypeKey profile page; otherwise it is not set).
     *
     * Usually it has this structure:
     * <code>
     * array( 'name' => array( 'john' ),
     *        'nick' => array( 'John Doe' ),
     *        'email' => array( 'john.doe@example.com' ) // or not set
     *      );
     * </code>
     *
     * @var array(string=>mixed)
     */
    protected $data = array();

    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    private $properties = array();

    /**
     * Creates a new object of this class.
     *
     * @throws ezcBaseExtensionNotFoundException
     *         if neither of the PHP gmp and bcmath extensions are installed
     * @param ezcAuthenticationTypekeyOptions $options Options for this class
     */
    public function __construct( ezcAuthenticationTypekeyOptions $options = null )
    {
        $this->options = ( $options === null ) ? new ezcAuthenticationTypekeyOptions() : $options;
        $this->lib = ezcAuthenticationMath::createBignumLibrary();
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     * @throws ezcBaseValueException
     *         if $value is not correct for the property $name
     * @param string $name The name of the property to set
     * @param mixed $value The new value of the property
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'lib':
                if ( $value instanceof ezcAuthenticationBignumLibrary )
                {
                    $this->properties[$name] = $value;
                }
                else
                {
                    throw new ezcBaseValueException( $name, $value, 'ezcAuthenticationBignumLibrary' );
                }
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
            case 'lib':
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
            case 'lib':
                return isset( $this->properties[$name] );

            default:
                return false;
        }
    }

    /**
     * Runs the filter and returns a status code when finished.
     *
     * @throws ezcAuthenticationTypekeyException
     *         if the keys from the TypeKey public keys file could not be fetched
     * @param ezcAuthenticationIdCredentials $credentials Authentication credentials
     * @return int
     */
    public function run( $credentials )
    {
        $source = $this->options->requestSource;
        if ( isset( $source['name'] ) && isset( $source['email'] ) && isset( $source['nick'] ) && isset( $source['ts'] ) && isset( $source['sig'] ) )
        {
            // parse the response URL sent by the TypeKey server
            $id = isset( $source['name'] ) ? $source['name'] : null;
            $mail = isset( $source['email'] ) ? $source['email'] : null;
            $nick = isset( $source['nick'] ) ? $source['nick'] : null;
            $timestamp = isset( $source['ts'] ) ? $source['ts'] : null;
            $signature = isset( $source['sig'] ) ? $source['sig'] : null;

            // extra data which will be returned by fetchData()
            $this->data['name'] = array( $id );
            $this->data['nick'] = array( $nick );
            if ( strpos( $mail, '@' ) !== false )
            {
                $this->data['email'] = array( $mail );
            }
        }
        else
        {
            return self::STATUS_SIGNATURE_MISSING;
        }
        if ( $this->options->validity !== 0 &&
             time() - $timestamp >= $this->options->validity
           )
        {
            return self::STATUS_SIGNATURE_EXPIRED;
        }
        $keys = $this->fetchPublicKeys( $this->options->keysFile );
        $msg = "{$mail}::{$id}::{$nick}::{$timestamp}";
        $signature = rawurldecode( urlencode( $signature ) );
        list( $r, $s ) = explode( ':', $signature );
        if ( $this->checkSignature( $msg, $r, $s, $keys ) )
        {
            return self::STATUS_OK;
        }
        return self::STATUS_SIGNATURE_INCORRECT;
    }

    /**
     * Checks the information returned by the TypeKey server.
     *
     * @param string $msg Plain text signature which needs to be verified
     * @param string $r First part of the signature retrieved from TypeKey
     * @param string $s Second part of the signature retrieved from TypeKey
     * @param array(string=>string) $keys Public keys retrieved from TypeKey
     * @return bool
     */
    protected function checkSignature( $msg, $r, $s, $keys )
    {
        $lib = $this->lib;

        $r = base64_decode( $r );
        $s = base64_decode( $s );

        foreach ( $keys as $key => $value )
        {
            $keys[$key] = $lib->init( (string) $value );
        }

        $s1 = $lib->init( $lib->binToDec( $r ) );
        $s2 = $lib->init( $lib->binToDec( $s ) );

        $w = $lib->invert( $s2, $keys['q'] );

        $msg = $lib->hexToDec( sha1( $msg ) );

        $u1 = $lib->mod( $lib->mul( $msg, $w ), $keys['q'] );
        $u2 = $lib->mod( $lib->mul( $s1, $w ), $keys['q'] );

        $v = $lib->mul( $lib->powmod( $keys['g'], $u1, $keys['p'] ), $lib->powmod( $keys['pub_key'], $u2, $keys['p'] ) );
        $v = $lib->mod( $lib->mod( $v, $keys['p'] ), $keys['q'] );

        return ( $lib->cmp( $v, $s1 ) === 0 );
    }

    /**
     * Fetches the public keys from the specified file or URL $file.
     *
     * The file must be composed of space-separated values for p, g, q, and
     * pub_key, like this:
     *   p=<value> g=<value> q=<value> pub_key=<value>
     *
     * The format of the returned array is:
     * <code>
     *   array( 'p' => p_val, 'g' => g_val, 'q' => q_val, 'pub_key' => pub_key_val )
     * </code>
     *
     * @throws ezcAuthenticationTypekeyPublicKeysMissingException
     *         if the keys from the TypeKey public keys file could not be fetched
     * @throws ezcAuthenticationTypekeyPublicKeysInvalidException
     *         if the keys fetched from the TypeKey public keys file are invalid
     * @param string $file The public keys file or URL
     * @return array(string=>string)
     */
    protected function fetchPublicKeys( $file )
    {
        // suppress warnings caused by file_get_contents() if $file could not be opened
        $data = @file_get_contents( $file );
        if ( empty( $data ) )
        {
            throw new ezcAuthenticationTypekeyPublicKeysMissingException( "Could not fetch public keys from '{$file}'." );
        }
        $lines = explode( ' ', trim( $data ) );
        foreach ( $lines as $line )
        {
            $val = explode( '=', $line );
            if ( count( $val ) < 2 )
            {
                throw new ezcAuthenticationTypekeyPublicKeysInvalidException( "The data retrieved from '{$file}' is invalid." );
            }
            $keys[$val[0]] = $val[1];
        }
        return $keys;
    }

    /**
     * Registers the extra data which will be fetched by the filter during the
     * authentication process.
     *
     * For TypeKey there is no registration needed, because all the possible
     * extra data is available in the response sent by the TypeKey server. So
     * a call to this function is not needed.
     *
     * To be able to get the email address of the user, need_email must be set
     * to a value different than 0 in the initial request sent to the TypeKey
     * server (along with the t and _return values).
     *
     * @param array(string) $data A list of attributes to fetch during authentication
     */
    public function registerFetchData( array $data = array() )
    {
        // does not need to do anything because all the extra data is returned by default
    }

    /**
     * Returns the extra data which was fetched during the authentication process.
     *
     * The TypeKey extra data is an array containing the values for name (the
     * TypeKey username), nick (the TypeKey display name) and email (the email
     * address of the user, fetched only if the initial request to the TypeKey
     * server contains need_email, and the user allowed the sharing of his email
     * address).
     *
     * Example of returned array:
     * <code>
     * array( 'name' => array( 'john' ),
     *        'nick' => array( 'John Doe' ),
     *        'email' => array( 'john.doe@example.com' ) // or not set
     *      );
     * </code>
     *
     * @return array(string=>mixed)
     */
    public function fetchData()
    {
        return $this->data;
    }
}
?>
