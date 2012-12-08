<?php
/**
 * File containing the ezcPersistentRelationFindQuery class.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Find query object to be used with ezcPersistentSessionIdentityDecorator.
 *
 * This special find query is returned by {@link
 * ezcPersistentSessionIdentityDecorator::createRelationFindQuery()}. It fulfills the
 * same purpose as its parent class, but can store the $relationSource object
 * and a $relationSetName in addition.
 *
 * An instance of this object can simply be used like an {@link
 * ezcPersistentFindQuery}.
 *
 * @property-read string $relationSetName
 *                Name of the named related object set to be stored in the
 *                identity map.
 * @property-read object $relationSource
 *                Source objects to which related objects should be found.
 *
 * @see ezcPersistentFindWithRelationsQuery
 *
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentRelationFindQuery extends ezcPersistentFindQuery
{
    /**
     * Creates a new relation find query.
     *
     * Creates a new relation find query from the query object $q and the
     * given $className. Optionally, a $relationSetName and the $relationSource
     * object can be given. Providing these results in the creation of a named
     * related object set when objects are found using {@link
     * ezcPersistentSessionIdentityDecorator::find()}.
     * 
     * @param ezcQuerySelect $query
     * @param string $className
     * @param string $relationSetName
     * @param ezcPersistentObject $relationSource
     */
    public function __construct( ezcQuerySelect $query, $className, $relationSetName = null, $relationSource = null )
    {
        parent::__construct( $query, $className );
        $this->__set( 'relationSetName', $relationSetName );
        $this->__set( 'relationSource', $relationSource );
    }

    /**
     * Property set access.
     * 
     * @param string $propertyName 
     * @param mixed $properyValue
     * @ignore
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the desired property could not be found.
     * @throws ezcBaseValueException
     *         if $properyValue is not valid for $propertyName.
     */
    public function __set( $propertyName, $properyValue )
    {
        switch ( $propertyName )
        {
            case 'relationSource':
                if ( !is_object( $properyValue ) && $properyValue !== null )
                {
                    throw new ezcBaseValueException( $propertyName, $properyValue, 'Object or null' );
                }
                $this->properties[$propertyName] = $properyValue;
                return;
            case 'relationSetName':
                if ( !is_string( $properyValue ) && $properyValue !== null )
                {
                    throw new ezcBaseValueException( $propertyName, $properyValue, 'string or null' );
                }
                $this->properties[$propertyName] = $properyValue;
                return;
        }

        parent::__set( $propertyName, $properyValue );
    }
}

?>
