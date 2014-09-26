<?php
/**
 * File containing the ezcQuerySelectSqlite class.
 *
 * @package Database
 * @version 1.4.7
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * SQLite specific implementation of ezcQuery.
 *
 * This class reimplements methods where SQLite differs from the standard
 * implementation in ezcQuery. The only difference is the right join syntax.
 *
 * @see ezcQuery
 * @package Database
 * @version 1.4.7
 */
class ezcQuerySelectSqlite extends ezcQuerySelect
{
    /**
     * Store info for building emulation of right joins in FROM clause.
     * 
     * This array contains null if there was no calls to rightJoin().
     * When call to rightJoin() occurs an item of right join info added.
     * Right join info is array that consists of two arrays: 'tables' and 'conditions'.
     * These arrays filled with values from parameters of rightJoin().
     * If from() was called then new right join info item added to $rightJoins.
     */
    protected $rightJoins = array( null );

    /**
     * Store tables that appear in FROM clause.
     * 
     * Used for building fromString every time when it requested
     */
    protected $fromTables = array();

    /**
     * Constructs a new ezcQuerySelectSqlite object.
     *
     * @param PDO $db
     */
    public function __construct( PDO $db )
    {
        parent::__construct( $db );
    }


    /**
     * Resets the query object for reuse.
     *
     * @return void
     */
    public function reset()
    {
        parent::reset();
        $this->fromTables = array();
        $this->rightJoins = array( null );
    }

    /**
     * Select which tables you want to select from.
     *
     * from() accepts an arbitrary number of parameters. Each parameter
     * must contain either the name of a table or an array containing
     * the names of tables..
     * from() could be invoked several times. All provided arguments 
     * added to the end of $fromString.
     * 
     * Additional actions performed to emulate right joins in SQLite.
     *
     * Example:
     * <code>
     * // the following code will produce the SQL
     * // SELECT id FROM t2 LEFT JOIN t1 ON t1.id = t2.id
     * $q->select( 'id' )->from( $q->rightJoin( 't1', 't2', 't1.id', 't2.id' ) );
     * 
     * // the following code will produce the same SQL
     * // SELECT id FROM t2 LEFT JOIN t1 ON t1.id = t2.id
     * $q->select( 'id' )->from( 't1' )->rightJoin( 't2', 't1.id', 't2.id' );
     * </code>
     *
     * @throws ezcQueryVariableParameterException if called with no parameters.
     * @param string|array(string) $... Either a string with a table name or an array of table names.
     * @return a pointer to $this
     */
    public function from()
    {
        $args = func_get_args();
        $tables = self::arrayFlatten( $args );
        if ( count( $tables ) < 1 )
        {
            throw new ezcQueryVariableParameterException( 'from', count( $args ), 1 );
        }
        $this->lastInvokedMethod = 'from';
        $tables = $this->getIdentifiers( $tables );

        $this->fromTables = array_merge( $this->fromTables, $tables );

        $this->fromString ='FROM '.join( ', ', $this->fromTables );

        // adding right join part of query to the end of fromString.
        $rightJoinPart = $this->buildRightJoins();
        if ( $rightJoinPart != '' ) 
        {
            $this->fromString .= ', '.$rightJoinPart;
        }

        // adding new empty entry to $rightJoins if last entry was already filled
        $lastRightJoin = end( $this->rightJoins );
        if ( $lastRightJoin != null ) 
        {
            $this->rightJoins[] = null; // adding empty stub to the rightJoins
                                        // it could be filled by next rightJoin()
        }
        return $this;
    }

    /**
     * Returns SQL string with part of FROM clause that performs right join emulation. 
     * 
     * SQLite don't support right joins directly but there is workaround.
     * identical result could be acheived using right joins for tables in reverce order.
     * 
     * String created from entries of $rightJoins. 
     * One entry is a complete table_reference clause of SQL FROM clause.
     * 
     * <code>
     * rightJoins[0][tables] = array( 'table1', 'table2', 'table3' )
     * rightJoins[0][conditions] = array( condition1, condition2 )
     * </code>
     * forms SQL: 'table3 LEFT JOIN table2 condition2 ON LEFT JOIN table1 ON condition1'.
     * 
     * 
     * @return string the SQL call including all right joins set in query.
     */
    private function buildRightJoins()
    {
        $resultArray = array();

        foreach ( $this->rightJoins as $rJoinPart )
        {
            $oneItemResult = '';
            if ( $rJoinPart === null ) 
            {
                break; // this is last empty entry so cancel adding.
            }

            // reverse lists of tables and conditions to make LEFT JOIN 
            // that will produce result equal to original right join.
            $reversedTables = array_reverse( $rJoinPart['tables'] );
            $reversedConditions = array_reverse( $rJoinPart['conditions'] );

            // adding first table.
            list( $key, $val ) = each( $reversedTables ); 
            $oneItemResult .= $val;

            while ( list( $key, $nextCondition ) = each( $reversedConditions ) )
            {
                list( $key2, $nextTable ) = each( $reversedTables );   
               $oneItemResult .= " LEFT JOIN {$nextTable} ON {$nextCondition}";
            }
            $resultArray[] = $oneItemResult;
        }

        return join( ', ', $resultArray );
    }

    /**
     * Returns the SQL for a right join or prepares $fromString for a right join.
     * 
     * This method could be used in two forms:
     *
     * <b>rightJoin( 't2', $joinCondition )</b>
     * 
     * Takes 2 string arguments and returns ezcQuery.
     *
     * The first parameter is the name of the table to join with. The table to
     * which is joined should have been previously set with the from() method.
     *
     * The second parameter should be a string containing a join condition that
     * is returned by an ezcQueryExpression.
     *
     * Example:
     * <code>
     * // the following code will produce the SQL
     * // SELECT id FROM t1 LEFT JOIN t2 ON t1.id = t2.id
     * $q->select( 'id' )->from( 't1' )->rightJoin( 't2', $q->expr->eq('t1.id', 't2.id' ) );
     * </code>
     *
     * <b>rightJoin( 't2', 't1.id', 't2.id' )</b>
     * 
     * Takes 3 string arguments and returns ezcQuery. This is a simplified form
     * of the 2 parameter version.  rightJoin( 't2', 't1.id', 't2.id' ) is
     * equal to rightJoin( 't2', $this->expr->eq('t1.id', 't2.id' ) );
     *
     * The first parameter is the name of the table to join with. The table to
     * which is joined should have been previously set with the from() method.
     * 
     * The second parameter is the name of the column on the table set
     * previously with the from() method and the third parameter the name of
     * the column to join with on the table that was specified in the first
     * parameter.
     *
     * Example:
     * <code>
     * // the following code will produce the SQL
     * // SELECT id FROM t1 LEFT JOIN t2 ON t1.id = t2.id
     * $q->select( 'id' )->from( 't1' )->rightJoin( 't2', 't1.id', 't2.id' );
     * </code>
     *
     * @apichange Remove 4 argument version.
     *
     * @throws ezcQueryInvalidException if called with inconsistent parameters or if
     *         invoked without preceding call to from().
     *
     * @param string $table2,... The table to join with, followed by either the
     *                           two join columns, or a join condition.
     * @return ezcQuery
     */
    public function rightJoin()
    {
        $args = func_get_args();
        $passedArgsCount = func_num_args();
        if ( $passedArgsCount < 2 || $passedArgsCount > 4 )
        {
            throw new ezcQueryInvalidException( 'SELECT', "Wrong argument count passed to {$type}Join(): {$passedArgsCount}" );
        }

        // deprecated syntax
        if ( $passedArgsCount == 4 ) 
        {
            if ( is_string( $args[0] ) && is_string( $args[1] ) &&
                 is_string( $args[2] ) && is_string( $args[3] ) 
               ) 
            {
                $table1 = $this->getIdentifier( $args[0] );
                $table2 = $this->getIdentifier( $args[1] );
                $column1 = $this->getIdentifier( $args[2] );
                $column2 = $this->getIdentifier( $args[3] );

                return "{$table2} LEFT JOIN {$table1} ON {$column1} = {$column2}";
            }
            else
            {
                throw new ezcQueryInvalidException( 'SELECT', 'Inconsistent types of arguments passed to rightJoin().' );
            }
        }

        // using from()->rightJoin() syntax assumed, so check if last call was to from()
        if ( $this->lastInvokedMethod != 'from' )
        {
            throw new ezcQueryInvalidException( 'SELECT', 'Invoking rightJoin() not immediately after from().' );
        }

        $table = '';
        if ( !is_string( $args[0] ) ) 
        {
            throw new ezcQueryInvalidException( 'SELECT', 
                     'Inconsistent type of first argument passed to rightJoin(). Should be string with name of table.' );
        }
        $table = $this->getIdentifier( $args[0] );

        $condition = '';
        if ( $passedArgsCount == 2 && is_string( $args[1] ) ) 
        {
            $condition = $args[1];
        }
        else if ( $passedArgsCount == 3 && is_string( $args[1] ) && is_string( $args[2] ) ) 
        {
            $arg1 = $this->getIdentifier( $args[1] );
            $arg2 = $this->getIdentifier( $args[2] );

            $condition = "{$arg1} = {$arg2}";
        }

        // If rightJoin info entry is empty than remove last table from
        // fromTables list and add it at first place to the list of tables in
        // correspondent rightJoin info entry.
        // Subsequent calls to rightJoin() without from() will just add one
        // table and one condition to the correspondent arrays.

        if ( end( $this->rightJoins ) === null ) // fill last rightJoin info entry with table name.
        {
            $lastTable = array_pop ( $this->fromTables );
            array_pop( $this->rightJoins );
            $this->rightJoins[count( $this->rightJoins )]['tables'][] = $lastTable;
        }
        
        if ( $table != '' && $condition != '' )
        {
            $this->rightJoins[count( $this->rightJoins ) - 1]['tables'][] = $table;
            $this->rightJoins[count( $this->rightJoins ) - 1]['conditions'][] = $condition;
        }
         
        // build fromString using fromTables and add right joins stuff to te end.
        $this->fromString = 'FROM ' . join( ', ', $this->fromTables );
        $this->fromString .= $this->buildRightJoins();

        return $this;
    }
}
?>
