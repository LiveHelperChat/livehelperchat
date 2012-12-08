<?php
/**
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.0
 * @filesource
 * @package MvcAuthenticationTiein
 */

/**
 * This class provides a filter that applications can use for authentication.
 *
 * Through the $options argument to the controller you can configure many
 * aspects of this filter. By default a few settings are made that setup table
 * names and fields names, as well as names of variables under which certain
 * bits of the filter will be available in the controller's actions. The filter
 * needs to be integrated in both the applications configuration (through an
 * implementation of ezcMvcDispatcherConfiguration) as well as in the
 * controller handling authentication actions. Please refer to the tutorial on
 * how to do this.
 *
 * Some of the defaults provided in this filter can easily be changed by
 * inheriting. For example if you want to change the password generation
 * algorithm you need to override generatePassword(). Another example is when
 * you want other authentication methods besides session and database. In that
 * case setupAuth() needs to be overridden in an inherited class.
 *
 * @package MvcAuthenticationTiein
 * @version 1.0
 * @mainclass
 */
class ezcMvcAuthenticationFilter
{
    /**
     * Contains the filter options object
     *
     * @var ezcMvcAuthenticationFilterOptions
     */
    private $options;

    /**
     * Constructs a new ezcMvcAuthenticationFilter object
     *
     * @param ezcMvcAuthenticationFilterOptions $options
     */
    function __construct( ezcMvcAuthenticationFilterOptions $options = null )
    {
        $this->options = $options === null ? new ezcMvcAuthenticationFilterOptions() : $options;
    }

    /**
     * Sets a new options object
     *
     * @param ezcMvcAuthenticationFilterOptions $options
     */
    public function setOptions( ezcMvcAuthenticationFilterOptions $options )
    {
        $this->options = $options;
    }

    /**
     * Returns the currently set options
     *
     * @return ezcMvcAuthenticationFilterOptions
     */
    public function getOptions()
    {
        return $this->options;
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
                break;
        }
        throw new ezcBasePropertyNotFoundException( $name );
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
                if ( !( $value instanceof ezcMvcAuthenticationFilterOptions ) )
                {
                    throw new ezcBaseValueException( 'options', $value, 'instanceof ezcMvcAuthenticationFilterOptions' );
                }
                $this->options = $value;
                break;

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
     * This method sets up the authentication mechanism.
     *
     * By default it uses database and session storage only. If you want to do
     * more complex things, the best way would be to inherit from this class
     * and override this method. It takes a user name and password, but those
     * can be empty if your overridden class does not require them. This method
     * will also be called with $user and $password being NULL in case the
     * filter needs to check whether a user is already logged in. In this case,
     * the session should be checked.
     *
     * @param string $user
     * @param string $password
     *
     * @return ezcAuthentication
     */
    protected function setupAuth( $user = null, $password = null )
    {
        $database = new ezcAuthenticationDatabaseInfo(
            $this->options->database,
            $this->options->tableName,
            array( $this->options->userIdField, $this->options->passwordField )
        );
        $databaseFilter = new ezcAuthenticationDatabaseFilter( $database );

        // use the options object when creating a new Session object
        $options = new ezcAuthenticationSessionOptions();
        $options->validity = 86400;
        $options->idKey = $this->options->sessionUserIdKey;
        $options->timestampKey = $this->options->sessionTimestampKey;

        $session = new ezcAuthenticationSession( $options );
        $session->start();

        if ( $user === null )
        {
            $user = $session->load();
            $password = null;
        }

        $credentials = new ezcAuthenticationPasswordCredentials( $user, $this->hashPassword( $password ) );
        $authentication = new ezcAuthentication( $credentials );
        $authentication->session = $session;
        $authentication->addFilter( $databaseFilter );

        return $authentication;
    }

    /**
     * Returns the username associated with the user ID as stored in the session.
     *
     * This method could be a likely candidate to override as well, although it
     * is as configurable as possible. If usernames are not stored in the
     * database, this method needs to be overridden as well. The method's
     * return value is used by the setVars() method to add user ID and user
     * name to the session so that the application can use this data.
     *
     * @return string
     */
    protected function fetchUserName()
    {
        if ( isset( $_SESSION[$this->options->sessionUserIdKey] ) )
        {
            $q = ezcDbInstance::get()->createSelectQuery();
            $q->select( '*' )
              ->from( $this->options->tableName )
              ->where( $q->expr->eq( $this->options->userIdField, $q->bindValue( $_SESSION[$this->options->sessionUserIdKey] ) ) );
            $s = $q->prepare();
            $s->execute();
            $r = $s->fetchAll();

            return $r[0][$this->options->userNameField];
        }
        return null;
    }

    /**
     * This method sets the user ID and user name variables as part of the
     * $request and $result objects.
     *
     * This method should be called by the application's runRequestFilters()
     * and runResultFilters() methods to add authentication information to the
     * request and/or result. The method also makes the authentication filter
     * available to the controller actions so it is important that it is called
     * in both filters, and preferably as the first method call.
     *
     * The variable names that contain the user ID and user name can be
     * configured through the $options object that is passed to the contructor.
     *
     * @param ezcMvcRequest|ezcMvcResult
     */
    public function setVars( $requestOrResult )
    {
        $requestOrResult->variables[$this->options->varNameFilter] = $this;
        if ( isset( $_SESSION[$this->options->sessionUserIdKey] ) )
        {
            $requestOrResult->variables[$this->options->varNameUserId] = $_SESSION[$this->options->sessionUserIdKey];
            $requestOrResult->variables[$this->options->varNameUserName] = self::fetchUserName();
        }
    }

    /**
     * Sets up the authentication mechanism to be used for routes that do not require authentication.
     *
     * This method is meant to be run from the runRequestFilters() method for
     * the routes that do not require authentication or deal with logging in,
     * logging out and registering users. It sets up the session so that the
     * controller has access to the authentication data.
     *
     * @param ezcMvcRequest $request
     */
    public function runAuthCheckLoggedIn( ezcMvcRequest $request )
    {
        $auth = self::setupAuth();
        $auth->run();
    }

    /**
     * Sets up the authentication mechanism to be used for routes that do require authentication.
     *
     * This method is meant to be run from the runRequestFilters() method for
     * the routes that do require an authenticated user.  It sets up the
     * session so that the controller has access to the authentication data.
     * The method will return an internal redirect return that redirects to the
     * configured loginRequiredUri. That Uri's controller and action needs to
     * present the login form.
     *
     * @param ezcMvcRequest $request
     */
    public function runAuthRequiredFilter( $request )
    {
        $auth = self::setupAuth();
        if ( !$auth->run() )
        {
            $status = $auth->getStatus();
            $request->variables['ezcAuth_redirUrl'] = $request->uri;
            $request->variables['ezcAuth_reasons']  = $status;

            $request->uri = $this->options->loginRequiredUri;
            return new ezcMvcInternalRedirect( $request );
        }
    }

    /**
     * Method to be called from the controller's login action to log a user in.
     *
     * @param ezcMvcRequest $request
     * @param string        $user
     * @param string        $password
     */
    public function login( ezcMvcRequest $request, $user, $password )
    {
        return $this->setupAuth( $user, $password );
    }

    /**
     * Method to be called from the controller's logout action to log a user out.
     *
     * @param ezcMvcRequest $request
     */
    public function logout( ezcMvcRequest $request )
    {
        $options = new ezcAuthenticationSessionOptions();
        $options->validity = 86400;

        $session = new ezcAuthenticationSession( $options );
        $session->start();
        unset( $_SESSION[$this->options->sessionUserIdKey] );
        $session->destroy();
    }

    /**
     * Checks whether a user exists in the database.
     *
     * This method should be called from the "register" action to see if the
     * requested user ID has already been registered or not.
     *
     * @param string $username
     * @return bool
     */
    public function checkUserExists( $username )
    {
        $q = ezcDbInstance::get()->createSelectQuery();
        $q->select( $this->options->userIdField )
          ->from( $this->options->tableName )
          ->where( $q->expr->eq(
                $this->options->userIdField,
                $q->bindValue( $username ) )
            );
        $s = $q->prepare();
        $s->execute();
        $r = $s->fetchAll();

        return ( count( $r ) == 0 );
    }

    /**
     * Returns a generated basic password depending on the $username.
     *
     * This method is typically called from the "register" action after a user
     * ID has been checked for existance.
     *
     * @param string $username
     * @return string
     */
    public function generatePassword( $username )
    {
        // generate password
        mt_srand(
            base_convert( substr( md5( $username ), 0, 6 ), 36, 10 ) *
            microtime( true )
        );
        $a = base_convert( mt_rand(), 10, 36 );
        $b = base_convert( mt_rand(), 10, 36 );
        $password = substr( $b . $a, 1, 8 );

        return $password;
    }

    /**
     * Returns the hashed version of the clear text password
     *
     * @param string $password
     * @return string
     */
    protected function hashPassword( $password )
    {
        return md5( $password );
    }

    /**
     * Creates an entry in the user database table for $username and $password.
     *
     * This method creates a user in the configured user table (through the
     * options for this class). You can specify extra information as a
     * key->value pair array as $extraInfo.  This method does *not* check
     * whether a user already exists.
     *
     * @param string $username
     * @param string $password
     * @param array(string=>mixed) $extraInfo
     */
    public function createUser( $username, $password, array $extraInfo )
    {
        $q = ezcDbInstance::get()->createInsertQuery();
        $q->insertInto( $this->options->tableName )
          ->set( $this->options->userIdField, $q->bindValue( $username ) )
          ->set( $this->options->passwordField, $q->bindValue( $this->hashPassword( $password ) ) );
        foreach ( $extraInfo as $key => $value )
        {
            $q->set( $key, $q->bindValue( $value ) );
        }
        $s = $q->prepare();
        $s->execute();
    }

    /**
     * Checks the status from the authentication run and adds the reasons as
     * variable to the $result.
     *
     * This method uses the information that is set by the
     * runAuthRequiredFilter() filter to generate an user-readable text of the
     * found $reasons and sets these as the variable ezcAuth_reasons in
     * the $result. You can supply your own mapping from status codes to
     * messages, but a default is provided. Please refer to the Authentication
     * tutorial for information about status codes.
     *
     * @param ezcMvcResult $result
     * @param array(string) $reasons
     * @param array(string=>array(int=>string) $errorMap
     */
    function processLoginRequired( ezcMvcResult $res, $reasons, $errorMap = null )
    {
        $reasonText = array();

        if ( $errorMap === null )
        {
            $errorMap = array(
                'ezcAuthenticationDatabaseFilter' => array(
                    ezcAuthenticationHtpasswdFilter::STATUS_USERNAME_INCORRECT => 'Incorrect or no credentials provided.',
                    ezcAuthenticationHtpasswdFilter::STATUS_PASSWORD_INCORRECT => 'Incorrect or no credentials provided.'
                ),
                'ezcAuthenticationSession' => array(
                    ezcAuthenticationSession::STATUS_EMPTY => 'No session',
                    ezcAuthenticationSession::STATUS_EXPIRED => 'Session expired'
                ),
            );
        }

        foreach ( $reasons as $line )
        {
            list( $key, $value ) = each( $line );
            $reasonText[] = $errorMap[$key][$value];
        }
        $res->variables['ezcAuth_reasons']  = $reasonText;
    }

    /**
     * Returns either an internal or external redirect depending on whether the
     * user authenticated succesfully.
     *
     * This method is run from the "login" action just after login() has been
     * called. It takes the $authentication object, the $request and the form
     * provided $redirUrl. It redirects upon failure to the configured
     * loginRequiredUri and upon succes to the provided $redirUrl. The
     * redirection happens by returning an ezcMvcInternalRedirect or
     * ezcMvcResult with a ezcMvcExternalRedirect status.
     *
     * @param ezcAuthentication $authentication
     * @param ezcMvcRequest     $request
     * @param string            $redirUrl
     * @return ezcMvcInternalRedirect|ezcMvcResult
     */
    function returnLoginRedirect( ezcAuthentication $authentication, ezcMvcRequest $request, $redirUrl )
    {
        if ( !$authentication->run() )
        {
            $request = clone $request;
            $status = $authentication->getStatus();
            $request->variables['ezcAuth_redirUrl'] = $redirUrl;
            $request->variables['ezcAuth_reasons']  = $status;

            $request->uri = $this->options->loginRequiredUri;
            return new ezcMvcInternalRedirect( $request );
        }

        $res = new ezcMvcResult;
        $res->status = new ezcMvcExternalRedirect( $redirUrl );
        return $res;
    }

    /**
     * Returns an external redirect depending to the configured logoutUri.
     *
     * This method is run from the "logout" action just after logout() has been
     * called. It takes the $request object as parameter although this is not
     * used by this default implementation.  The method returns an
     * ezcMvcRequest result with a ezcMvcExternalRedirect status to redirect to
     * the configured logoutUri.
     *
     * @param ezcMvcRequest     $request
     * @return ezcMvcResult
     */
    function returnLogoutRedirect( ezcMvcRequest $request )
    {
        $res = new ezcMvcResult;
        $res->status = new ezcMvcExternalRedirect( $this->options->logoutUri );
        return $res;
    }
}
?>
