<?php
/**
 * File containing the ezcQuery class.
 *
 * @package Database
 * @version 1.4.7
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The ezcQuery class provides the common API for all Query objects.
 *
 * ezcQuery has three main purposes:
 * - it provides a common API for building queries through the getQuery() method.
 * - it provides a common API for binding parameters to queries through
 *   bindValue() and bindParam()
 * - it provides internal aliasing functionality that allows you to use
 *   aliases for table and column names. The substitution is done inside of the
 *   query classes before the query itself is built.
 *
 * Through the bind methods you can bind parameters and values to your
 * query. Finally you can use prepare to get a PDOStatement object
 * from your query object.
 *
 * Subclasses should provide functionality to build an actual query.
 *
 * @package Database
 * @version 1.4.7
 */
abstract class ezcQuery
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
     * Counter used to create unique ids in the bind methods.
     *
     * @var int
     */
    private $boundCounter = 0;

    /**
     * Stores the list of parameters that will be bound with doBind().
     *
     * Format: array( ':name' => &mixed )
     * @var array(string=>&mixed)
     */
    private $boundParameters = array();

    /**
     * Stores the type of a value which will we used when the value is bound.
     * 
     * @var array(string=>int)
     */
    private $boundParametersType = array();

    /**
     * Stores the list of values that will be bound with doBind().
     *
     * Format: array( ':name' => mixed )
     * @var array(string=>mixed)
     */
    private $boundValues = array();

    /**
     * Stores the type of a value which will we used when the value is bound.
     * 
     * @var array(string=>int)
     */
    private $boundValuesType = array();

    /**
     * The expression object for this class.
     *
     * @var ezcQueryExpression
     */
    public $expr = null;

    /**
     * Constructs a new ezcQuery that works on the database $db and with the aliases $aliases.
     *
     * The aliases can be used to substitute the column and table names with more
     * friendly names. E.g PersistentObject uses it to allow using property and class
     * names instead of column and table names.
     *
     * @param PDO $db
     * @param array(string=>string) $aliases
     */
    public function __construct( PDO $db, array $aliases = array() )
    {
        $this->db = $db;
        if ( $this->expr == null )
        {
            $this->expr = $db->createExpression();
        }
        if ( !empty( $aliases ) )
        {
            $this->aliases = $aliases;
            $this->expr->setAliases( $this->aliases );
        }
    }

    /**
     * Sets the aliases $aliases for this object.
     *
     * The aliases should be in the form array( "aliasName" => "databaseName" )
     * Each alias defines a relation between a user-defined name and a name
     * in the database. This is supported for table names as column names.
     *
     * The aliases can be used to substitute the column and table names with more
     * friendly names. The substitution is done when the query is built, not using
     * AS statements in the database itself.
     *
     * Example of a select query with aliases:
     * <code>
     * <?php
     * $q->setAliases( array( 'Identifier' => 'id', 'Company' => 'company' ) );
     * $q->select( 'Company' )
     *   ->from( 'table' )
     *   ->where( $q->expr->eq( 'Identifier', 5 ) );
     * echo $q->getQuery();
     * ?>
     * </code>
     *
     * This example will output SQL similar to:
     * <code>
     * SELECT company FROM table WHERE id = 5
     * </code>
     *
     * Aliasses also take effect for composite names in the form
     * tablename.columnname as the following example shows:
     * <code>
     * <?php
     * $q->setAliases( array( 'Order' => 'orders', 'Recipient' => 'company' ) );
     * $q->select( 'Order.Recipient' )
     *   ->from( 'Order' );
     * echo $q->getQuery();
     * ?>
     * </code>
     *
     * This example will output SQL similar to:
     * <code>
     * SELECT orders.company FROM orders;
     * </code>
     *
     * It is even possible to have an alias point to a table name/column name
     * combination. This will only work for alias names without a . (dot):
     * <code>
     * <?php
     * $q->setAliases( array( 'Order' => 'orders', 'Recipient' => 'orders.company' ) );
     * $q->select( 'Recipient' )
     *   ->from( 'Order' );
     * echo $q->getQuery();
     * ?>
     * </code>
     *
     * This example will output SQL similar to:
     * <code>
     * SELECT orders.company FROM orders;
     * </code>
     *
     * In the following example, the Recipient alias will not be used, as it is
     * points to a fully qualified name - the Order alias however is used:
     * <code>
     * <?php
     * $q->setAliases( array( 'Order' => 'orders', 'Recipient' => 'orders.company' ) );
     * $q->select( 'Order.Recipient' )
     *   ->from( 'Order' );
     * echo $q->getQuery();
     * ?>
     * </code>
     *
     * This example will output SQL similar to:
     * <code>
     * SELECT orders.Recipient FROM orders;
     * </code>
     *
     * @param array(string=>string) $aliases
     * @return void
     */
    public function setAliases( array $aliases )
    {
        $this->aliases = $aliases;
        $this->expr->setAliases( $aliases );
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
     * This can method handles composite identifiers separated by a dot ('.').
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
                 is_string( $alias ) &&
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
     * Binds the value $value to the specified variable name $placeHolder.
     *
     * This method provides a shortcut for PDOStatement::bindValue
     * when using prepared statements.
     *
     * The parameter $value specifies the value that you want to bind. If
     * $placeholder is not provided bindValue() will automatically create a
     * placeholder for you. An automatic placeholder will be of the name
     * 'ezcValue1', 'ezcValue2' etc.
     *
     * For more information see {@link http://php.net/pdostatement-bindparam}
     *
     * Example:
     * <code>
     * $value = 2;
     * $q->eq( 'id', $q->bindValue( $value ) );
     * $stmt = $q->prepare(); // the value 2 is bound to the query.
     * $value = 4;
     * $stmt->execute(); // executed with 'id = 2'
     * </code>
     *
     * @see doBind()
     * @param mixed $value
     * @param string $placeHolder the name to bind with. The string must start with a colon ':'.
     * @return string the placeholder name used.
     */
    public function bindValue( $value, $placeHolder = null, $type = PDO::PARAM_STR )
    {
        if ( $placeHolder === null )
        {
            $this->boundCounter++;
            $placeHolder = ":ezcValue{$this->boundCounter}";
        }

        $this->boundValues[$placeHolder] = $value;
        $this->boundValuesType[$placeHolder] = $type;

        return $placeHolder;
    }

    /**
     * Binds the parameter $param to the specified variable name $placeHolder..
     *
     * This method provides a shortcut for PDOStatement::bindParam
     * when using prepared statements.
     *
     * The parameter $param specifies the variable that you want to bind. If
     * $placeholder is not provided bind() will automatically create a
     * placeholder for you. An automatic placeholder will be of the name
     * 'ezcValue1', 'ezcValue2' etc.
     *
     * For more information see {@link http://php.net/pdostatement-bindparam}
     *
     * Example:
     * <code>
     * $value = 2;
     * $q->eq( 'id', $q->bindParam( $value ) );
     * $stmt = $q->prepare(); // the parameter $value is bound to the query.
     * $value = 4;
     * $stmt->execute(); // executed with 'id = 4'
     * </code>
     *
     * @see doBind()
     * @param &mixed $param
     * @param string $placeHolder the name to bind with. The string must start with a colon ':'.
     * @return string the placeholder name used.
     */
    public function bindParam( &$param, $placeHolder = null, $type = PDO::PARAM_STR )
    {
        if ( $placeHolder === null )
        {
            $this->boundCounter++;
            $placeHolder = ":ezcValue{$this->boundCounter}";
        }
        
        $this->boundParameters[$placeHolder] =& $param;
        $this->boundParametersType[$placeHolder] = $type;

        return $placeHolder;
    }

    /**
     * Resets the bound values and parameters to empty.
     *
     * This is useful if your query can be reset and used multiple times.
     *
     * @return void
     */
    protected function resetBinds()
    {
        $this->boundCounter = 0;
        $this->boundParameters = array();
        $this->boundValues = array();
    }

    /**
     * Performs binding of variables bound with bindValue and bindParam on the statement $stmt.
     *
     * This method must be called if you have used the bind methods
     * in your query and you build the method yourself using build.
     *
     * @param PDOStatement $stmt
     * @return void
     */
    public function doBind( PDOStatement $stmt )
    {
        foreach ( $this->boundValues as $key => $value )
        {
            try
            {
                $stmt->bindValue( $key, $value, $this->boundValuesType[$key] );
            }
            catch ( PDOException $e )
            {
                // see comment below
            }
        }
        foreach ( $this->boundParameters as $key => &$value )
        {
            try
            {
                $stmt->bindParam( $key, $value, $this->boundParametersType[$key] );
            }
            catch ( PDOException $e )
            {
                // we are ignoring this exception since it may only occur when
                // a bound parameter is not found in the query anymore.
                // this can happen if either drop an expression with a bound value
                // created with this query or if you remove a bind in a query by
                // replacing it with another one.
                // the only other way to avoid this problem is parse the string for the
                // bound variables. Note that a simple search will not do since the variable
                // name may occur in a string.
            }
        }
    }

    /**
     * Returns a prepared statement from this query which can be used for execution.
     *
     * The returned object is a PDOStatement for which you can find extensive
     * documentation in the PHP manual:
     * {@link http://php.net/pdostatement-bindcolumn}
     *
     * prepare() automatically calls doBind() on the statement.
     * @return PDOStatement
     */
    public function prepare()
    {
        $stmt = $this->db->prepare( $this->getQuery() );
        $this->doBind( $stmt );
        return $stmt;
    }

    /**
     * Returns all the elements in $array as one large single dimensional array.
     *
     * @todo public? Currently this is needed for QueryExpression.
     * @param array $array
     * @return array
     */
    static public function arrayFlatten( array $array )
    {
        $flat = array();
        foreach ( $array as $arg )
        {
            switch ( gettype( $arg ) )
            {
                case 'array':
                    $flat = array_merge( $flat, $arg );
                    break;

                default:
                    $flat[] = $arg;
                    break;
            }
        }
        return $flat;
    }

    /**
     * Return SQL string for query.
     *
     * Typecasting to (string) should be used to make __toString() to be called
     * with PHP 5.1.  This will not be needed in PHP 5.2 and higher when this
     * object is used in a string context.
     *
     * Example:
     * <code>
     * $q->select('*')
     *   ->from( 'table1' )
     *   ->where ( $q->expr->eq( 'name', $q->bindValue( "Beeblebrox" ) ) );
     * echo $q, "\n";
     * </code>
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getQuery();
    }

    /**
     * Returns the ezcQuerySubSelect query object.
     *
     * This method creates new ezcQuerySubSelect object
     * that could be used for building correct
     * subselect inside query.
     *
     * @return ezcQuerySubSelect
     */
    public function subSelect()
    {
        return new ezcQuerySubSelect( $this );
    }

    /**
     * Returns the query string for this query object.
     *
     * @throws ezcQueryInvalidException if it was not possible to build a valid query.
     * @return string
     */
    abstract public function getQuery();
}
?>
