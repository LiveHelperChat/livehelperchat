<?php
/**
 * File containing the ezcDbHandlerSqlite class.
 *
 * @package Database
 * @version 1.4.7
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * SQLite driver implementation
 *
 * @see ezcDbHandler
 * @package Database
 * @version 1.4.7
 */
class ezcDbHandlerSqlite extends ezcDbHandler
{
    /**
     * Constructs a handler object from the parameters $dbParams.
     *
     * Supported database parameters are:
     * - dbname|database: Database name
     * - port:            If "memory" is used then the driver will use an
     *                    in-memory database, and the database name is ignored.
     *
     * @throws ezcDbMissingParameterException if the database name was not specified.
     * @param array $dbParams Database connection parameters (key=>value pairs).
     */
    public function __construct( $dbParams )
    {
        $database = false;

        foreach ( $dbParams as $key => $val )
        {
            switch ( $key )
            {
                case 'database':
                case 'dbname':
                    $database = $val;
                    if ( !empty( $database ) && $database[0] != '/' && ezcBaseFeatures::os() != 'Windows' )
                    {
                        $database = '/' . $database;
                    }
                    break;
            }
        }

        // If the "port" is set then we use "sqlite::memory:" as DSN, otherwise we fallback
        // to the database name.
        if ( !empty( $dbParams['port'] ) && $dbParams['port'] == 'memory' )
        {
            $dsn = "sqlite::memory:";
        }
        else
        {
            if ( $database === false )
            {
                throw new ezcDbMissingParameterException( 'database', 'dbParams' );
            }

            $dsn = "sqlite:$database";
        }

        parent::__construct( $dbParams, $dsn );

        /* Register PHP implementations of missing functions in SQLite */
        $this->sqliteCreateFunction( 'md5', array( 'ezcQuerySqliteFunctions', 'md5Impl' ), 1 );
        $this->sqliteCreateFunction( 'mod', array( 'ezcQuerySqliteFunctions', 'modImpl' ), 2 );
        $this->sqliteCreateFunction( 'locate', array( 'ezcQuerySqliteFunctions', 'positionImpl' ), 2 );
        $this->sqliteCreateFunction( 'floor', array( 'ezcQuerySqliteFunctions', 'floorImpl' ), 1 );
        $this->sqliteCreateFunction( 'ceil', array( 'ezcQuerySqliteFunctions', 'ceilImpl' ), 1 );
        $this->sqliteCreateFunction( 'concat', array( 'ezcQuerySqliteFunctions', 'concatImpl' ) );
        $this->sqliteCreateFunction( 'toUnixTimestamp', array( 'ezcQuerySqliteFunctions', 'toUnixTimestampImpl' ), 1 );
        $this->sqliteCreateFunction( 'now', 'time', 0 );
    }

    /**
     * Returns 'sqlite'.
     *
     * @return string
     */
    static public function getName()
    {
        return 'sqlite';
    }

    /**
     * Returns true if $feature is supported by this db handler.
     *
     * @apichange Never implemented properly, no good use (See #10937)
     * @ignore
     * @param string $feature
     * @return array(string)
     */
    static public function hasFeature( $feature )
    {
        $supportedFeatures = array( 'multi-table-delete', 'cross-table-update' );
        return in_array( $feature, $supportedFeatures );
    }

    /**
     * Returns a new ezcQuerySelect derived object with SQLite implementation specifics.
     *
     * @return ezcQuerySelectSqlite
     */
    public function createSelectQuery()
    {
        return new ezcQuerySelectSqlite( $this );
    }

    /**
     * Returns a new ezcQueryExpression derived object with SQLite implementation specifics.
     *
     * @return ezcQueryExpressionqSqlite
     */
    public function createExpression()
    {
        return new ezcQueryExpressionSqlite( $this );
    }

    /**
     * Returns a new ezcUtilities derived object with SQLite implementation specifics.
     *
     * @return ezcUtilitiesSqlite
     */
    public function createUtilities()
    {
        return new ezcDbUtilitiesSqlite( $this );
    }
}
?>
