<?php
/**
 * File containing the ezcQueryExpressionOracle class.
 *
 * @package Database
 * @version 1.4.7
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The ezcQueryExpressionOracle class is used to create SQL expression for Oracle.
 *
 * This class reimplements the methods that have a different syntax in Oracle.
 *
 * @package Database
 * @version 1.4.7
 */
class ezcQueryExpressionOracle extends ezcQueryExpression
{
    /**
     * Constructs an empty ezcQueryExpression
     *
     * @param PDO $db
     */
    public function __construct( PDO $db )
    {
        parent::__construct( $db );
    }

    /**
     * Returns a series of strings concatinated
     *
     * concat() accepts an arbitrary number of parameters. Each parameter
     * must contain an expression or an array with expressions.
     *
     * @throws ezcQueryVariableException if no parameters are provided
     * @param string|array(string) $... strings that will be concatinated.
     * @return string
     */
    public function concat()
    {
        $args = func_get_args();
        $cols = ezcQuery::arrayFlatten( $args );
        if ( count( $cols ) < 1 )
        {
            throw new ezcQueryVariableParameterException( 'concat', count( $args ), 1 );
        }

        $cols = $this->getIdentifiers( $cols );
        return join( ' || ' , $cols );
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
            return "substr( {$value}, {$from} )";
        }
        else
        {
            $len = $this->getIdentifier( $len );
            return "substr( {$value}, {$from}, {$len} )";
        }
    }

    /**
     * Returns the current system date and time in the database internal
     * format.
     *
     * Note: The returned timestamp is a SYSDATE.
     * The format can be set after connecting with e.g.:
     * ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'
     *
     * @return string
     */
    public function now()
    {
        return "LOCALTIMESTAMP";
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
        return "INSTR( {$value}, '{$substr}' )";
    }

    /**
     * Returns the SQL that performs the bitwise AND on two values.
     *
     * @param string $value1
     * @param string $value2
     * @return string
     */
    public function bitAnd( $value1, $value2 )
    {
        $value1 = $this->getIdentifier( $value1 );
        $value2 = $this->getIdentifier( $value2 );
        return "bitand( {$value1}, {$value2} )";
    }

    /**
     * Returns the SQL that performs the bitwise OR on two values.
     *
     * @param string $value1
     * @param string $value2
     * @return string
     */
    public function bitOr( $value1, $value2 )
    {
        $value1 = $this->getIdentifier( $value1 );
        $value2 = $this->getIdentifier( $value2 );
        return "( {$value1} + {$value2} - bitand( {$value1}, {$value2} ) )";
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
        return "( {$value1} + {$value2} - bitand( {$value1}, {$value2} ) * 2 )";
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

        if ( $column != 'NOW()' )
        {
            $column = "CAST( {$column} AS TIMESTAMP )";
//            // alternative
//            if ( preg_match( '/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $column ) ) {
//                $column = "TO_TIMESTAMP( {$column}, 'YYYY-MM-DD HH24:MI:SS' )";
//            }
        }

        $date1 = "CAST( SYS_EXTRACT_UTC( {$column} ) AS DATE )";
        $date2 = "TO_DATE( '19700101000000', 'YYYYMMDDHH24MISS' )";
        return " ROUND( ( {$date1} - {$date2} ) / ( 1 / 86400 ) ) ";
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
        $column = $this->getIdentifier( $column );

        if ( $column != 'NOW()' )
        {
            $column = "CAST( {$column} AS TIMESTAMP )";
        }

        return " {$column} - INTERVAL '{$expr}' {$type} ";
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
        $column = $this->getIdentifier( $column );

        if ( $column != 'NOW()' )
        {
            $column = "CAST( {$column} AS TIMESTAMP )";
        }

        return " {$column} + INTERVAL '{$expr}' {$type} ";
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
        $column = $this->getIdentifier( $column );

        if ( $column != 'NOW()' )
        {
            $column = "CAST( {$column} AS TIMESTAMP )";
        }

        if ( $type == 'SECOND' )
        {
            return " FLOOR( EXTRACT( {$type} FROM {$column} ) ) ";
        }
        else
        {
            return " EXTRACT( {$type} FROM {$column} ) ";
        }
    }

    /**
     * Returns the SQL to check if a value is one in a set of
     * given values.
     *
     * in() accepts an arbitrary number of parameters. The first parameter
     * must always specify the value that should be matched against. Successive
     * parameters must contain a logical expression or an array with logical
     * expressions.  These expressions will be matched against the first
     * parameter.
     *
     * Example:
     * <code>
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->in( 'id', 1, 2, 3 ) );
     * </code>
     *
     * Oracle limits the number of values in a single IN() to 1000. This 
     * implementation creates a list of combined IN() expressions to bypass 
     * this limitation.
     *
     * @throws ezcQueryVariableParameterException if called with less than two
     *         parameters.
     * @throws ezcQueryInvalidParameterException if the 2nd parameter is an
     *         empty array.
     * @param string|array(string) values that will be matched against $column
     * @return string logical expression
     */
    public function in( $column )
    {
        $args = func_get_args();
        if ( count( $args ) < 2 )
        {
            throw new ezcQueryVariableParameterException( 'in', count( $args ), 2 );
        }

        if ( is_array( $args[1] ) && count( $args[1] ) == 0 )
        {
            throw new ezcQueryInvalidParameterException( 'in', 2, 'empty array', 'non-empty array' );
        }

        $values = ezcQuerySelect::arrayFlatten( array_slice( $args, 1 ) );
        $values = $this->getIdentifiers( $values );
        $column = $this->getIdentifier( $column );

        if ( count( $values ) == 0 )
        {
            throw new ezcQueryVariableParameterException( 'in', count( $args ), 2 );
        }
        
        if ( $this->quoteValues )
        {
            foreach ( $values as $key => $value )
            {
                switch ( true )
                {
                    case is_int( $value ):
                    case is_float( $value ):
                    case $value instanceof ezcQuerySubSelect:
                        $values[$key] = (string) $value;
                        break;
                    default:
                        $values[$key] = $this->db->quote( $value );
                }
            }
        }
        
        if ( count( $values ) <= 1000 )
        {
            return "{$column} IN ( " . join( ', ', $values ) . ' )';
        }
        else
        {
            $expression = '( ';

            do {
                $bunch = array_slice( $values, 0, 1000 );
                $values = array_slice( $values, 1000 );

                $expression .= "{$column} IN ( " . join( ', ', $bunch ) . ' ) OR ';
            } while ( count( $values ) > 1000 );

            $expression .= "{$column} IN ( " . join( ', ', $values ) . ' ) )';

            return $expression;
        }
    }
}
?>
