<?php
/**
 * File containing the ezcAuthenticationGroupFilter class.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Group authentication filters together.
 *
 * If there are no filters in the group, then the run() method will return
 * STATUS_OK.
 *
 * The way of grouping the filters is specified with the mode option:
 *  - ezcAuthenticationGroupFilter::MODE_OR (default): at least one filter
 *    in the group needs to succeed in order for the group to succeed.
 *  - ezcAuthenticationGroupFilter::MODE_AND: all filters in the group
 *    need to succeed in order for the group to succeed.
 *
 * Example of using the mode option:
 * <code>
 * $options = new ezcAuthenticationGroupOptions();
 * $options->mode = ezcAuthenticationGroupFilter::MODE_AND;
 *
 * // $filter1 and $filter2 are authentication filters which all need to succeed
 * // in order for the group to succeed
 * $filter = new ezcAuthenticationGroupFilter( array( $filter1, $filter2 ), $options );
 * </code>
 *
 * Example of using the group filter with LDAP and Database filters:
 * <code>
 * $credentials = new ezcAuthenticationPasswordCredentials( 'jan.modaal', 'qwerty' );
 *
 * // create a database filter
 * $database = new ezcAuthenticationDatabaseInfo( ezcDbInstance::get(), 'users', array( 'user', 'password' ) );
 * $databaseFilter = new ezcAuthenticationDatabaseFilter( $database );
 *
 * // create an LDAP filter
 * $ldap = new ezcAuthenticationLdapInfo( 'localhost', 'uid=%id%', 'dc=example,dc=com', 389 );
 * $ldapFilter = new ezcAuthenticationLdapFilter( $ldap );
 * $authentication = new ezcAuthentication( $credentials );
 *
 * // use the database and LDAP filters in paralel (at least one needs to succeed in
 * // order for the user to be authenticated)
 * $authentication->addFilter( new ezcAuthenticationGroupFilter( array( $databaseFilter, $ldapFilter ) ) );
 * // add more filters if needed
 * if ( !$authentication->run() )
 * {
 *     // authentication did not succeed, so inform the user
 *     $status = $authentication->getStatus();
 *     $err = array(
 *             array( 'ezcAuthenticationLdapFilter' => array(
 *                 ezcAuthenticationLdapFilter::STATUS_USERNAME_INCORRECT => 'Incorrect username',
 *                 ezcAuthenticationLdapFilter::STATUS_PASSWORD_INCORRECT => 'Incorrect password'
 *                 ) ),
 *             array( 'ezcAuthenticationDatabaseFilter' => array(
 *                 ezcAuthenticationDatabaseFilter::STATUS_USERNAME_INCORRECT => 'Incorrect username',
 *                 ezcAuthenticationDatabaseFilter::STATUS_PASSWORD_INCORRECT => 'Incorrect password'
 *                 ) )
 *             );
 *     foreach ( $status as $line => $error )
 *     {
 *         list( $key, $value ) = each( $error );
 *         echo $err[$line][$key][$value] . "\n";
 *     }
 * }
 * else
 * {
 *     // authentication succeeded, so allow the user to see his content
 * }
 * </code>
 *
 * It is possible to use multiple credentials when grouping filters together, by
 * enabling the option multipleCredentials for the Group filter object. When this
 * option is enabled, each filter added to the group must have a credentials
 * object passed along with it.
 *
 * Example of using the Group filter to handle multiple credentials:
 * <code>
 * $credentials1 = new ezcAuthenticationPasswordCredentials( 'jan.modaal', 'b1b3773a05c0ed0176787a4f1574ff0075f7521e' ); // incorrect password
 * $credentials2 = new ezcAuthenticationPasswordCredentials( 'john.doe', 'wpeE20wyWHnLE' ); // correct username + password
 *
 * $options = new ezcAuthenticationGroupOptions();
 * $options->multipleCredentials = true;
 * $options->mode = ezcAuthenticationGroupFilter::MODE_AND;
 * $group = new ezcAuthenticationGroupFilter( array(), $options );
 *
 * $group->addFilter( new ezcAuthenticationHtpasswdFilter( '../../tests/filters/htpasswd/data/htpasswd' ), $credentials1 );
 * $group->addFilter( new ezcAuthenticationHtpasswdFilter( '../../tests/filters/htpasswd/data/htpasswd' ), $credentials2 );
 *
 * $authentication = new ezcAuthentication( $credentials1 );
 * $authentication->addFilter( $group );
 * // add more filters if needed
 *
 * if ( !$authentication->run() )
 * {
 *     // authentication did not succeed, so inform the user
 *     $status = $authentication->getStatus();
 *
 *     $err = array(
 *                 array( 'ezcAuthenticationHtpasswdFilter' => array(
 *                         ezcAuthenticationHtpasswdFilter::STATUS_OK => '',
 *                         ezcAuthenticationHtpasswdFilter::STATUS_USERNAME_INCORRECT => 'Incorrect username ' . $credentials1->id,
 *                         ezcAuthenticationHtpasswdFilter::STATUS_PASSWORD_INCORRECT => 'Incorrect password for ' . $credentials1->id
 *                         ) ),
 *
 *                 array( 'ezcAuthenticationHtpasswdFilter' => array(
 *                         ezcAuthenticationHtpasswdFilter::STATUS_OK => '',
 *                         ezcAuthenticationHtpasswdFilter::STATUS_USERNAME_INCORRECT => 'Incorrect username ' . $credentials2->id,
 *                         ezcAuthenticationHtpasswdFilter::STATUS_PASSWORD_INCORRECT => 'Incorrect password for ' . $credentials2->id
 *                         ) )
 *                 );
 *
 *     foreach ( $status as $line => $error )
 *     {
 *         list( $key, $value ) = each( $error );
 *         echo $err[$line][$key][$value] . "\n";
 *     }
 * }
 * else
 * {
 *     // authentication succeeded, so allow the user to see his content
 * }
 * </code>
 *
 * @property ezcAuthenticationStatus $status
 *           The status object which holds the status of the run filters.
 *
 * @package Authentication
 * @version 1.3.1
 * @mainclass
 */
class ezcAuthenticationGroupFilter extends ezcAuthenticationFilter
{
    /**
     * All or some of the filters in the group failed (depeding on the mode
     * option).
     */
    const STATUS_GROUP_FAILED = 1;

    /**
     * At least one filter needs to succeed in order for the group to succeed.
     */
    const MODE_OR = 1;

    /**
     * All the filters need to succeed in order for the group to succeed.
     */
    const MODE_AND = 2;

    /**
     * Authentication filters.
     * 
     * @var array(ezcAuthenticationFilter)
     */
    protected $filters = array();

    /**
     * The properties of this class.
     * 
     * @var array(string=>mixed)
     */
    private $properties = array();

    /**
     * Creates a new object of this class.
     *
     * The filters can be specified as an array of filter objects, or as an
     * array of array(fiter,credentials) when the multipleCredentials option is
     * enabled.
     *
     * Example of using multipleCredentials:
     * <code>
     * $credentials1 = new ezcAuthenticationPasswordCredentials( 'john.doe', '1234' );
     * $credentials1 = new ezcAuthenticationPasswordCredentials( 'jan.modaal', 'qwerty' );
     *
     * $filter1 = new ezcAuthenticationHtpasswdFilter( '/etc/htpasswd1' );
     * $filter2 = new ezcAuthenticationHtpasswdFilter( '/etc/htpasswd2' );
     *
     * // enable multiple credentials
     * $options = new ezcAuthenticationGroupOptions();
     * $options->multipleCredentials = true;
     *
     * // add the filters to the group with the constructor
     * $group = new ezcAuthenticationGroupFilter( array(
     *              array( $filter1, $credentials1 ),
     *              array( $filter2, $credentials2 ) ), $options );
     *
     * // the filters can also be added to the group with addFilter()
     * </code>
     *
     * @throws ezcAuthenticationException
     *         if the multipleCredentials option is enabled and a credentials
     *         object was not specified
     * @param array(ezcAuthenticationFilter|mixed) $filters Authentication filters
     * @param ezcAuthenticationGroupOptions $options Options for this class
     */
    public function __construct( array $filters, ezcAuthenticationGroupOptions $options = null )
    {
        $this->options = ( $options === null ) ? new ezcAuthenticationGroupOptions() : $options;

        foreach ( $filters as $filter )
        {
            if ( is_array( $filter ) )
            {
                if ( count( $filter ) > 1 )
                {
                    $this->addFilter( $filter[0], $filter[1] );
                }
                else
                {
                    $this->addFilter( $filter[0] );
                }
            }
            else
            {
                $this->addFilter( $filter );
            }
        }

        $this->status = new ezcAuthenticationStatus();
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
            case 'status':
                if ( $value instanceof ezcAuthenticationStatus )
                {
                    $this->properties[$name] = $value;
                }
                else
                {
                    throw new ezcBaseValueException( $name, $value, 'ezcAuthenticationStatus' );
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
            case 'status':
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
            case 'status':
                return isset( $this->properties[$name] );

            default:
                return false;
        }
    }

    /**
     * Runs the filter and returns a status code when finished.
     *
     * @param ezcAuthenticationCredentials $credentials Authentication credentials
     * @return int
     */
    public function run( $credentials )
    {
        if ( count( $this->filters ) === 0 )
        {
            return self::STATUS_OK;
        }

        $success = false;

        if ( $this->options->mode === self::MODE_OR )
        {
            $success = false;
            foreach ( $this->filters as $filter )
            {
                $credentials = ( $this->options->multipleCredentials === true ) ? $filter[1] :
                                                                                  $credentials;

                $code = $filter[0]->run( $credentials );
                $this->status->append( get_class( $filter[0] ), $code );
                if ( $code === self::STATUS_OK )
                {
                    $success = true;
                }
            }
        }

        if ( $this->options->mode === self::MODE_AND )
        {
            $success = true;
            foreach ( $this->filters as $filter )
            {
                $credentials = ( $this->options->multipleCredentials === true ) ? $filter[1] :
                                                                                  $credentials;

                $code = $filter[0]->run( $credentials );
                $this->status->append( get_class( $filter[0] ), $code );
                if ( $code !== self::STATUS_OK )
                {
                    $success = false;
                }
            }
        }

        // other modes are not possible due to the way mode is set in __set()
        // in the options class

        return ( $success === true ) ? self::STATUS_OK :
                                       self::STATUS_GROUP_FAILED;
    }

    /**
     * Adds an authentication filter at the end of the filter list.
     *
     * Example of using multipleCredentials:
     * <code>
     * $credentials1 = new ezcAuthenticationPasswordCredentials( 'john.doe', '1234' );
     * $credentials1 = new ezcAuthenticationPasswordCredentials( 'jan.modaal', 'qwerty' );
     *
     * $filter1 = new ezcAuthenticationHtpasswdFilter( '/etc/htpasswd1' );
     * $filter2 = new ezcAuthenticationHtpasswdFilter( '/etc/htpasswd2' );
     *
     * // enable multiple credentials
     * $options = new ezcAuthenticationGroupOptions();
     * $options->multipleCredentials = true;
     *
     * // add the filters to the group with addFilter()
     * $group = new ezcAuthenticationGroupFilter( array(), $options );
     * $group->addFilter( $filter1, $credentials1 );
     * $group->addFilter( $filter2, $credentials2 );
     *
     * // the filters can also be added to the group with the constructor
     * </code>
     *
     * @throws ezcAuthenticationGroupException
     *         if the multipleCredentials option is enabled and a credentials
     *         object was not specified
     * @param ezcAuthenticationFilter $filter The authentication filter to add
     * @param ezcAuthenticationCredentials $credentials Credentials object associated
     *                                                  with $filter if the multipleCredentials
     *                                                  option is enabled
     */
    public function addFilter( ezcAuthenticationFilter $filter, ezcAuthenticationCredentials $credentials = null )
    {
        if ( $this->options->multipleCredentials === true )
        {
            if ( $credentials === null )
            {
                throw new ezcAuthenticationGroupException( 'A credentials object must be specified for each filter when the multipleCredentials option is enabled.' );
            }

            $this->filters[] = array( $filter, $credentials );
        }
        else
        {
            $this->filters[] = array( $filter );
        }
    }
}
?>
