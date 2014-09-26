<?php
/**
 * File containing the ezcQueryExpression class.
 *
 * @package Database
 * @version 1.4.7
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The ezcQueryExpression class is used to create database independent SQL expression.
 *
 * The QueryExpression class is usually used through the 'expr' variable in
 * one of the Select, Insert, Update or Delete classes.
 *
 * Note that the methods for logical or and and are
 * named lOr and lAnd respectively. This is because and and or are reserved names
 * in PHP and can not be used in method names.
 *
 * @package Database
 * @version 1.4.7
 * @mainclass
 */
class ezcQueryExpression
{
    /**
     * A pointer to the database handler to use for this query.
     *
     * @var PDO
     */
    protected $db;

    /**
     * The column and table name aliases.
     *
     * Format: array('alias' => 'realName')
     * @var array(string=>string)
     */
    private $aliases = null;

    /**
     * The flag that switch quoting mode for
     * values provided by user in miscelaneous SQL functions.
     *
     * @var boolean
     */
    protected $quoteValues = true;

    /**
     * Contains an interval map from generic intervals to MySQL native intervals.
     *
     * @var array(string=>string)
     */
    protected $intervalMap = array(
        'SECOND' => 'SECOND',
        'MINUTE' => 'MINUTE',
        'HOUR' => 'HOUR',
        'DAY' => 'DAY',
        'MONTH' => 'MONTH',
        'YEAR' => 'YEAR',
    );

    /**
     * Constructs an empty ezcQueryExpression
     *
     * @param PDO $db
     * @param array(string=>string) $aliases
     */
    public function __construct( PDO $db, array $aliases = array() )
    {
        $this->db = $db;
        if ( !empty( $aliases ) )
        {
            $this->aliases = $aliases;
        }
    }

    /**
     * Sets the aliases $aliases for this object.
     *
     * The aliases can be used to substitute the column and table names with more
     * friendly names. E.g PersistentObject uses it to allow using property and class
     * names instead of column and table names.
     *
     * @param array(string=>string) $aliases
     * @return void
     */
    public function setAliases( array $aliases )
    {
        $this->aliases = $aliases;
    }

    /**
     * Returns true if this object has aliases.
     *
     * @return bool
     */
    public function hasAliases()
    {
        return $this->aliases !== null ? true : false;
    }

    /**
     * Returns the correct identifier for the alias $alias.
     *
     * If the alias does not exists in the list of aliases
     * it is returned unchanged.
     *
     * @param string $alias
     * @return string
     */
    protected function getIdentifier( $alias )
    {
        $aliasParts = explode( '.', $alias );
        $identifiers = array();
        // If the alias consists of one part, then we just look it up in the
        // array. If we find it, we use it, otherwise we return the name as-is
        // and assume it's just a column name. The alias target can be a fully
        // qualified name (table.column).
        if ( count( $aliasParts ) == 1 )
        {
            if ( $this->aliases !== null &&
                array_key_exists( $alias, $this->aliases ) )
            {
                $alias = $this->aliases[$alias];
            }
            return $alias;
        }
        // If the passed name consist of two parts, we need to check all parts
        // of the passed-in name for aliases, because an alias can be made for
        // both a table name and a column name. For each element we try to find
        // whether we have an alias mapping. Unlike the above case, the alias
        // target can in this case *not* consist of a fully qualified name as
        // this would introduce another part of the name (with two dots).
        for ( $i = 0; $i < count( $aliasParts ); $i++ )
        {
            if ( $this->aliases !== null &&
                array_key_exists( $aliasParts[$i], $this->aliases ) )
            {
                // We only use the found alias if the alias target is not a fully
                // qualified name (table.column).
                $tmpAlias = $this->aliases[$aliasParts[$i]];
                if ( count( explode( '.', $tmpAlias ) ) === 1 )
                {
                    $aliasParts[$i] = $this->aliases[$aliasParts[$i]];
                }
            }
        }
        $alias = join( '.', $aliasParts );
        return $alias;
    }

    /**
     * Returns the correct identifiers for the aliases found in $aliases.
     *
     * This method is similar to getIdentifier except that it works on an array.
     *
     * @param array(string) $aliasList
     * @return array(string)
     */
    protected function getIdentifiers( array $aliasList )
    {
        if ( $this->aliases !== null )
        {
            foreach ( $aliasList as $key => $alias )
            {
                $aliasList[$key] = $this->getIdentifier( $alias );
            }
        }
        return $aliasList;
    }

    /**
     * Sets the mode of quoting for parameters passed 
     * to SQL functions and operators.
     * 
     * Quoting mode is set to ON by default.
     * $q->expr->in( 'column1', 'Hello', 'world' ) will
     * produce SQL "column1 IN ( 'Hello', 'world' )" 
     * ( note quotes in SQL ).
     * 
     * User must execute setValuesQuoting( false ) before call 
     * to function where quoting of parameters is not desirable.
     * Example:
     * <code>
     * $q->expr->setValuesQuoting( false );
     * $q->expr->in( 'column1', 'SELECT * FROM table' ) 
     * </code>
     * This will produce SQL "column1 IN ( SELECT * FROM table )".
     * 
     * Quoting mode will remain unchanged until next call 
     * to setValuesQuoting().
     *
     * @param boolean $doQuoting - flag that switch quoting.
     * @return void
     */
    public function setValuesQuoting( $doQuoting )
    {
        $this->quoteValues = $doQuoting;
    }


    /**
     * Returns the SQL to bind logical expressions together using a logical or.
     *
     * lOr() accepts an arbitrary number of parameters. Each parameter
     * must contain a logical expression or an array with logical expressions.
     *
     * Example:
     * <code>
     * $q = ezcDbInstance::get()->createSelectQuery();
     * $e = $q->expr;
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $e->lOr( $e->eq( 'id', $q->bindValue( 1 ) ),
     *                                    $e->eq( 'id', $q->bindValue( 2 ) ) ) );
     * </code>
     *
     * @throws ezcDbAbstractionException if called with no parameters.
     * @return string a logical expression
     */
    public function lOr()
    {
        $args = func_get_args();
        if ( count( $args ) < 1 )
        {
            throw new ezcQueryVariableParameterException( 'lOr', count( $args ), 1 );
        }

        $elements = ezcQuerySelect::arrayFlatten( $args );
        if ( count( $elements ) == 1 )
        {
            return $elements[0];
        }
        else
        {
            return '( ' . join( ' OR ', $elements ) . ' )';
        }
    }

    /**
     * Returns the SQL to bind logical expressions together using a logical and.
     *
     * lAnd() accepts an arbitrary number of parameters. Each parameter
     * must contain a logical expression or an array with logical expressions.
     *
     * Example:
     * <code>
     * $q = ezcDbInstance::get()->createSelectQuery();
     * $e = $q->expr;
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $e->lAnd( $e->eq( 'id', $q->bindValue( 1 ) ),
     *                                     $e->eq( 'id', $q->bindValue( 2 ) ) ) );
     * </code>
     *
     * @throws ezcDbAbstractionException if called with no parameters.
     * @return string a logical expression
     */
    public function lAnd()
    {
        $args = func_get_args();
        if ( count( $args ) < 1 )
        {
            throw new ezcQueryVariableParameterException( 'lAnd', count( $args ), 1 );
        }

        $elements = ezcQuerySelect::arrayFlatten( $args );
        if ( count( $elements ) == 1 )
        {
            return $elements[0];
        }
        else
        {
            return '( ' . join( ' AND ', $elements ) . ' )';
        }
    }

    /**
     * Returns the SQL for a logical not, negating the $expression.
     *
     * Example:
     * <code>
     * $q = ezcDbInstance::get()->createSelectQuery();
     * $e = $q->expr;
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $e->eq( 'id', $e->not( 'null' ) ) );
     * </code>
     *
     * @param string $expression
     * @return string a logical expression
     */
    public function not( $expression )
    {
        $expression = $this->getIdentifier( $expression );
        return "NOT ( {$expression} )";
    }

    // math

    /**
     * Returns the SQL to perform the same mathematical operation over an array
     * of values or expressions.
     *
     * basicMath() accepts an arbitrary number of parameters. Each parameter
     * must contain a value or an expression or an array with values or
     * expressions.
     *
     * @throws ezcDbAbstractionException if called with no parameters.
     * @param string $type the type of operation, can be '+', '-', '*' or '/'.
     * @param string|array(string) $...
     * @return string an expression
     */
    private function basicMath( $type )
    {
        $args = func_get_args();
        $elements = ezcQuerySelect::arrayFlatten( array_slice( $args, 1 ) );
        $elements = $this->getIdentifiers( $elements );
        if ( count( $elements ) < 1 )
        {
            throw new ezcQueryVariableParameterException( $type, count( $args ), 1 );
        }
        if ( count( $elements ) == 1 )
        {
            return $elements[0];
        }
        else
        {
            return '( ' . join( " $type ", $elements ) . ' )';
        }
    }

    /**
     * Returns the SQL to add values or expressions together.
     *
     * add() accepts an arbitrary number of parameters. Each parameter
     * must contain a value or an expression or an array with values or
     * expressions.
     *
     * Example:
     * <code>
     * $q = ezcDbInstance::get()->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->add( 'id', 2 )  );
     * </code>
     *
     * @throws ezcDbAbstractionException if called with no parameters.
     * @param string|array(string) $...
     * @return string an expression
     */
    public function add()
    {
        $args = func_get_args();
        return $this->basicMath( '+', $args  );
    }

    /**
     * Returns the SQL to subtract values or expressions from eachother.
     *
     * subtract() accepts an arbitrary number of parameters. Each parameter
     * must contain a value or an expression or an array with values or
     * expressions.
     *
     * Example:
     * <code>
     * $q = ezcDbInstance::get()->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->subtract( 'id', 2 )  );
     * </code>
     *
     * @throws ezcDbAbstractionException if called with no parameters.
     * @param string|array(string) $...
     * @return string an expression
     */
    public function sub()
    {
        $args = func_get_args();
        return $this->basicMath( '-', $args );
    }

    /**
     * Returns the SQL to multiply values or expressions by eachother.
     *
     * multiply() accepts an arbitrary number of parameters. Each parameter
     * must contain a value or an expression or an array with values or
     * expressions.
     *
     * Example:
     * <code>
     * $q = ezcDbInstance::get()->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->multiply( 'id', 2 )  );
     * </code>
     *
     * @throws ezcDbAbstractionException if called with no parameters.
     * @param string|array(string) $...
     * @return string an expression
     */
    public function mul()
    {
        $args = func_get_args();
        return $this->basicMath( '*', $args );
    }

    /**
     * Returns the SQL to divide values or expressions by eachother.
     *
     * divide() accepts an arbitrary number of parameters. Each parameter
     * must contain a value or an expression or an array with values or
     * expressions.
     *
     * Example:
     * <code>
     * $q = ezcDbInstance::get()->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->divide( 'id', 2 )  );
     * </code>
     *
     * @throws ezcDbAbstractionException if called with no parameters.
     * @param string|array(string) $...
     * @return string an expression
     */
    public function div()
    {
        $args = func_get_args();
        return $this->basicMath( '/', $args );
    }

    /**
     * Returns the SQL to check if two values are equal.
     *
     * Example:
     * <code>
     * $q = ezcDbInstance::get()->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->eq( 'id', $q->bindValue( 1 ) ) );
     * </code>
     *
     * @param string $value1 logical expression to compare
     * @param string $value2 logical expression to compare with
     * @return string logical expression
     */
    public function eq( $value1, $value2 )
    {
        $value1 = $this->getIdentifier( $value1 );
        $value2 = $this->getIdentifier( $value2 );
        return "{$value1} = {$value2}";
    }

    /**
     * Returns the SQL to check if two values are unequal.
     *
     * Example:
     * <code>
     * $q = ezcDbInstance::get()->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->neq( 'id', $q->bindValue( 1 ) ) );
     * </code>
     *
     * @param string $value1 logical expression to compare
     * @param string $value2 logical expression to compare with
     * @return string logical expression
     */
    public function neq( $value1, $value2 )
    {
        $value1 = $this->getIdentifier( $value1 );
        $value2 = $this->getIdentifier( $value2 );
        return "{$value1} <> {$value2}";
    }

    /**
     * Returns the SQL to check if one value is greater than another value.
     *
     * Example:
     * <code>
     * $q = ezcDbInstance::get()->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->gt( 'id', $q->bindValue( 1 ) ) );
     * </code>
     *
     * @param string $value1 logical expression to compare
     * @param string $value2 logical expression to compare with
     * @return string logical expression
     */
    public function gt( $value1, $value2 )
    {
        $value1 = $this->getIdentifier( $value1 );
        $value2 = $this->getIdentifier( $value2 );
        return "{$value1} > {$value2}";
    }

    /**
     * Returns the SQL to check if one value is greater than or equal to
     * another value.
     *
     * Example:
     * <code>
     * $q = ezcDbInstance::get()->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->gte( 'id', $q->bindValue( 1 ) ) );
     * </code>
     *
     * @param string $value1 logical expression to compare
     * @param string $value2 logical expression to compare with
     * @return string logical expression
     */
    public function gte( $value1, $value2 )
    {
        $value1 = $this->getIdentifier( $value1 );
        $value2 = $this->getIdentifier( $value2 );
        return "{$value1} >= {$value2}";
    }

    /**
     * Returns the SQL to check if one value is less than another value.
     *
     * Example:
     * <code>
     * $q = ezcDbInstance::get()->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->lt( 'id', $q->bindValue( 1 ) ) );
     * </code>
     *
     * @param string $value1 logical expression to compare
     * @param string $value2 logical expression to compare with
     * @return string logical expression
     */
    public function lt( $value1, $value2 )
    {
        $value1 = $this->getIdentifier( $value1 );
        $value2 = $this->getIdentifier( $value2 );
        return "{$value1} < {$value2}";
    }

    /**
     * Returns the SQL to check if one value is less than or equal to
     * another value.
     *
     * Example:
     * <code>
     * $q = ezcDbInstance::get()->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->lte( 'id', $q->bindValue( 1 ) ) );
     * </code>
     *
     * @param string $value1 logical expression to compare
     * @param string $value2 logical expression to compare with
     * @return string logical expression
     */
    public function lte( $value1, $value2 )
    {
        $value1 = $this->getIdentifier( $value1 );
        $value2 = $this->getIdentifier( $value2 );
        return "{$value1} <= {$value2}";
    }

    /**
     * Returns the SQL to check if a value is one in a set of
     * given values..
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
     * Optimization note: Call setQuotingValues( false ) before using in() with
     * big lists of numeric parameters. This avoid redundant quoting of numbers
     * in resulting SQL query and saves time of converting strings to
     * numbers inside RDBMS.
     *
     * @throws ezcQueryVariableParameterException if called with less than two
     *         parameters.
     * @throws ezcQueryInvalidParameterException if the 2nd parameter is an
     *         empty array.
     * @param string $column the value that should be matched against
     * @param string|array(string) $... values that will be matched against $column
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
            throw new ezcQueryInvalidParameterException( 'in', 2, 'an empty array', 'a non-empty array' );
        }

        $values = ezcQuerySelect::arrayFlatten( array_slice( $args, 1 ) );

        $column = $this->getIdentifier( $column );
        
        // Special handling of sub selects to avoid double braces
        if ( count( $values ) === 1 && $values[0] instanceof ezcQuerySubSelect )
        {
            return "{$column} IN " . $values[0]->getQuery();
        }

        $values = $this->getIdentifiers( $values );

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
                    case $value instanceof ezcQuerySubSelect:
                        $values[$key] = $value->getQuery();  // fix for PHP 5.1.6 because typecasting to string not working there.
                        break;
                    case is_int( $value ):
                    case is_float( $value ):
                        $values[$key] = (string) $value;
                        break;
                    default:
                        $values[$key] = $this->db->quote( $value );
                }
            }
        }
        
        return "{$column} IN ( " . join( ', ', $values ) . ' )';
    }

    /**
     * Returns SQL that checks if a expression is null.
     *
     * Example:
     * <code>
     * $q = ezcDbInstance::get()->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->isNull( 'id' ) );
     * </code>
     *
     * @param string $expression the expression that should be compared to null
     * @return string logical expression
     */
    public function isNull( $expression )
    {
        $expression = $this->getIdentifier( $expression );
        return "{$expression} IS NULL";
    }

    /**
     * Returns SQL that checks if an expression evaluates to a value between
     * two values.
     *
     * The parameter $expression is checked if it is between $value1 and $value2.
     *
     * Note: There is a slight difference in the way BETWEEN works on some databases.
     * http://www.w3schools.com/sql/sql_between.asp. If you want complete database
     * independence you should avoid using between().
     *
     * Example:
     * <code>
     * $q = ezcDbInstance::get()->createSelectQuery();
     * $q->select( '*' )->from( 'table' )
     *                  ->where( $q->expr->between( 'id', $q->bindValue( 1 ), $q->bindValue( 5 ) ) );
     * </code>
     *
     * @param string $expression the value to compare to
     * @param string $value1 the lower value to compare with
     * @param string $value2 the higher value to compare with
     * @return string logical expression
     */
    public function between( $expression, $value1, $value2 )
    {
        $expression = $this->getIdentifier( $expression );
        $value1 = $this->getIdentifier( $value1 );
        $value2 = $this->getIdentifier( $value2 );
        return "{$expression} BETWEEN {$value1} AND {$value2}";
    }

    /**
     * Match a partial string in a column.
     *
     * Like will look for the pattern in the column given. Like accepts
     * the wildcards '_' matching a single character and '%' matching
     * any number of characters.
     *
     * @param string $expression the name of the expression to match on
     * @param string $pattern the pattern to match with.
     */
    public function like( $expression, $pattern )
    {
        $expression = $this->getIdentifier( $expression );
        return "{$expression} LIKE {$pattern}";
    }
    // aggregate functions
    /**
     * Returns the average value of a column
     *
     * @param string $column the column to use
     * @return string
     */
    public function avg( $column )
    {
        $column = $this->getIdentifier( $column );
        return "AVG( {$column} )";
    }

    /**
     * Returns the number of rows (without a NULL value) of a column
     *
     * If a '*' is used instead of a column the number of selected rows
     * is returned.
     *
     * @param string $column the column to use
     * @return string
     */
    public function count( $column )
    {
        $column = $this->getIdentifier( $column );
        return "COUNT( {$column} )";
    }

    /**
     * Returns the highest value of a column
     *
     * @param string $column the column to use
     * @return string
     */
    public function max( $column )
    {
        $column = $this->getIdentifier( $column );
        return "MAX( {$column} )";
    }

    /**
     * Returns the lowest value of a column
     *
     * @param string $column the column to use
     * @return string
     */
    public function min( $column )
    {
        $column = $this->getIdentifier( $column );
        return "MIN( {$column} )";
    }

    /**
     * Returns the total sum of a column
     *
     * @param string $column the column to use
     * @return string
     */
    public function sum( $column )
    {
        $column = $this->getIdentifier( $column );
        return "SUM( {$column} )";
    }

    // scalar functions

    /**
     * Returns the md5 sum of $column.
     *
     * Note: Not SQL92, but common functionality
     *
     * @param string $column
     * @return string
     */
    public function md5( $column )
    {
        $column = $this->getIdentifier( $column );
        return "MD5( {$column} )";
    }

    /**
     * Returns the length of text field $column
     *
     * @param string $column
     * @return string
     */
    public function length( $column )
    {
        $column = $this->getIdentifier( $column );
        return "LENGTH( {$column} )";
    }

    /**
     * Rounds a numeric field to the number of decimals specified.
     *
     * @param string $column
     * @param int $decimals
     * @return string
     */
    public function round( $column, $decimals )
    {
        $column = $this->getIdentifier( $column );

        return "ROUND( {$column}, {$decimals} )";
    }

    /**
     * Returns the remainder of the division operation
     * $expression1 / $expression2.
     *
     * @param string $expression1
     * @param string $expression2
     * @return string
     */
    public function mod( $expression1, $expression2 )
    {
        $expression1 = $this->getIdentifier( $expression1 );
        $expression2 = $this->getIdentifier( $expression2 );
        return "MOD( {$expression1}, {$expression2} )";
    }

    /**
     * Returns the current system date and time in the database internal
     * format.
     *
     * @return string
     */
    public function now()
    {
        return "NOW()";
    }

    // string functions
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
            return "substring( {$value} from {$from} )";
        }
        else
        {
            $len = $this->getIdentifier( $len );
            return "substring( {$value} from {$from} for {$len} )";
        }
    }

    /**
     * Returns a series of strings concatinated
     *
     * concat() accepts an arbitrary number of parameters. Each parameter
     * must contain an expression or an array with expressions.
     *
     * @param string|array(string) $... strings that will be concatinated.
     */
    public function concat()
    {
        $args = func_get_args();
        $cols = ezcQuerySelect::arrayFlatten( $args );

        if ( count( $cols ) < 1 )
        {
            throw new ezcQueryVariableParameterException( 'concat', count( $args ), 1 );
        }

        $cols = $this->getIdentifiers( $cols );
        return "CONCAT( " . join( ', ', $cols ) . ' )';
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
        return "LOCATE( '{$substr}', {$value} )";
    }

    /**
     * Returns the SQL to change all characters to lowercase
     *
     * @param string $value
     * @return string
     */
    public function lower( $value )
    {
        $value = $this->getIdentifier( $value );
        return "LOWER( {$value} )";
    }

    /**
     * Returns the SQL to change all characters to uppercase
     * 
     * @param string $value
     * @return string
     */
    public function upper( $value )
    {
        $value = $this->getIdentifier( $value );
        return "UPPER( {$value} )";
    }

    /**
     * Returns the SQL to calculate the next lowest integer value from the number.
     * 
     * @param string $number
     * @return string
     */
    public function floor( $number )
    {
        $number = $this->getIdentifier( $number );
        return " FLOOR( {$number} ) ";
    }

    /**
     * Returns the SQL to calculate the next highest integer value from the number.
     *
     * @param string $number
     * @return string
     */
    public function ceil( $number )
    {
        $number = $this->getIdentifier( $number );
        return " CEIL( {$number} ) ";
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
        return "( {$value1} & {$value2} )";
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
        return "( {$value1} | {$value2} )";
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
        return "( {$value1} ^ {$value2} )";
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
        return " UNIX_TIMESTAMP( {$column} ) ";
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
        return " {$column} - INTERVAL {$expr} {$type} ";
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
        return " {$column} + INTERVAL {$expr} {$type} ";
    }

    /**
     * Returns the SQL that extracts parts from a timestamp value.
     *
     * @param string $column The column to operate on
     * @param string $type one of SECOND, MINUTE, HOUR, DAY, MONTH, or YEAR
     * @return string
     */
    public function dateExtract( $column, $type )
    {
        $type = $this->intervalMap[$type];

        $column = $this->getIdentifier( $column );
        return " EXTRACT( {$type} FROM {$column} ) ";
    }

    /**
     * Returns a searched CASE statement.
     *
     * Accepts an arbitrary number of parameters. 
     * The first parameter (array) must always be specified, the last 
     * parameter (string) specifies the ELSE result.
     *
     * Example:
     * <code>
     * $q = ezcDbInstance::get()->createSelectQuery();
     * $q->select(
     *      $q->expr->searchedCase(
     *            array( $q->expr->gte( 'column1', 20 ), 'column1' )
     *          , array( $q->expr->gte( 'column2', 50 ), 'column2' )
     *          , 'column3'
     *      )
     *  )
     *     ->from( 'table' );
     * </code>
     *
     * @throws ezcQueryVariableParameterException
     * @return string
     */
    public function searchedCase()
    {
        $args = func_get_args();
        if ( count( $args ) === 0 )
        {
            throw new ezcQueryVariableParameterException( 'searchedCase', count( $args ), 1 );
        }

        $expr = ' CASE';
        foreach ( $args as $arg )
        {
            if ( is_array( $arg ) && count( $arg ) == 2 )
            {
                $column1 = $this->getIdentifier( $arg[0] );
                $column2 = $this->getIdentifier( $arg[1] );
                $expr .= " WHEN {$column1} THEN {$column2}";
            }
            else if ( is_scalar( $arg ) )
            {
                $column = $this->getIdentifier( $arg );
                $expr .= " ELSE {$column}";
            }
        }
        $expr .= ' END ';

        return $expr;
    }
}
?>
