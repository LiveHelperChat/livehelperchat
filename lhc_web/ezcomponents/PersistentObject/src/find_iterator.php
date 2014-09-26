<?php
/**
 * File containing the ezcPersistentFindIterator class
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This class provides functionality to iterate over a database
 * result set in the form of persistent objects.
 *
 * Note: The iterator does not return only a single instance anymore, since
 * this has been fixed as a bug fix. A new instance is cloned for each
 * iteration step.
 *
 * You must loop over the complete resultset of the iterator or
 * flush it before executing new queries.
 *
 * Example:
 * <code>
 *  $q = $session->createFindQuery( 'Person' );
 *  $q->where( $q->expr->gt( 'age', $q->bindValue( 15 ) ) )
 *    ->orderBy( 'name' )
 *    ->limit( 10 );
 *  $objects = $session->findIterator( $q, 'Person' );
 *
 *  foreach ( $objects as $object )
 *  {
 *     if ( ... )
 *     {
 *        $objects->flush();
 *        break;
 *     }
 *  }
 * </code>
 *
 * @version 1.7.1
 * @package PersistentObject
 */
class ezcPersistentFindIterator implements Iterator
{
    /**
     * Stores the current object of the iterator.
     *
     * This variable is null if there is no current object.
     *
     * @var object
     */
    protected $object = null;

    /**
     * The statement to retrieve data from.
     *
     * @var PDOStatement
     */
    private $stmt = null;

    /**
     * The definition of the persistent object type.
     *
     * $var ezcPersistentObjectDefinition
     */
    protected $def = null;

    /**
     * Initializes the iterator with the statement $stmt and the definition $def..
     *
     * The statement $stmt must be executed but not used to retrieve any results yet.
     * The iterator will return objects with they persistent object type provided by
     * $def.
     * @param PDOStatement $stmt
     * @param ezcPersistentObjectDefinition $def
     */
    public function __construct( PDOStatement $stmt, ezcPersistentObjectDefinition $def )
    {
        $this->stmt = $stmt;
        $this->def = $def;
    }

    /**
     * Sets the iterator to point to the first object in the result set.
     *
     * @return void
     */
    public function rewind()
    {
        if ( $this->object === null )
        {
            $this->next();
        }
    }

    /**
     * Returns the current object of this iterator.
     *
     * Returns null if there is no current object.
     *
     * @return object
     */
    public function current()
    {
        return $this->object;
    }

    /**
     * Returns null.
     *
     * Persistent objects do not have a key. Hence, this method always returns
     * null.
     *
     * @return null
     */
    public function key()
    {
        return null;
    }

    /**
     * Returns the next persistent object in the result set.
     *
     * The next object is set to the current object of the iterator.
     * Returns null and sets the current object to null if there
     * are no more results in the result set.
     *
     * @return object
     */
    public function next()
    {
        $row = false;
        try
        {
            $row = $this->stmt->fetch( PDO::FETCH_ASSOC );
        }
        catch ( PDOException $e ) // MySQL 5.0 throws this if the statement is not executed.
        {
            $this->object = null;
            return;
        }

        // SQLite returns empty array on faulty statement!
        if ( $row !== false && ( is_array( $row ) && sizeof( $row ) != 0 ) && $this->checkDef() )
        {
            if ( $this->object == null ) // no object yet
            {
                $this->object = new $this->def->class;
            }
            else
            {
                // Issue #14473: ezcPersistentFindIterator overwrites last object
                $this->object = clone $this->object;
            }
            $this->object->setState( ezcPersistentStateTransformer::rowToStateArray( $row, $this->def ) );
        }
        else // no more objects in the result set
        {
            $this->object = null;
        }
        return $this->object;
    }

    /**
     * Checks if the persistence defintion contains at least a table and a
     * class name.
     *
     * @return bool
     */
    private function checkDef()
    {
        return $this->def->class !== null && $this->def->table !== null;
    }

    /**
     * Returns true if there is a current object.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->object !== null ? true : false;
    }

    /**
     * Clears the results from the iterator.
     *
     * This method must be called if you decide not to iterate over the complete resultset.
     * Failure to do so may result in errors on subsequent SQL queries.
     *
     * @return void
     */
    public function flush()
    {
        $this->stmt->closeCursor();
    }
}
?>
