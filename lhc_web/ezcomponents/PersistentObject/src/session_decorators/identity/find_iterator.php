<?php
/**
 * File containing the ezcPersistentIdentityFindIterator class.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Iterator class with identity mapping facilities.
 *
 * {@link ezcPersistentFindIterator} only instantiates one object which is
 * reused for each iteration. This saves memory and is faster than fetching and
 * instantiating the result set in one go. This class in addition performs
 * identity mapping and does not re-created objects that already exist in the
 * application scope.
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
 * @see ezcPersistentSessionIdentityDecorator
 *
 * @version 1.7.1
 * @package PersistentObject
 */
class ezcPersistentIdentityFindIterator extends ezcPersistentFindIterator
{
    /**
     * Identity map. 
     * 
     * @var ezcPersistentIdentityMap
     */
    protected $idMap;

    /**
     * Identity session options 
     * 
     * @var ezcPersistentSessionIdentityDecoratorOptions
     */
    protected $options;

    /**
     * Initializes the iterator with the statement $stmt and the definition $def..
     *
     * The statement $stmt must be executed but not used to retrieve any results yet.
     * The iterator will return objects with they persistent object type provided by
     * $def.
     *
     * The $idMap will be used to retrieve existing identities and to store new
     * ones, if discovered. The $options object contains the options used by
     * the identity decorator which uses this instance.
     *
     * @param PDOStatement $stmt
     * @param ezcPersistentObjectDefinition $def
     * @param ezcPersistentIdentityMap $idMap
     * @param ezcPersistentSessionIdentityDecoratorOptions $options
     */
    public function __construct(
        PDOStatement $stmt,
        ezcPersistentObjectDefinition $def,
        ezcPersistentIdentityMap $idMap,
        ezcPersistentSessionIdentityDecoratorOptions $options
    )
    {
        parent::__construct( $stmt, $def );
        $this->idMap   = $idMap;
        $this->options = $options;
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
        $object = parent::next();

        if ( $object !== null )
        {
            $identity = null;

            if ( !$this->options->refetch )
            {
                $class = get_class( $object );
                $state = $object->getState();

                $identity = $this->idMap->getIdentity(
                    $class,
                    $state[$this->def->idProperty->propertyName]
                );
            }

            if ( $identity !== null )
            {
                $this->object = $identity;
            }
            else
            {
                $this->idMap->setIdentity( $object );
            }
        }

        return $this->object;
    }
}
?>
