<?php
/**
 * File containing the ezcPersistentObjectRelations class.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcPersistentObjectRelations class.
 * 
 * @access private
 *
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentObjectRelations extends ArrayObject
{
    /**
     * Stores the relation objects. 
     * 
     * @var array(ezcPersistentRelation)
     */
    private $relations;

    /**
     * Create a new instance.
     * Implicitly done in constructor of 
     * 
     * @return void
     */
    public function __construct()
    {
        $this->relations = array();
        parent::__construct( $this->relations );
    }

    /**
     * See SPL interface ArrayAccess.
     * 
     * @param string $offset 
     * @param ezcPersistentRelation $value 
     * @return void
     */
    public function offsetSet( $offset, $value )
    {
        if ( !( $value instanceof ezcPersistentRelation ) && !( $value instanceof ezcPersistentRelationCollection ) )
        {
            throw new ezcBaseValueException( 'value', $value, 'ezcPersistentRelation or ezcPersistentRelationCollection' );
        }
        if ( !is_string( $offset ) || strlen( $offset ) < 1 )
        {
            throw new ezcBaseValueException( 'offset', $offset, 'string, length > 0' );
        }
        parent::offsetSet( $offset, $value );
    }

    /**
     * See SPL class ArrayObject.
     * Performs additional value checks on the array.
     * 
     * @param array(ezcPersistentRelation) $array New relations array.
     * @return void
     */
    public function exchangeArray( $array )
    {
        foreach ( $array as $offset => $value )
        {
            if ( !( $value instanceof ezcPersistentRelation ) && !( $value instanceof ezcPersistentRelationCollection ) )
            {
                throw new ezcBaseValueException( 'value', $value, 'ezcPersistentRelation or ezcPersistentRelationCollection' );
            }
            if ( !is_string( $offset ) || strlen( $offset ) < 1 )
            {
                throw new ezcBaseValueException( 'offset', $offset, 'string, length > 0' );
            }
        }
        parent::exchangeArray( $array );
    }

    /**
     * See SPL class ArrayObject.
     * Performs check if only 0 is used as a flag.
     * 
     * @param int $flags Must be 0.
     * @return void
     */
    public function setFlags( $flags )
    {
        if ( $flags !== 0 )
        {
            throw new ezcBaseValueException( 'flags', $flags, '0' );
        }
    }

    /**
     * Appending is not supported. 
     * 
     * @param mixed $value 
     * @return void
     */
    public function append( $value )
    {
        throw new Exception( 'Operation append is not supported by this object.' );
    }
    
    /**
     * Sets the state on deserialization.
     * 
     * @param array $state
     * @return ezcPersistentObjectRelations
     */
    public static function __set_state( array $state )
    {
        $relations = new ezcPersistentObjectRelations();
        if ( isset( $state['columns'] ) && count( $state ) === 1 )
        {
            $relations->exchangeArray( $state['columns'] );
        }
        else
        {
            // Old exported objects.
            $relations->exchangeArray( $state );
        }
        return $relations;
    }
}

?>
