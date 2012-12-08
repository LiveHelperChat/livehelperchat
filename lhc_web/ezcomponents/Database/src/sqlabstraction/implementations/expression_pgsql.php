<?php
/**
 * File containing the ezcQueryExpressionPgsql class.
 *
 * @package Database
 * @version 1.4.7
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The ezcQueryExpressionPgsql class is used to create SQL expression for PostgreSQL.
 *
 * This class reimplements the methods that have a different syntax in postgreSQL.
 *
 * @package Database
 * @version 1.4.7
 */
class ezcQueryExpressionPgsql extends ezcQueryExpression
{
    /**
     * Stores the PostgreSQL version number.
     *
     * @var int
     */
    private $version;

    /**
     * Constructs an pgsql expression object using the db $db.
     *
     * @param PDO $db
     */
    public function __construct( PDO $db )
    {
        parent::__construct( $db );

        $version = $db->getAttribute( PDO::ATTR_SERVER_VERSION );
        $this->version = substr( $version, 0, 1 );
    }

    /**
     * Returns the md5 sum of the field $column.
     *
     * Note: Not SQL92, but common functionality
     *
     * md5() works with the default PostgreSQL 8 versions.
     *
     * If you are using PostgreSQL 7.x or older you need
     * to make sure that the digest procedure.
     * If you use RPMS (Redhat and Mandrake) install the postgresql-contrib
     * package. You must then install the procedure by running this shell command:
     * <code>
     * psql [dbname] < /usr/share/pgsql/contrib/pgcrypto.sql
     * </code>
     * You should make sure you run this as the postgres user.
     *
     * @param string $column
     * @return string
     */
    public function md5( $column )
    {
        $column = $this->getIdentifier( $column );
        if ( $this->version > 7 )
        {
            return "MD5( {$column} )";
        }
        else
        {
            return " encode( digest( $column, 'md5' ), 'hex' ) ";
        }
    }

    /**
     * Returns part of a string.
     *
     * Note: Not SQL92, but common functionality.
     *
     * @param string $value the target $value the string or the string column.
     * @param int $from extract from this characeter.
     * @param int $len extract this amount of characters.
     * @return string sql that extracts part of a string.
     */
    public function subString( $value, $from, $len = null )
    {
        $value = $this->getIdentifier( $value );
        if ( $len === null )
        {
            $len = $this->getIdentifier( $len );
            return "substr( {$value}, {$from} )";
        }
        else
        {
            return "substr( {$value}, {$from}, {$len} )";
        }
    }

    /**
     * Returns a series of strings concatinated
     *
     * concat() accepts an arbitrary number of parameters. Each parameter
     * must contain an expression or an array with expressions.
     *
     * @throws ezcQueryVariableParameterException if no parameters are provided.
     * @param string|array(string) $... strings that will be concatinated.
     * @return string
     */
    public function concat()
    {
        $args = func_get_args();
        $cols = ezcQuery::arrayFlatten( $args );
        if ( count( $cols ) < 1 )
        {
            throw new ezcQueryVariableParameterException( 'select', count( $args ), 1 );
        }

        $cols = $this->getIdentifiers( $cols );

        return join( ' || ' , $cols );
    }

    /**
     * Returns the current system date and time in the database internal
     * format.
     *
     * @return string
     */
    public function now()
    {
        return "LOCALTIMESTAMP(0)";
    }

    /**
     * Returns the SQL to locate the position of the first occurrence of a substring
     * 
     * @param string $substr
     * @param string $value
     * @return string
     */
    public function position( $substr, $value )
    {
        $value = $this->getIdentifier( $value );
        return "POSITION( '{$substr}' IN {$value} )";
    }

    /**
     * Returns the SQL that performs the bitwise XOR on two values.
     *
     * @param string $value1
     * @param string $value2
     * @return string
     */
    public function bitXor( $value1, $value2 )
    {
        $value1 = $this->getIdentifier( $value1 );
        $value2 = $this->getIdentifier( $value2 );
        return "( {$value1} # {$value2} )";
    }

    /**
     * Returns the SQL that converts a timestamp value to a unix timestamp.
     *
     * @param string $column
     * @return string
     */
    public function unixTimestamp( $column )
    {
        $column = $this->getIdentifier( $column );
        return " EXTRACT( EPOCH FROM CAST( {$column} AS TIMESTAMP ) ) ";
    }

    /**
     * Returns the SQL that subtracts an interval from a timestamp value.
     *
     * @param string $column
     * @param numeric $expr
     * @param string $type one of SECOND, MINUTE, HOUR, DAY, MONTH, or YEAR
     * @return string
     */
    public function dateSub( $column, $expr, $type )
    {
        $type = $this->intervalMap[$type];

        if ( $column != 'NOW()' )
        {
            $column = $this->getIdentifier( $column );
            $column = "CAST( {$column} AS TIMESTAMP )";
        }

        return " {$column} - INTERVAL '{$expr} {$type}' ";
    }

    /**
     * Returns the SQL that adds an interval to a timestamp value.
     *
     * @param string $column
     * @param numeric $expr
     * @param string $type one of SECOND, MINUTE, HOUR, DAY, MONTH, or YEAR
     * @return string
     */
    public function dateAdd( $column, $expr, $type )
    {
        $type = $this->intervalMap[$type];

        if ( $column != 'NOW()' )
        {
            $column = $this->getIdentifier( $column );
            $column = "CAST( {$column} AS TIMESTAMP )";
        }

        return " {$column} + INTERVAL '{$expr} {$type}' ";
    }

    /**
     * Returns the SQL that extracts parts from a timestamp value.
     *
     * @param string $column
     * @param string $type one of SECOND, MINUTE, HOUR, DAY, MONTH, or YEAR
     * @return string
     */
    public function dateExtract( $column, $type )
    {
        $type = $this->intervalMap[$type];

        if ( $column != 'NOW()' )
        {
            $column = $this->getIdentifier( $column );
            $column = "CAST( {$column} AS TIMESTAMP )";
        }

        return " EXTRACT( {$type} FROM {$column} ) ";
    }
}
?>
