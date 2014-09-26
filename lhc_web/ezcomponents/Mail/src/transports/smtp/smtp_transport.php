<?php
/**
 * File containing the ezcMailSmtpTransport class.
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This class implements the Simple Mail Transfer Protocol (SMTP)
 * with authentication support.
 *
 * The implementation supports most of the commands specified in:
 *  - {@link http://www.faqs.org/rfcs/rfc821.html RFC821 - SMTP}
 *  - {@link http://www.faqs.org/rfcs/rfc2554.html RFC2554 - SMTP Authentication}
 *  - {@link http://www.faqs.org/rfcs/rfc2831.html RFC2831 - DIGEST-MD5 Authentication}
 *  - {@link http://www.faqs.org/rfcs/rfc2195.html RFC2195 - CRAM-MD5 Authentication}
 *  - {@link http://davenport.sourceforge.net/ntlm.html NTLM Authentication}
 *
 * By default, the SMTP transport tries to login anonymously to the SMTP server
 * (if an empty username and password have been provided), or to authenticate
 * with the strongest method supported by the server (if username and password
 * have been provided). The default behaviour can be changed with the option
 * preferredAuthMethod (see {@link ezcMailSmtpTransportOptions}).
 *
 * If the preferred method is specified via options, only that authentication
 * method will be attempted on the SMTP server. If it fails, an exception will
 * be thrown.
 *
 * Supported authentication methods (from strongest to weakest):
 *  - DIGEST-MD5
 *  - CRAM-MD5
 *  - NTLM (requires the PHP mcrypt extension)
 *  - LOGIN
 *  - PLAIN
 *
 * Not all SMTP servers support these methods, and some SMTP servers don't
 * support authentication at all.
 *
 * Example send mail:
 * <code>
 * $mail = new ezcMailComposer();
 *
 * $mail->from = new ezcMailAddress( 'sender@example.com', 'Adrian Ripburger' );
 * $mail->addTo( new ezcMailAddress( 'receiver@example.com', 'Maureen Corley' ) );
 * $mail->subject = "This is the subject of the example mail";
 * $mail->plainText = "This is the body of the example mail.";
 * $mail->build();
 *
 * // Create a new SMTP transport object with an SSLv3 connection.
 * // The port will be 465 by default, use the 4th argument to change it.
 * // Username and password (2nd and 3rd arguments) are left blank, which means
 * // the mail host does not need authentication.
 * // The 5th parameter is the optional $options object.
 * $options = new ezcMailSmtpTransportOptions();
 * $options->connectionType = ezcMailSmtpTransport::CONNECTION_SSLV3;
 *
 * $transport = new ezcMailSmtpTransport( 'mailhost.example.com', '', '', null, $options );
 *
 * // Use the SMTP transport to send the created mail object
 * $transport->send( $mail );
 * </code>
 *
 * Example require NTLM authentication:
 * <code>
 * // Create an SMTP transport and demand NTLM authentication.
 * // Username and password must be specified, otherwise no authentication
 * // will be attempted.
 * // If NTLM authentication fails, an exception will be thrown.
 * $options = new ezcMailSmtpTransportOptions();
 * $options->preferredAuthMethod = ezcMailSmtpTransport::AUTH_NTLM;
 *
 * $transport = new ezcMailSmtpTransport( 'mailhost.example.com', 'username', 'password', null, $options );
 *
 * // The option can also be specified via the option property:
 * $transport->options->preferredAuthMethod = ezcMailSmtpTransport::AUTH_NTLM;
 * </code>
 *
 * See {@link ezcMailSmtpTransportOptions} for options you can specify for SMTP.
 *
 * @property string $serverHost
 *           The SMTP server host to connect to.
 * @property int $serverPort
 *           The port of the SMTP server. Defaults to 25.
 * @property string $username
 *           The username used for authentication. The default is blank which
 *           means no authentication.
 * @property string $password
 *           The password used for authentication.
 * @property int $timeout
 *           The timeout value of the connection in seconds. The default is
 *           5 seconds. When setting/getting this option, the timeout option
 *           from $this->options {@link ezcMailTransportOptions} will be set instead.
 * @property string $senderHost
 *           The hostname of the computer that sends the mail. The default is
 *           'localhost'.
 * @property ezcMailSmtpTransportOptions $options
 *           Holds the options you can set to the SMTP transport.
 *
 * @package Mail
 * @version 1.7.1
 * @mainclass
 */
class ezcMailSmtpTransport implements ezcMailTransport
{
    /**
     * Plain connection.
     */
    const CONNECTION_PLAIN = 'tcp';

    /**
     * SSL connection.
     */
    const CONNECTION_SSL = 'ssl';

    /**
     * SSLv2 connection.
     */
    const CONNECTION_SSLV2 = 'sslv2';

    /**
     * SSLv3 connection.
     */
    const CONNECTION_SSLV3 = 'sslv3';

    /**
     * TLS connection.
     */
    const CONNECTION_TLS = 'tls';

    /**
     * Authenticate with 'AUTH PLAIN'.
     */
    const AUTH_PLAIN = 'PLAIN';

    /**
     * Authenticate with 'AUTH LOGIN'.
     */
    const AUTH_LOGIN = 'LOGIN';

    /**
     * Authenticate with 'AUTH CRAM-MD5'.
     */
    const AUTH_CRAM_MD5 = 'CRAM-MD5';

    /**
     * Authenticate with 'AUTH DIGEST-MD5'.
     */
    const AUTH_DIGEST_MD5 = 'DIGEST-MD5';

    /**
     * Authenticate with 'AUTH NTLM'.
     */
    const AUTH_NTLM = 'NTLM';

    /**
     * No authentication method. Specifies that the transport should try to
     * authenticate using the methods supported by the SMTP server in their
     * decreasing strength order. If one method fails an exception will be
     * thrown.
     */
    const AUTH_AUTO = null;

    /**
     * The line-break characters to use.
     *
     * @access private
     */
    const CRLF = "\r\n";

    /**
     * We are not connected to a server.
     *
     * @access private
     */
    const STATUS_NOT_CONNECTED = 1;

    /**
     * We are connected to the server, but not authenticated.
     *
     * @access private
     */
    const STATUS_CONNECTED = 2;

    /**
     * We are connected to the server and authenticated.
     *
     * @access private
     */
    const STATUS_AUTHENTICATED = 3;

    /**
     * The connection to the SMTP server.
     *
     * @var resource
     */
    protected $connection;

    /**
     * Holds the connection status.
     *
     * $var int {@link STATUS_NOT_CONNECTED},
     *          {@link STATUS_CONNECTED} or
     *          {@link STATUS_AUTHENTICATED}.
     */
    protected $status;

    /**
     * True if authentication should be performed; otherwise false.
     *
     * This variable is set to true if a username is provided for login.
     *
     * @var bool
     */
    protected $doAuthenticate;

    /**
     * Holds if the connection should be kept open after sending a mail.
     *
     * @var bool
     */
    protected $keepConnection = false;

    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    protected $properties = array();

    /**
     * Holds the options of this class.
     *
     * @var ezcMailSmtpTransportOptions
     */
    protected $options;

    /**
     * Constructs a new ezcMailSmtpTransport.
     *
     * The constructor expects, at least, the hostname $host of the SMTP server.
     *
     * The username $user will be used for authentication if provided.
     * If it is left blank no authentication will be performed.
     *
     * The password $password will be used for authentication
     * if provided. Use this parameter always in combination with the $user
     * parameter.
     *
     * The value $port specifies on which port to connect to $host. By default
     * it is 25 for plain connections and 465 for TLS/SSL/SSLv2/SSLv3.
     *
     * Note: The ssl option from {@link ezcMailTransportOptions} doesn't apply to SMTP.
     * If you want to connect to SMTP using TLS/SSL/SSLv2/SSLv3 use the connectionType
     * option in {@link ezcMailSmtpTransportOptions}.
     *
     * For options you can specify for SMTP see {@link ezcMailSmtpTransportOptions}.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if $options contains a property not defined
     * @throws ezcBaseValueException
     *         if $options contains a property with a value not allowed
     * @param string $host
     * @param string $user
     * @param string $password
     * @param int $port
     * @param ezcMailSmtpTransportOptions|array(string=>mixed) $options
     */
    public function __construct( $host, $user = '', $password = '', $port = null, $options = array() )
    {
        if ( $options instanceof ezcMailSmtpTransportOptions )
        {
            $this->options = $options;
        }
        else if ( is_array( $options ) )
        {
            $this->options = new ezcMailSmtpTransportOptions( $options );
        }
        else
        {
            throw new ezcBaseValueException( "options", $options, "ezcMailSmtpTransportOptions|array" );
        }

        $this->serverHost = $host;
        if ( $port === null )
        {
            $port = ( $this->options->connectionType === self::CONNECTION_PLAIN ) ? 25 : 465;
        }
        $this->serverPort = $port;
        $this->user = $user;
        $this->password = $password;
        $this->doAuthenticate = $user != '' ? true : false;

        $this->status = self::STATUS_NOT_CONNECTED;
        $this->senderHost = 'localhost';
    }

    /**
     * Destructs this object.
     *
     * Closes the connection if it is still open.
     */
    public function __destruct()
    {
        if ( $this->status != self::STATUS_NOT_CONNECTED )
        {
            $this->sendData( 'QUIT' );
            fclose( $this->connection );
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
            case 'user':
            case 'password':
            case 'senderHost':
            case 'serverHost':
            case 'serverPort':
                $this->properties[$name] = $value;
                break;

            case 'timeout':
                // the timeout option from $this->options is used instead of
                // the timeout option of this class
                $this->options->timeout = $value;
                break;

            case 'options':
                if ( !( $value instanceof ezcMailSmtpTransportOptions ) )
                {
                    throw new ezcBaseValueException( 'options', $value, 'instanceof ezcMailSmtpTransportOptions' );
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
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'user':
            case 'password':
            case 'senderHost':
            case 'serverHost':
            case 'serverPort':
                return $this->properties[$name];

            case 'timeout':
                return $this->options->timeout;

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
            case 'user':
            case 'password':
            case 'senderHost':
            case 'serverHost':
            case 'serverPort':
                return isset( $this->properties[$name] );

            case 'timeout':
            case 'options':
                return true;

            default:
                return false;
        }
    }

    /**
     * Sets if the connection should be kept open after sending an email.
     *
     * This method should be called prior to the first call to send().
     *
     * Keeping the connection open is useful if you are sending a lot of mail.
     * It removes the overhead of opening the connection after each mail is
     * sent.
     *
     * Use disconnect() to close the connection if you have requested to keep
     * it open.
     */
    public function keepConnection()
    {
        $this->keepConnection = true;
    }

    /**
     * Sends the ezcMail $mail using the SMTP protocol.
     *
     * If you want to send several emails use keepConnection() to leave the
     * connection to the server open between each mail.
     *
     * @throws ezcMailTransportException
     *         if the mail could not be sent
     * @throws ezcBaseFeatureNotFoundException
     *         if trying to use SSL and the openssl extension is not installed
     * @param ezcMail $mail
     */
    public function send( ezcMail $mail )
    {
        // sanity check the e-mail
        // need at least one recepient
        if ( ( count( $mail->to ) + count( $mail->cc ) + count( $mail->bcc ) ) < 1 )
        {
            throw new ezcMailTransportException( "Can not send e-mail with no 'to' recipients." );
        }

        try
        {
            // open connection unless we are connected already.
            if ( $this->status != self::STATUS_AUTHENTICATED )
            {
                $this->connect();
            }

            if ( isset( $mail->returnPath ) )
            {
                $this->cmdMail( $mail->returnPath->email );
            }
            else
            {
                $this->cmdMail( $mail->from->email );
            }

            // each recepient must be listed here.
            // this controls where the mail is actually sent as SMTP does not
            // read the headers itself
            foreach ( $mail->to as $address )
            {
                $this->cmdRcpt( $address->email );
            }
            foreach ( $mail->cc as $address )
            {
                $this->cmdRcpt( $address->email );
            }
            foreach ( $mail->bcc as $address )
            {
                $this->cmdRcpt( $address->email );
            }
            // done with the from and recipients, lets send the mail itself
            $this->cmdData();

            // A '.' on a line ends the mail. Make sure this does not happen in
            // the data we want to send.  also called transparancy in the RFC,
            // section 4.5.2
            $data = $mail->generate();
            $data = str_replace( self::CRLF . '.', self::CRLF . '..', $data );
            if ( $data[0] == '.' )
            {
                $data = '.' . $data;
            }

            $this->sendData( $data );
            $this->sendData( '.' );

            if ( $this->getReplyCode( $error ) !== '250' )
            {
                throw new ezcMailTransportSmtpException( "Error: {$error}" );
            }
        }
        catch ( ezcMailTransportSmtpException $e )
        {
            throw new ezcMailTransportException( $e->getMessage() );
            // TODO: reset connection here.pin
        }

        // close connection unless we should keep it
        if ( $this->keepConnection === false )
        {
            try
            {
                $this->disconnect();
            }
            catch ( Exception $e )
            {
                // Eat! We don't care anyway since we are aborting the connection
            }
        }
    }

    /**
     * Creates a connection to the SMTP server and initiates the login
     * procedure.
     *
     * @todo The @ should be removed when PHP doesn't throw warnings for connect problems
     *
     * @throws ezcMailTransportSmtpException
     *         if no connection could be made
     *         or if the login failed
     * @throws ezcBaseExtensionNotFoundException
     *         if trying to use SSL and the openssl extension is not installed
     */
    protected function connect()
    {
        $errno = null;
        $errstr = null;
        if ( $this->options->connectionType !== self::CONNECTION_PLAIN &&
             !ezcBaseFeatures::hasExtensionSupport( 'openssl' ) )
        {
            throw new ezcBaseExtensionNotFoundException( 'openssl', null, "PHP not configured --with-openssl." );
        }
        if ( count( $this->options->connectionOptions ) > 0 )
        {
            $context = stream_context_create( $this->options->connectionOptions );
            $this->connection = @stream_socket_client( "{$this->options->connectionType}://{$this->serverHost}:{$this->serverPort}",
                                                       $errno, $errstr, $this->options->timeout, STREAM_CLIENT_CONNECT, $context );
        }
        else
        {
            $this->connection = @stream_socket_client( "{$this->options->connectionType}://{$this->serverHost}:{$this->serverPort}",
                                                       $errno, $errstr, $this->options->timeout );
        }

        if ( is_resource( $this->connection ) )
        {
            stream_set_timeout( $this->connection, $this->options->timeout );
            $this->status = self::STATUS_CONNECTED;
            $greeting = $this->getData();
            $this->login();
        }
        else
        {
            throw new ezcMailTransportSmtpException( "Failed to connect to the smtp server: {$this->serverHost}:{$this->serverPort}." );
        }
    }

    /**
     * Performs the initial handshake with the SMTP server and
     * authenticates the user, if login data is provided to the
     * constructor.
     *
     * @throws ezcMailTransportSmtpException
     *         if the HELO/EHLO command or authentication fails
     */
    protected function login()
    {
        if ( $this->doAuthenticate )
        {
            $this->sendData( 'EHLO ' . $this->senderHost );
        }
        else
        {
            $this->sendData( 'HELO ' . $this->senderHost );
        }

        if ( $this->getReplyCode( $response ) !== '250' )
        {
            throw new ezcMailTransportSmtpException( "HELO/EHLO failed with error: {$response}." );
        }

        // do authentication
        if ( $this->doAuthenticate )
        {
            if ( $this->options->preferredAuthMethod !== self::AUTH_AUTO )
            {
                $this->auth( $this->options->preferredAuthMethod );
            }
            else
            {
                preg_match( "/250-AUTH[= ](.*)/", $response, $matches );
                if ( count( $matches ) > 0 )
                {
                    $methods = explode( ' ', trim( $matches[1] ) );
                }
                if ( count( $matches ) === 0 || count( $methods ) === 0 )
                {
                    throw new ezcMailTransportSmtpException( 'SMTP server does not accept the AUTH command.' );
                }

                $authenticated = false;
                $methods = $this->sortAuthMethods( $methods );
                foreach ( $methods as $method )
                {
                    if ( $this->auth( $method ) === true )
                    {
                        $authenticated = true;
                        break;
                    }
                }

                if ( $authenticated === false )
                {
                    throw new ezcMailTransportSmtpException( 'SMTP server did not respond correctly to any of the authentication methods ' . implode( ', ', $methods ) . '.' );
                }
            }
        }
        $this->status = self::STATUS_AUTHENTICATED;
    }

    /**
     * Returns an array with the authentication methods supported by the
     * SMTP transport class (not by the SMTP server!).
     *
     * The returned array has the methods sorted by their relative strengths,
     * so stronger methods are first in the array.
     *
     * @return array(string)
     */
    public static function getSupportedAuthMethods()
    {
        return array(
            ezcMailSmtpTransport::AUTH_DIGEST_MD5,
            ezcMailSmtpTransport::AUTH_CRAM_MD5,
            ezcMailSmtpTransport::AUTH_NTLM,
            ezcMailSmtpTransport::AUTH_LOGIN,
            ezcMailSmtpTransport::AUTH_PLAIN,
            );
    }

    /**
     * Sorts the specified array of AUTH methods $methods by strength, so higher
     * strength methods will be used first.
     *
     * For example, if the server supports:
     * <code>
     *   $methods = array( 'PLAIN', 'LOGIN', 'CRAM-MD5' );
     * </code>
     *
     * then this method will return:
     * <code>
     *   $methods = array( 'CRAM-MD5', 'LOGIN', 'PLAIN' );
     * </code>
     *
     * @param array(string) $methods
     * @return array(string)
     */
    protected function sortAuthMethods( array $methods )
    {
        $result = array();
        $unsupported = array();
        $supportedAuthMethods = self::getSupportedAuthMethods();
        foreach ( $supportedAuthMethods as $method )
        {
            if ( in_array( $method, $methods ) )
            {
                $result[] = $method;
            }
        }
        return $result;
    }

    /**
     * Calls the appropiate authentication method based on $method.
     *
     * @throws ezcMailTransportSmtpException
     *         if $method is not supported by the transport class
     * @return bool
     */
    protected function auth( $method )
    {
        switch ( $method )
        {
            case self::AUTH_DIGEST_MD5:
                $authenticated = $this->authDigestMd5();
                break;

            case self::AUTH_CRAM_MD5:
                $authenticated = $this->authCramMd5();
                break;

            case self::AUTH_NTLM:
                $authenticated = $this->authNtlm();
                break;

            case self::AUTH_LOGIN:
                $authenticated = $this->authLogin();
                break;

            case self::AUTH_PLAIN:
                $authenticated = $this->authPlain();
                break;

            default:
                throw new ezcMailTransportSmtpException( "Unsupported AUTH method '{$method}'." );
        }

        return $authenticated;
    }

    /**
     * Tries to login to the SMTP server with 'AUTH DIGEST-MD5' and returns true if
     * successful.
     *
     * @todo implement auth-int and auth-conf quality of protection (qop) modes
     * @todo support other algorithms than md5-sess?
     *
     * @throws ezcMailTransportSmtpException
     *         if the SMTP server returned an error
     * @return bool
     */
    protected function authDigestMd5()
    {
        $this->sendData( 'AUTH DIGEST-MD5' );
        if ( $this->getReplyCode( $serverResponse ) !== '334' )
        {
            throw new ezcMailTransportSmtpException( 'SMTP server does not accept AUTH DIGEST-MD5.' );
        }

        $serverDigest = base64_decode( trim( substr( $serverResponse, 4 ) ) );
        $parts = explode( ',', $serverDigest );
        foreach ( $parts as $part )
        {
            $args = explode( '=', $part, 2 );
            $params[trim( $args[0] )] = trim( $args[1] );
        }

        if ( !isset( $params['nonce'] ) ||
             !isset( $params['algorithm'] ) )
        {
            throw new ezcMailTransportSmtpException( 'SMTP server did not send a correct DIGEST-MD5 challenge.' );
        }

        $nonce = trim( $params['nonce'], '"' );
        $algorithm = trim( $params['algorithm'], '"' );

        $qop = 'auth';
        $realm = isset( $params['realm'] ) ? trim( $params['realm'], '"' ) : $this->serverHost;
        $cnonce = $this->generateNonce( 32 );
        $digestUri = "smtp/{$this->serverHost}";
        $nc = '00000001';
        $charset = isset( $params['charset'] ) ? trim( $params['charset'], '"' ) : 'utf-8';
        $maxbuf = isset( $params['maxbuf'] ) ? trim( $params['maxbuf'], '"' ) : 65536;

        $response = '';
        $A2 = "AUTHENTICATE:{$digestUri}";
        $A1 = pack( 'H32', md5( "{$this->user}:{$realm}:{$this->password}" ) ) . ":{$nonce}:{$cnonce}";
        $response = md5( md5( $A1 ) . ":{$nonce}:{$nc}:{$cnonce}:{$qop}:" . md5( $A2 ) );

        $loginParams = array(
            'username' => "\"{$this->user}\"",
            'cnonce' => "\"{$cnonce}\"",
            'nonce' => "\"{$nonce}\"",
            'nc' => $nc,
            'qop' => $qop,
            'digest-uri' => "\"{$digestUri}\"",
            'charset' => $charset,
            'realm' => "\"{$realm}\"",
            'response' => $response,
            'maxbuf' => $maxbuf
            );

        $parts = array();
        foreach ( $loginParams as $key => $value )
        {
            $parts[] = "{$key}={$value}";
        }
        $login = base64_encode( implode( ',', $parts ) );

        $this->sendData( $login );
        if ( $this->getReplyCode( $serverResponse ) !== '334' )
        {
            throw new ezcMailTransportSmtpException( 'SMTP server did not accept the provided username and password.' );
        }

        $serverResponse = base64_decode( trim( substr( $serverResponse, 4 ) ) );
        $parts = explode( '=', $serverResponse );
        $rspauthServer = trim( $parts[1] );

        $A2 = ":{$digestUri}";
        $rspauthClient = md5( md5( $A1 ) . ":{$nonce}:{$nc}:{$cnonce}:{$qop}:" . md5( $A2 ) );

        if ( $rspauthServer !== $rspauthClient )
        {
            throw new ezcMailTransportSmtpException( 'SMTP server did not responded correctly to the DIGEST-MD5 authentication.' );
        }

        $this->sendData( '' );
        if ( $this->getReplyCode( $serverResponse ) !== '235' )
        {
            throw new ezcMailTransportSmtpException( 'SMTP server did not allow DIGEST-MD5 authentication.' );
        }

        return true;
    }

    /**
     * Tries to login to the SMTP server with 'AUTH CRAM-MD5' and returns true if
     * successful.
     *
     * @throws ezcMailTransportSmtpException
     *         if the SMTP server returned an error
     * @return bool
     */
    protected function authCramMd5()
    {
        $this->sendData( 'AUTH CRAM-MD5' );
        if ( $this->getReplyCode( $response ) !== '334' )
        {
            throw new ezcMailTransportSmtpException( 'SMTP server does not accept AUTH CRAM-MD5.' );
        }

        $serverDigest = trim( substr( $response, 4 ) );
        $clientDigest = hash_hmac( 'md5', base64_decode( $serverDigest ), $this->password );
        $login = base64_encode( "{$this->user} {$clientDigest}" );

        $this->sendData( $login );
        if ( $this->getReplyCode( $error ) !== '235' )
        {
            throw new ezcMailTransportSmtpException( 'SMTP server did not accept the provided username and password.' );
        }

        return true;
    }

    /**
     * Tries to login to the SMTP server with 'AUTH NTLM' and returns true if
     * successful.
     *
     * @throws ezcMailTransportSmtpException
     *         if the SMTP server returned an error
     * @return bool
     */
    protected function authNtlm()
    {
        if ( !ezcBaseFeatures::hasExtensionSupport( 'mcrypt' ) )
        {
            throw new ezcBaseExtensionNotFoundException( 'mcrypt', null, "PHP not compiled with --with-mcrypt." );
        }

        // Send NTLM type 1 message
        $msg1 = base64_encode( $this->authNtlmMessageType1( $this->senderHost, $this->serverHost ) );

        $this->sendData( "AUTH NTLM {$msg1}" );
        if ( $this->getReplyCode( $serverResponse ) !== '334' )
        {
            throw new ezcMailTransportSmtpException( 'SMTP server does not accept AUTH NTLM.' );
        }

        // Parse NTLM type 2 message
        $msg2 = base64_decode( trim( substr( $serverResponse, 4 ) ) );
        $parts = array(
                        substr( $msg2, 0, 8 ),  // Signature ("NTLMSSP\0")
                        substr( $msg2, 8, 4 ),  // Message type
                        substr( $msg2, 12, 8 ), // Target name (security buffer)
                        substr( $msg2, 20, 4 ), // Flags
                        substr( $msg2, 24, 8 ), // Challenge
                        substr( $msg2, 32 )     // The rest of information
                      );

        $challenge = $parts[4];

        // Send NTLM type 3 message
        $msg3 = base64_encode( $this->authNtlmMessageType3( $challenge, $this->user, $this->password, $this->senderHost, $this->serverHost ) );

        $this->sendData( $msg3 );
        if ( $this->getReplyCode( $serverResponse ) !== '235' )
        {
            throw new ezcMailTransportSmtpException( 'SMTP server did not allow NTLM authentication.' );
        }
    }

    /**
     * Tries to login to the SMTP server with 'AUTH LOGIN' and returns true if
     * successful.
     *
     * @throws ezcMailTransportSmtpException
     *         if the SMTP server returned an error
     * @return bool
     */
    protected function authLogin()
    {
        $this->sendData( 'AUTH LOGIN' );
        if ( $this->getReplyCode( $error ) !== '334' )
        {
            throw new ezcMailTransportSmtpException( 'SMTP server does not accept AUTH LOGIN.' );
        }

        $this->sendData( base64_encode( $this->user ) );
        if ( $this->getReplyCode( $error ) !== '334' )
        {
            throw new ezcMailTransportSmtpException( "SMTP server did not accept login: {$this->user}." );
        }

        $this->sendData( base64_encode( $this->password ) );
        if ( $this->getReplyCode( $error ) !== '235' )
        {
            throw new ezcMailTransportSmtpException( 'SMTP server did not accept the provided username and password.' );
        }

        return true;
    }

    /**
     * Tries to login to the SMTP server with 'AUTH PLAIN' and returns true if
     * successful.
     *
     * @throws ezcMailTransportSmtpException
     *         if the SMTP server returned an error
     * @return bool
     */
    protected function authPlain()
    {
        $digest = base64_encode( "\0{$this->user}\0{$this->password}" );
        $this->sendData( "AUTH PLAIN {$digest}" );
        if ( $this->getReplyCode( $error ) !== '235' )
        {
            throw new ezcMailTransportSmtpException( 'SMTP server did not accept the provided username and password.' );
        }

        return true;
    }

    /**
     * Sends the QUIT command to the server and breaks the connection.
     *
     * @throws ezcMailTransportSmtpException
     *         if the QUIT command failed
     */
    public function disconnect()
    {
        if ( $this->status != self::STATUS_NOT_CONNECTED )
        {
            $this->sendData( 'QUIT' );
            $replyCode = $this->getReplyCode( $error ) !== '221';
            fclose( $this->connection );
            $this->status = self::STATUS_NOT_CONNECTED;
            if ( $replyCode )
            {
                throw new ezcMailTransportSmtpException( "QUIT failed with error: $error." );
            }
        }
    }

    /**
     * Returns the $email enclosed within '< >'.
     *
     * If $email is already enclosed within '< >' it is returned unmodified.
     *
     * @param string $email
     * $return string
     */
    protected function composeSmtpMailAddress( $email )
    {
        if ( !preg_match( "/<.+>/", $email ) )
        {
            $email = "<{$email}>";
        }
        return $email;
    }

    /**
     * Sends the MAIL FROM command, with the sender's mail address $from.
     *
     * This method must be called once to tell the server the sender address.
     *
     * The sender's mail address $from may be enclosed in angle brackets.
     *
     * @throws ezcMailTransportSmtpException
     *         if there is no valid connection
     *         or if the MAIL FROM command failed
     * @param string $from
     */
    protected function cmdMail( $from )
    {
        if ( $this->status === self::STATUS_AUTHENTICATED )
        {
            $this->sendData( 'MAIL FROM:' . $this->composeSmtpMailAddress( $from ) . '' );
            if ( $this->getReplyCode( $error ) !== '250' )
            {
                throw new ezcMailTransportSmtpException( "MAIL FROM failed with error: $error." );
            }
        }
    }

    /**
     * Sends the 'RCTP TO' to the server with the address $email.
     *
     * This method must be called once for each recipient of the mail
     * including cc and bcc recipients. The RCPT TO commands control
     * where the mail is actually sent. It does not affect the headers
     * of the email.
     *
     * The recipient mail address $email may be enclosed in angle brackets.
     *
     * @throws ezcMailTransportSmtpException
     *         if there is no valid connection
     *         or if the RCPT TO command failed
     * @param string $email
     */
    protected function cmdRcpt( $email )
    {
        if ( $this->status === self::STATUS_AUTHENTICATED )
        {
            $this->sendData( 'RCPT TO:' . $this->composeSmtpMailAddress( $email ) );
            if ( $this->getReplyCode( $error ) !== '250' )
            {
                throw new ezcMailTransportSmtpException( "RCPT TO failed with error: $error." );
            }
        }
    }

    /**
     * Sends the DATA command to the SMTP server.
     *
     * @throws ezcMailTransportSmtpException
     *         if there is no valid connection
     *         or if the DATA command failed
     */
    protected function cmdData()
    {
        if ( $this->status === self::STATUS_AUTHENTICATED )
        {
            $this->sendData( 'DATA' );
            if ( $this->getReplyCode( $error ) !== '354' )
            {
                throw new ezcMailTransportSmtpException( "DATA failed with error: $error." );
            }
        }
    }

    /**
     * Sends $data to the SMTP server through the connection.
     *
     * This method appends one line-break at the end of $data.
     *
     * @throws ezcMailTransportSmtpException
     *         if there is no valid connection
     * @param string $data
     */
    protected function sendData( $data )
    {
        if ( is_resource( $this->connection ) )
        {
            if ( fwrite( $this->connection, $data . self::CRLF,
                        strlen( $data ) + strlen( self::CRLF  ) ) === false )
            {
                throw new ezcMailTransportSmtpException( 'Could not write to SMTP stream. It was probably terminated by the host.' );
            }
        }
    }

    /**
     * Returns data received from the connection stream.
     *
     * @throws ezcMailTransportSmtpException
     *         if there is no valid connection
     * @return string
     */
    protected function getData()
    {
        $data = '';
        $line   = '';
        $loops  = 0;

        if ( is_resource( $this->connection ) )
        {
            while ( ( strpos( $data, self::CRLF ) === false || (string) substr( $line, 3, 1 ) !== ' ' ) && $loops < 100 )
            {
                $line = fgets( $this->connection, 512 );
                $data .= $line;
                $loops++;
            }
            return $data;
        }
        throw new ezcMailTransportSmtpException( 'Could not read from SMTP stream. It was probably terminated by the host.' );
    }

    /**
     * Returns the reply code of the last message from the server.
     *
     * $line contains the complete data retrieved from the stream. This can be used to retrieve
     * the error message in case of an error.
     *
     * @throws ezcMailTransportSmtpException
     *         if it could not fetch data from the stream
     * @param string &$line
     * @return string
     */
    protected function getReplyCode( &$line )
    {
        return substr( trim( $line = $this->getData() ), 0, 3 );
    }

    /**
     * Generates an alpha-numeric random string with the specified $length.
     *
     * Used in the DIGEST-MD5 authentication method.
     *
     * @param int $length
     * @return string
     */
    protected function generateNonce( $length = 32 )
    {
        $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $result = '';
        for ( $i = 0; $i < $length; $i++ )
        {
            $result .= $chars[mt_rand( 0, strlen( $chars ) - 1 )];
        }

        return $result;
    }

    /**
     * Generates an NTLM type 1 message.
     *
     * @param string $workstation
     * @param string $domain
     * @return string
     */
    protected function authNtlmMessageType1( $workstation, $domain )
    {
        $parts = array(
                        "NTLMSSP\x00",
                        "\x01\x00\x00\x00",
                        "\x07\x32\x00\x00",
                        $this->authNtlmSecurityBuffer( $domain, 32 + strlen( $workstation ) ),
                        $this->authNtlmSecurityBuffer( $workstation, 32 ),
                        $workstation,
                        $domain
                      );

        return implode( "", $parts );
    }

    /**
     * Generates an NTLM type 3 message from the $challenge sent by the SMTP
     * server in an NTLM type 2 message.
     *
     * @param string $challenge
     * @param string $user
     * @param string $password
     * @param string $workstation
     * @param string $domain
     * @return string
     */
    protected function authNtlmMessageType3( $challenge, $user, $password, $workstation, $domain )
    {
        $domain = chunk_split( $domain, 1, "\x00" );
        $user = chunk_split( $user, 1, "\x00" );
        $workstation = chunk_split( $workstation, 1, "\x00" );
        $lm = '';
        $ntlm = $this->authNtlmResponse( $challenge, $password );
        $session = '';

        $domainOffset = 64;
        $userOffset = $domainOffset + strlen( $domain );
        $workstationOffset = $userOffset + strlen( $user );
        $lmOffset = $workstationOffset + strlen( $workstation );
        $ntlmOffset = $lmOffset + strlen( $lm );
        $sessionOffset = $ntlmOffset + strlen( $ntlm );

        $parts = array(
                        "NTLMSSP\x00",
                        "\x03\x00\x00\x00",
                        $this->authNtlmSecurityBuffer( $lm, $lmOffset ),
                        $this->authNtlmSecurityBuffer( $ntlm, $ntlmOffset ),
                        $this->authNtlmSecurityBuffer( $domain, $domainOffset ),
                        $this->authNtlmSecurityBuffer( $user, $userOffset ),
                        $this->authNtlmSecurityBuffer( $workstation, $workstationOffset ),
                        $this->authNtlmSecurityBuffer( $session, $sessionOffset ),
                        "\x01\x02\x00\x00",
                        $domain,
                        $user,
                        $workstation,
                        $lm,
                        $ntlm
                      );

        return implode( '', $parts );
    }

    /**
     * Calculates an NTLM response to be used in the creation of the NTLM type 3
     * message.
     *
     * @param string $challenge
     * @param string $password
     * @return string
     */
    protected function authNtlmResponse( $challenge, $password )
    {
        $password = chunk_split( $password, 1, "\x00" );
        $password = hash( 'md4', $password, true );
        $password .= str_repeat( "\x00", 21 - strlen( $password ) );

        $td = mcrypt_module_open( 'des', '', 'ecb', '' );
        $iv = mcrypt_create_iv( mcrypt_enc_get_iv_size( $td ), MCRYPT_RAND );

        $response = '';        
        for ( $i = 0; $i < 21; $i += 7 )
        {
            $packed = '';
            for ( $p = $i; $p < $i + 7; $p++ )
            {
                $packed .= str_pad( decbin( ord( substr( $password, $p, 1 ) ) ), 8, '0', STR_PAD_LEFT );
            }

            $key = '';
            for ( $p = 0; $p < strlen( $packed ); $p += 7 )
            {
                $s = substr( $packed, $p, 7 );
                $b = $s . ( ( substr_count( $s, '1' ) % 2 ) ? '0' : '1' );
                $key .= chr( bindec( $b ) );
            }

            mcrypt_generic_init( $td, $key, $iv );
            $response .= mcrypt_generic( $td, $challenge );
            mcrypt_generic_deinit( $td );
        }
        mcrypt_module_close( $td );

        return $response;
    }

    /**
     * Creates an NTLM security buffer information string.
     *
     * The structure of the security buffer is:
     *  - a short containing the length of the buffer content in bytes (may be
     *    zero).
     *  - a short containing the allocated space for the buffer in bytes (greater
     *    than or equal to the length; typically the same as the length).
     *  - a long containing the offset to the start of the buffer in bytes (from
     *    the beginning of the NTLM message).
     *
     * Example:
     *  - buffer content length: 1234 bytes (0xd204 in hexa)
     *  - allocated space: 1234 bytes( 0xd204 in hexa)
     *  - offset: 4321 bytes (0xe1100000 in hexa)
     *
     * then the security buffer would be 0xd204d204e1100000 (in hexa).
     *
     * @param string $text
     * @param int $offset
     * @return string
     */
    protected function authNtlmSecurityBuffer( $text, $offset )
    {
        return pack( 'v', strlen( $text ) ) .
               pack( 'v', strlen( $text ) ) .
               pack( 'V', $offset );
    }
}
?>
