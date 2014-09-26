<?php
/**
 * File containing the ezcAuthenticationDatabaseFilter class.
 *
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package AuthenticationDatabaseTiein
 * @version 1.1
 */

/**
 * Filter to authenticate against a database.
 *
 * The database instance to use is specified using a ezcAuthenticationDatabaseInfo
 * structure. Table name and field names are specified in the same structure.
 *
 * Example:
 * <code>
 * $credentials = new ezcAuthenticationPasswordCredentials( 'jan.modaal', 'b1b3773a05c0ed0176787a4f1574ff0075f7521e' );
 * $database = new ezcAuthenticationDatabaseInfo( ezcDbInstance::get(), 'users', array( 'user', 'password' ) );
 * $authentication = new ezcAuthentication( $credentials );
 * $authentication->addFilter( new ezcAuthenticationDatabaseFilter( $database ) );
 * if ( !$authentication->run() )
 * {
 *     // authentication did not succeed, so inform the user
 *     $status = $authentication->getStatus();
 *     $err = array(
 *              'ezcAuthenticationDatabaseFilter' => array(
 *                  ezcAuthenticationDatabaseFilter::STATUS_USERNAME_INCORRECT => 'Incorrect username',
 *                  ezcAuthenticationDatabaseFilter::STATUS_PASSWORD_INCORRECT => 'Incorrect password'
 *                  )
 *              );
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
 * Extra data can be fetched from the database during the authentication process,
 * by registering the data to be fetched before calling run(). Example:
 * <code>
 * // $filter is an ezcAuthenticationDatabaseFilter object
 * $filter->registerFetchData( array( 'name', 'country' ) );
 *
 * // after run()
 * $data = $filter->fetchData();
 * </code>
 *
 * The $data array will be something like:
 * <code>
 * array( 'name' => array( 'John Doe' ),
 *        'country' => array( 'US' )
 *      );
 * </code>
 *
 * @property ezcAuthenticationDatabaseInfo $database
 *           Structure which holds a database instance, table name and fields
 *           which are used for authentication.
 *
 * @package AuthenticationDatabaseTiein
 * @version 1.1
 * @mainclass
 */
class ezcAuthenticationDatabaseFilter extends ezcAuthenticationFilter implements ezcAuthenticationDataFetch
{
    /**
     * Username is not found in the database.
     */
    const STATUS_USERNAME_INCORRECT = 1;

    /**
     * Password is incorrect.
     */
    const STATUS_PASSWORD_INCORRECT = 2;

    /**
     * Holds the attributes which will be requested during the authentication
     * process.
     *
     * Usually it has this structure:
     * <code>
     * array( 'fullname', 'gender', 'country', 'language' );
     * </code>
     *
     * @var array(string)
     */
    protected $requestedData = array();

    /**
     * Holds the extra data fetched during the authentication process.
     *
     * Usually it has this structure:
     * <code>
     * array( 'name' => array( 'John Doe' ),
     *        'country' => array( 'US' )
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
     * @param ezcAuthenticationDatabaseInfo $database Database to use in authentication
     * @param ezcAuthenticationDatabaseOptions $options Options for this class
     */
    public function __construct( ezcAuthenticationDatabaseInfo $database, ezcAuthenticationDatabaseOptions $options = null )
    {
        $this->options = ( $options === null ) ? new ezcAuthenticationDatabaseOptions() : $options;
        $this->database = $database;
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
            case 'database':
                if ( $value instanceof ezcAuthenticationDatabaseInfo )
                {
                    $this->properties[$name] = $value;
                }
                else
                {
                    throw new ezcBaseValueException( $name, $value, 'ezcAuthenticationDatabaseInfo' );
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
            case 'database':
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
            case 'database':
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
        $db = $this->database;

        // see if username exists
        $query = new ezcQuerySelect( $db->instance );
        $e = $query->expr;
        $query->select( 'COUNT( ' . $db->instance->quoteIdentifier( $db->fields[0] ) . ' )' )
              ->from( $db->instance->quoteIdentifier( $db->table ) )
              ->where(
                  $e->eq( $db->instance->quoteIdentifier( $db->fields[0] ), $query->bindValue( $credentials->id ) )
                     );
        $rows = $query->prepare();
        $rows->execute();
        $count = (int)$rows->fetchColumn( 0 );
        if ( $count === 0 )
        {
            return self::STATUS_USERNAME_INCORRECT;
        }
        $rows->closeCursor();

        // see if username has the specified password
        $query = new ezcQuerySelect( $db->instance );
        $e = $query->expr;
        $query->select( 'COUNT( ' . $db->instance->quoteIdentifier( $db->fields[0] ) . '  )' )
              ->from( $db->instance->quoteIdentifier( $db->table ) )
              ->where( $e->lAnd(
                  $e->eq( $db->instance->quoteIdentifier( $db->fields[0] ), $query->bindValue( $credentials->id ) ),
                  $e->eq( $db->instance->quoteIdentifier( $db->fields[1] ), $query->bindValue( $credentials->password ) )
                     ) );
        $rows = $query->prepare();
        $rows->execute();
        $count = (int)$rows->fetchColumn( 0 );
        if ( $count === 0 )
        {
            return self::STATUS_PASSWORD_INCORRECT;
        }
        $rows->closeCursor();

        if ( count( $this->requestedData ) > 0 )
        {
            // fetch extra data from the database
            $query = new ezcQuerySelect( $db->instance );
            $e = $query->expr;
            $params = array();
            foreach ( $this->requestedData as $param )
            {
                $params[] = $db->instance->quoteIdentifier( $param );
            }
            $query->select( implode( ', ', $params ) )
                  ->from( $db->instance->quoteIdentifier( $db->table ) )
                  ->where( $e->lAnd(
                      $e->eq( $db->instance->quoteIdentifier( $db->fields[0] ), $query->bindValue( $credentials->id ) ),
                      $e->eq( $db->instance->quoteIdentifier( $db->fields[1] ), $query->bindValue( $credentials->password ) )
                         ) );
            $rows = $query->prepare();
            $rows->execute();
            $data = $rows->fetchAll();
            $data = $data[0];

            foreach ( $this->requestedData as $attribute )
            {
                $this->data[$attribute] = array( $data[$attribute] );
            }
        }

        return self::STATUS_OK;
    }

    /**
     * Registers the extra data which will be fetched by the filter during the
     * authentication process.
     *
     * The input $data should be an array of attributes, for example:
     * <code>
     * array( 'name', 'country' );
     * </code>
     *
     * @param array(string) $data The extra data to fetch during authentication
     */
    public function registerFetchData( array $data = array() )
    {
        $this->requestedData = $data;
    }

    /**
     * Returns the extra data which was fetched during the authentication process.
     *
     * Example of returned array:
     * <code>
     * array( 'name' => array( 'John Doe' ),
     *        'country' => array( 'US' )
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
