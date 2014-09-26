<?php
/**
 * File containing the ezcDbFactory class.
 *
 * @package Database
 * @version 1.4.7
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcDbFactory manages the list of known database drivers
 * and is used to create their instances.
 *
 * Example:
 * <code>
 * $dbparams = array(
 *     'type'   => 'mysql',
 *     'dbname' => 'test',
 *     'user'   => 'john',
 *     'pass'   => 'topsecret' );
 * $db = ezcDbFactory::create( $dbparams );
 * $db->query( 'SELECT * FROM tbl' );
 * </code>
 *
 * Instead of passing an array with those parameters, you can also pass a DSN:
 *
 * <code>
 * $dsn = "mysql://root@localhost/geolocation";
 * $db = ezcDbFactory::create( $dsn );
 * </code>
 *
 * Other examples of DSNs are:
 * <code>
 * $dsn = "sqlite:///tmp/ezc.sqlite"; // Disk based databases for SQLite.
 * $dsn = "sqlite://:memory:";        // In memory databases for SQLite.
 * </code>
 *
 * Note that this class does not deal with character sets automatically, you
 * have to make sure that you do that yourself. For MySQL that means running
 * a query "SET NAMES" for example. See the tutorial for some hints on this.
 *
 * @see create()
 *
 * @package Database
 * @version 1.4.7
 */
class ezcDbFactory
{
    /**
     * List of supported database implementations.
     *
     * The list is an array with the form 'dbname' => 'HandlerClassName'
     * This list may be extended using {@link addImplementation()}.
     *
     * @var array(string=>string)
     */
    static private $implementations = array( 'mysql'  => 'ezcDbHandlerMysql',
                                             'pgsql'  => 'ezcDbHandlerPgsql',
                                             'oracle' => 'ezcDbHandlerOracle',
                                             'sqlite' => 'ezcDbHandlerSqlite',
                                             'mssql' => 'ezcDbHandlerMssql', );

    /**
     * Adds a database implementation to the list of known implementations.
     *
     * $implementationName is the name of the implemenation. This name should
     * be short and uniquely identify the database. $className is the class name
     * of the class that implements the handler for this database.
     *
     * Example:
     * <code>
     * class DB2Handler
     * {
     * }
     * ezcDbFactory::addImplementation( 'db2', 'DB2Handler' );
     * // ...
     * $dbparams = array( 'handler' => 'db2', ... );
     * $db = ezcDbFactory::create( $dbparams );
     * </code>
     *
     * @param string $implementationName
     * @param string $className
     * @return void
     */
    static public function addImplementation( $implementationName, $className )
    {
        self::$implementations[$implementationName] = $className;
    }

    /**
     * Returns a list with supported database implementations.
     *
     * Example:
     * <code>
     * ezcDbFactory::getImplementations();
     * </code>
     *
     * @return array(string)
     */
    static public function getImplementations()
    {
        $list = array();
        foreach ( self::$implementations as $name => $className )
        {
            $list[] = $name;
        }
        return $list;
    }

    /**
     * Creates and returns an instance of the specified ezcDbHandler implementation.
     *
     * Supported database parameters are:
     * - phptype|type|handler|driver: Database implementation
     * - user|username:               Database user name
     * - pass|password:               Database user password
     * - dbname|database:             Database name
     * - host|hostspec:               Name of the host database is running on
     * - port:                        TCP port
     * - charset:                     Client character set
     * - socket:                      UNIX socket path
     *
     * The list above is actually driver-dependent and may be extended in the future.
     * You can specify any parameters your database handler supports.
     *
     * @throws ezcDbHandlerNotFoundException if the requested database handler could not be found.
     * @param   mixed  $dbParams Database parameters
     *                 (driver, host, port, user, pass, etc).
     *                 May be specified either as array (key => val ....) or as DSN string.
     *                 Format of the DSN is the same as accepted by PEAR::DB::parseDSN().
     * @return ezcDbHandler
     */
    static public function create( $dbParams )
    {
        if ( is_string( $dbParams ) )
        {
            $dbParams = self::parseDSN( $dbParams );
        }
        else if ( !is_array( $dbParams ) )
        {
            throw new ezcBaseValueException( 'dbParams', $dbParams, 'string or array', 'parameter' );
        }

        $impName = null; // implementation name

        foreach ( $dbParams as $key => $val )
        {
            if ( in_array( $key, array( 'phptype', 'type', 'handler', 'driver' ) ) )
            {
                 $impName = $val;
                 break;
            }
        }

        if ( $impName === null || !array_key_exists( $impName, self::$implementations ) )
        {
            throw new ezcDbHandlerNotFoundException( $impName, array_keys( self::$implementations ) );
        }

        $className = self::$implementations[$impName];
        $instance = new $className( $dbParams );

        return $instance;
    }

    /**
     * Returns the Data Source Name as a structure containing the various parts of the DSN.
     *
     * Additional keys can be added by appending a URI query string to the
     * end of the DSN.
     *
     * The format of the supplied DSN is in its fullest form:
     * <code>
     *  phptype(dbsyntax)://username:password@protocol+hostspec/database?option=8&another=true
     * </code>
     *
     * Most variations are allowed:
     * <code>
     *  phptype://username:password@protocol+hostspec:110//usr/db_file.db?mode=0644
     *  phptype://username:password@hostspec/database_name
     *  phptype://username:password@hostspec
     *  phptype://username@hostspec
     *  phptype://hostspec/database
     *  phptype://hostspec
     *  phptype(dbsyntax)
     *  phptype
     * </code>
     *
     * This function is 'borrowed' from PEAR /DB.php .
     *
     * @param string $dsn Data Source Name to be parsed
     *
     * @return array an associative array with the following keys:
     *  + phptype:  Database backend used in PHP (mysql, odbc etc.)
     *  + dbsyntax: Database used with regards to SQL syntax etc.
     *  + protocol: Communication protocol to use (tcp, unix etc.)
     *  + hostspec: Host specification (hostname[:port])
     *  + database: Database to use on the DBMS server
     *  + username: User name for login
     *  + password: Password for login
     */
    public static function parseDSN( $dsn )
    {
        $parsed = array(
            'phptype'  => false,
            'dbsyntax' => false,
            'username' => false,
            'password' => false,
            'protocol' => false,
            'hostspec' => false,
            'port'     => false,
            'socket'   => false,
            'database' => false,
        );

        if ( is_array( $dsn ) )
        {
            $dsn = array_merge( $parsed, $dsn );
            if ( !$dsn['dbsyntax'] )
            {
                $dsn['dbsyntax'] = $dsn['phptype'];
            }
            return $dsn;
        }

        // Find phptype and dbsyntax
        if ( ( $pos = strpos( $dsn, '://' ) ) !== false )
        {
            $str = substr( $dsn, 0, $pos );
            $dsn = substr( $dsn, $pos + 3 );
        }
        else
        {
            $str = $dsn;
            $dsn = null;
        }

        // Get phptype and dbsyntax
        // $str => phptype(dbsyntax)
        if ( preg_match( '|^(.+?)\((.*?)\)$|', $str, $arr ) )
        {
            $parsed['phptype']  = $arr[1];
            $parsed['dbsyntax'] = !$arr[2] ? $arr[1] : $arr[2];
        }
        else
        {
            $parsed['phptype']  = $str;
            $parsed['dbsyntax'] = $str;
        }

        if ( !count( $dsn ) )
        {
            return $parsed;
        }

        // Get (if found): username and password
        // $dsn => username:password@protocol+hostspec/database
        if ( ( $at = strrpos( (string) $dsn, '@' ) ) !== false )
        {
            $str = substr( $dsn, 0, $at );
            $dsn = substr( $dsn, $at + 1 );
            if ( ( $pos = strpos( $str, ':' ) ) !== false )
            {
                $parsed['username'] = rawurldecode( substr( $str, 0, $pos ) );
                $parsed['password'] = rawurldecode( substr( $str, $pos + 1 ) );
            }
            else
            {
                $parsed['username'] = rawurldecode( $str );
            }
        }

        // Find protocol and hostspec

        if ( preg_match( '|^([^(]+)\((.*?)\)/?(.*?)$|', $dsn, $match ) )
        {
            // $dsn => proto(proto_opts)/database
            $proto       = $match[1];
            $proto_opts  = $match[2] ? $match[2] : false;
            $dsn         = $match[3];
        }
        else
        {
            // $dsn => protocol+hostspec/database (old format)
            if ( strpos( $dsn, '+' ) !== false )
            {
                list( $proto, $dsn ) = explode( '+', $dsn, 2 );
            }
            if ( strpos( $dsn, '/' ) !== false )
            {
                list( $proto_opts, $dsn ) = explode( '/', $dsn, 2 );
            }
            else
            {
                $proto_opts = $dsn;
                $dsn = null;
            }
        }

        // process the different protocol options
        $parsed['protocol'] = ( !empty( $proto ) ) ? $proto : 'tcp';
        $proto_opts = rawurldecode( $proto_opts );
        if ( $parsed['protocol'] == 'tcp' )
        {
            if ( strpos( $proto_opts, ':' ) !== false )
            {
                list( $parsed['hostspec'], $parsed['port'] ) = explode( ':', $proto_opts );
            }
            else
            {
                $parsed['hostspec'] = $proto_opts;
            }
        }
        elseif ( $parsed['protocol'] == 'unix' )
        {
            $parsed['socket'] = $proto_opts;
        }

        // Get dabase if any
        // $dsn => database
        if ( $dsn )
        {
            if ( ( $pos = strpos( $dsn, '?' ) ) === false )
            {
                // /database
                $parsed['database'] = rawurldecode( $dsn );
            }
            else
            {
                // /database?param1=value1&param2=value2
                $parsed['database'] = rawurldecode( substr( $dsn, 0, $pos ) );
                $dsn = substr( $dsn, $pos + 1 );
                if ( strpos( $dsn, '&') !== false )
                {
                    $opts = explode( '&', $dsn );
                }
                else
                { // database?param1=value1
                    $opts = array( $dsn );
                }
                foreach ( $opts as $opt )
                {
                    list( $key, $value ) = explode( '=', $opt );
                    if ( !isset( $parsed[$key] ) )
                    {
                        // don't allow params overwrite
                        $parsed[$key] = rawurldecode( $value );
                    }
                }
            }
        }
        return $parsed;
    }
}
?>
