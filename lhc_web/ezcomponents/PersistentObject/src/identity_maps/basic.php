<?php
/**
 * File containing the ezcPersistentBasicIdentityMap class.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Default identity map used in ezcPersistentSessionIdentityDecorator.
 *
 * An instance of this class is used in {@link
 * ezcPersistentSessionIdentityDecorator} to perform the internal work of
 * storing and retrieving object identities.
 * 
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentBasicIdentityMap implements ezcPersistentIdentityMap
{
    /**
     * Object identities.
     *
     * Structure:
     *
     * <code>
     * <?php
     * array(
     *     '<className>' => array(
     *         '<id1>' => ezcPersistentIdentity(),
     *         '<id2>' => ezcPersistentIdentity(),
     *         // ...
     *     ),
     *     '<anotherClassName>' => array(
     *         '<idA>' => ezcPersistentIdentity(),
     *         '<idB>' => ezcPersistentIdentity(),
     *         // ...
     *     ),
     *     // ...
     * );
     * ?>
     * </code>
     * 
     * @var array(string=>array(mixed=>ezcPersistentIdentity))
     */
    protected $identities = array();

    /**
     * Definition manager used by {@link ezcPersistentSession}.
     * 
     * @var ezcPersistentDefinitionManager
     */
    protected $definitionManager;

    /**
     * Creates a new identity map.
     *
     * Creates a new identity map, which makes use of the given
     * $definitionManager to determine object identities and relations.
     * 
     * @param ezcPersistentDefinitionManager $definitionManager 
     */
    public function __construct( ezcPersistentDefinitionManager $definitionManager )
    {
        $this->definitionManager = $definitionManager;
    }

    /**
     * Records the identity of $object.
     *
     * Records the identity of $object. If an identity is already recorded for
     * this object, it is silently replaced. The user of this method must take
     * care of checking for already recorded identities of the given $object
     * itself.
     *
     * @param ezcPersistentObject $object 
     */
    public function setIdentity( $object )
    {
        $class = get_class( $object );
        $def   = $this->definitionManager->fetchDefinition( $class );
        $state = $object->getState();
        $id    = $state[$def->idProperty->propertyName];
        
        if ( !isset( $this->identities[$class] ) )
        {
            $this->identities[$class] = array();
        }

        $newIdentity = new ezcPersistentIdentity( $object );
        if ( isset( $this->identities[$class][$id] ) )
        {
            $this->replaceIdentityReferences(
                $this->identities[$class][$id],
                $newIdentity
            );
        }

        $this->identities[$class][$id] = $newIdentity;
    }

    /**
     * Returns the object of $class with $id or null.
     *
     * Returns the object of $class with $id, if its identity has already been
     * recorded. Otherwise null is returned.
     * 
     * @param string $class 
     * @param mixed $id 
     * @return object($class)|null
     */
    public function getIdentity( $class, $id )
    {
        if ( !isset( $this->identities[$class] ) )
        {
            return null;
        }
        if ( !isset( $this->identities[$class][$id] ) )
        {
            return null;
        }
        return $this->identities[$class][$id]->object;
    }

    /**
     * Removes the object of $class width $id from the map.
     *
     * Removes the identity of the object of $class with $id from the map and
     * deletes all references of it. If the identity does not exist, the call
     * is silently ignored.
     * 
     * @param string $class 
     * @param mixed $id 
     */
    public function removeIdentity( $class, $id )
    {
        if ( isset( $this->identities[$class][$id] ) )
        {
            $this->removeIdentityReferences( $this->identities[$class][$id] );
            unset( $this->identities[$class][$id] );
        }
    }

    /**
     * Records a set of $relatedObjects to $sourceObject.
     *
     * Records the given set of $relatedObjects for $sourceObject.
     * $relationName is the optional name of the relation, which must be
     * provided, if multiple relations from $sourceObject to the class of the
     * objects in $relatedObjects exist.
     *
     * In case a set of related objects has already been recorded for
     * $sourceObject and the class of the objects in $relatedObjects (and
     * optionally $relationName), the existing set is silently replaced and all
     * references to it are removed.
     *
     * If for any object in $relatedObjects no identity is recorded, yet, it
     * will be recorded. Otherwise, the object will be replaced by its existing
     * identity in the set. Except for if the $replaceIdentities parameter is
     * set to true: In this case a new identity will be recorded for every
     * object in $relatedObjects, replacing potentially existing ones silently.
     *
     * If the given array of $relatedObjects is inconsistent (any contained
     * object is not of $relatedClass), an {@link
     * ezcPersistentIdentityRelatedObjectsInconsistentException} is thrown.
     *
     * To avoid a call to {@link getRelatedObjects()} after this method has
     * been called, the recorded set of related objects (including potentially
     * replaced identities) is returned.
     * 
     * @param ezcPersistentObject $sourceObject
     * @param array(ezcPersistentObject) $relatedObjects 
     * @param string $relatedClass 
     * @param string $relationName 
     * @param bool $replaceIdentities
     *
     * @return array(mixed=>object($relatedClass))
     *
     * @throws ezcPersistentIdentityRelatedObjectsInconsistentException
     *         if an object in $relatedObjects is not of $relatedClass.
     *
     */
    public function setRelatedObjects( $sourceObject, array $relatedObjects, $relatedClass, $relationName = null, $replaceIdentities = false )
    {
        $srcClass = get_class( $sourceObject );
        $srcDef   = $this->definitionManager->fetchDefinition( $srcClass );
        $srcState = $sourceObject->getState();
        $srcId    = $srcState[$srcDef->idProperty->propertyName];
        $relDef   = $this->definitionManager->fetchDefinition( $relatedClass );

        if ( !isset( $this->identities[$srcClass][$srcId] ) )
        {
            throw new ezcPersistentIdentityMissingException(
                $srcClass,
                $srcId
            );
        }

        $srcIdentity = $this->identities[$srcClass][$srcId];

        // Sanity checks already performed while loading related objects

        $relationStoreName = $this->createRelationStoreName(
            $relatedClass,
            $relationName
        );

        // Remove references before replacing a set
        if ( isset( $srcIdentity->relatedObjects[$relationStoreName] ) )
        {
            $this->removeReferences( $srcIdentity->relatedObjects[$relationStoreName] );
        }

        $relStore = new ArrayObject();
        foreach ( $relatedObjects as $relObj )
        {
            if ( !( $relObj instanceof $relatedClass ) )
            {
                // Cleanup already set references before bailing out
                $this->removeReferences( $relStore );
                throw new ezcPersistentIdentityRelatedObjectsInconsistentException(
                    $srcClass, $srcId, $relatedClass, get_class( $relObj )
                );
            }

            $relState = $relObj->getState();
            $relId    = $relState[$relDef->idProperty->propertyName];

            // Check and replace identities
            if ( !isset( $this->identities[$relatedClass][$relId] ) )
            {
                $this->identities[$relatedClass][$relId] = new ezcPersistentIdentity(
                    $relObj
                );
            }
            else if ( $replaceIdentities )
            {
                // Replace identities on re-fetch
                $newIdentity = new ezcPersistentIdentity( $relObj );
                $this->replaceIdentityReferences(
                    $this->identities[$relatedClass][$relId],
                    $newIdentity
                );
                $this->identities[$relatedClass][$relId] = $newIdentity;
            }
            else
            {
                $relObj = $this->identities[$relatedClass][$relId]->object;
            }

            $relStore[$relId] = $relObj;

            // Store reference
            $this->identities[$relatedClass][$relId]->references->attach( $relStore );
        }
        
        $srcIdentity->relatedObjects[$relationStoreName] = $relStore;

        return $relStore->getArrayCopy();
    }

    /**
     * Records a named set of $relatedObjects for $sourceObject.
     *
     * Records the given array of $relatedObjects with as a "named related
     * object sub-set" for $sourceObject, using $setName. A named "named
     * related object sub-set" contains only objects related to $sourceObject,
     * but not necessarily all such objects of a certain class. Such a set is
     * the result of {@link ezcPersistentSessionIdentityDecorator::find()} with
     * a find query generated by {@link
     * ezcPersistentSessionIdentityDecorator::createFindQueryWithRelations()}
     * and manipulated using a WHERE clause.
     *
     * In case a named set of related objects with $setName has already been
     * recorded for $sourceObject, this set is silently replaced.
     *
     * If for any of the objects in $relatedObjects no identity is recorded,
     * yet, it will be recorded. Otherwise, the object will be replaced by its
     * existing identity in the set. Except for if $replaceIdentities is set to
     * true: In this case a new identity will be recorded for every object in
     * $relatedObjects.
     *
     * The method returns the created set of related objects to avoid another
     * call to {@link getRelatedObjectSet()} by the using objct.
     * 
     * @param ezcPersistentObject $sourceObject
     * @param array(ezcPersistentObject) $relatedObjects 
     * @param string $setName 
     * @param bool $replaceIdentities
     *
     * @return array(ezcPersistentObject)
     *
     * @throws ezcPersistentIdentityRelatedObjectsInconsistentException
     *         if an object in $relatedObjects is not of $relatedClass.
     */
    public function setRelatedObjectSet( $sourceObject, array $relatedObjects, $setName, $replaceIdentities = false )
    {
        $srcClass = get_class( $sourceObject );
        $srcDef   = $this->definitionManager->fetchDefinition( $srcClass );
        $srcState = $sourceObject->getState();
        $srcId    = $srcState[$srcDef->idProperty->propertyName];

        // Sanity checks

        if ( !isset( $this->identities[$srcClass][$srcId] ) )
        {
            throw new ezcPersistentIdentityMissingException(
                $srcClass,
                $srcId
            );
        }

        $srcIdentity = $this->identities[$srcClass][$srcId];

        // Remove references before replacing a set
        if ( isset( $srcIdentity->namedRelatedObjectSets[$setName] ) )
        {
            $this->removeReferences( $srcIdentity->namedRelatedObjectSets[$setName] );
        }

        $relDefs  = array();
        $relStore = new ArrayObject();

        foreach ( $relatedObjects as $relObj )
        {
            $relClass = get_class( $relObj );
            if ( !isset( $relDefs[$relClass] ) )
            {
                $relDefs[$relClass] = $this->definitionManager->fetchDefinition( $relClass );
            }

            $relState = $relObj->getState();
            $relId    = $relState[$relDefs[$relClass]->idProperty->propertyName];

            // Check and replace identities
            if ( !isset( $this->identities[$relClass][$relId] ) )
            {
                $this->identities[$relClass][$relId] = new ezcPersistentIdentity(
                    $relObj
                );
            }
            else if ( $replaceIdentities )
            {
                // Replace identities on re-fetch
                $newIdentity = new ezcPersistentIdentity( $relObj );
                $this->replaceIdentityReferences(
                    $this->identities[$relClass][$relId],
                    $newIdentity
                );
                $this->identities[$relClass][$relId] = $newIdentity;
            }
            else
            {
                $relObj = $this->identities[$relClass][$relId]->object;
            }

            $relStore[$relId] = $relObj;

            // Store reference
            $this->identities[$relClass][$relId]->references->attach( $relStore );
        }
        
        $srcIdentity->namedRelatedObjectSets[$setName] = $relStore;

        return $relStore->getArrayCopy();
    }

    /**
     * Appends a new $relatedObject to a related object set of $sourceObject.
     *
     * Appends the given $relatedObject to the set of related objects for
     * $sourceObject with the class of $relatedObject and optionally
     * $relationName.
     *
     * In case that no set of related objects with the specific class has been
     * recorded for $object, yet, the call is ignored and related objects are
     * newly fetched whenever {@link getRelatedObjects()} is called.
     *
     * Note: All named related object sub-sets for $relatedObject are
     * automatically invalidated by a call to the method. The identity map can
     * not determine, to which named related object sub-set the $relatedObject
     * might be added.
     *
     * @param ezcPersistentObject $sourceObject 
     * @param ezcPersistentObject $relatedObject 
     * @param string $relationName
     *
     * @throws ezcPersistentRelationNotFoundException
     *         if no relation from $sourceObject to $relatedObject is defined.
     * @throws ezcPersistentIdentityMissingException
     *         if no identity has been recorded for $sourceObject or
     *         $relatedObject, yet.
     * @throws ezcPersistentIdentityRelatedObjectAlreadyExistsException
     *         if the given $relatedObject is already part of the set of
     *         related objects it should be added to.
     */
    public function addRelatedObject( $sourceObject, $relatedObject, $relationName = null )
    {
        $srcClass = get_class( $sourceObject );
        $relClass = get_class( $relatedObject );

        $srcDef   = $this->definitionManager->fetchDefinition( $srcClass );
        $relDef   = $this->definitionManager->fetchDefinition( $relClass );

        $srcState = $sourceObject->getState();
        $srcId    = $srcState[$srcDef->idProperty->propertyName];

        if ( !isset( $this->identities[$srcClass][$srcId] ) )
        {
            throw new ezcPersistentIdentityMissingException(
                $srcClass,
                $srcId
            );
        }

        $srcIdentity = $this->identities[$srcClass][$srcId];

        $relState = $relatedObject->getState();
        $relId    = $relState[$relDef->idProperty->propertyName];

        if ( !isset( $this->identities[$relClass][$relId] ) )
        {
            throw new ezcPersistentIdentityMissingException(
                $relClass,
                $relId
            );
        }

        $relStoreName = $this->createRelationStoreName(
            $relClass,
            $relationName
        );

        if ( !isset( $srcIdentity->relatedObjects[$relStoreName] ) )
        {
            // Ignore call, since related objects for $relClass have not been stored, yet
            return null;
        }

        $relStore = $srcIdentity->relatedObjects[$relStoreName];

        if ( isset( $relStore[$relId] ) )
        {
            throw new ezcPersistentIdentityRelatedObjectAlreadyExistsException(
                $srcClass, $srcId, $relClass, $relId, $relationName
            );
        }

        $relStore[$relId] = $relatedObject;

        // Store new reference
        $this->identities[$relClass][$relId]->references->attach(
            $relStore
        );
        
        // Invalidate all named sets, since they might be inconsistent now
        $this->removeAllReferences( 
            $srcIdentity->namedRelatedObjectSets
        );
        $srcIdentity->namedRelatedObjectSets = array();
    }

    /**
     * Removes a $relatedObject from the sets of related objects of $sourceObject.
     *
     * Removes the $relatedObject from all recorded sets of related objects
     * (named and unnamed) for $sourceObject. This method (in contrast to
     * {@link addRelatedObject()}) does not invalidate named related object
     * sets, but simply removes the $relatedObject from them.
     * 
     * @param ezcPersistentObject $sourceObject 
     * @param ezcPersistentObject $relatedObject 
     * @param string $relationName
     *
     * @throws ezcPersistentIdentityMissingException
     *         if no identity for $sourceObject has been recorded, yet.
     */
    public function removeRelatedObject( $sourceObject, $relatedObject, $relationName = null )
    {
        $srcClass = get_class( $sourceObject );
        $relClass = get_class( $relatedObject );

        $srcDef   = $this->definitionManager->fetchDefinition( $srcClass );
        $relDef   = $this->definitionManager->fetchDefinition( $relClass );

        $srcState = $sourceObject->getState();
        $srcId    = $srcState[$srcDef->idProperty->propertyName];

        $relState = $relatedObject->getState();
        $relId    = $relState[$relDef->idProperty->propertyName];

        if ( !isset( $this->identities[$srcClass][$srcId] ) )
        {
            throw new ezcPersistentIdentityMissingException(
                $srcClass,
                $srcId
            );
        }
        if ( !isset( $this->identities[$relClass][$relId] ) )
        {
            // Ignore call
            return null;
        }

        $relationStoreName = $this->createRelationStoreName(
            $relClass,
            $relationName
        );

        $srcIdentity = $this->identities[$srcClass][$srcId];
        $relIdentity = $this->identities[$relClass][$relId];

        if ( isset( $srcIdentity->relatedObjects[$relationStoreName] ) )
        {
            unset( $srcIdentity->relatedObjects[$relationStoreName][$relId] );
            $relIdentity->references->detach( $srcIdentity->relatedObjects[$relationStoreName] );
        }

        foreach ( $srcIdentity->namedRelatedObjectSets as $setName => $rels )
        {
            if ( isset( $rels[$relId] ) && $rels[$relId] instanceof $relClass )
            {
                unset( $srcIdentity->namedRelatedObjectSets[$setName][$relId] );
                $relIdentity->references->detach(
                    $srcIdentity->namedRelatedObjectSets[$setName]
                );
            }
        }
    }

    /**
     * Returns the set of related objects of $relatedClass for $sourceObject.
     *
     * Returns the set of related objects of $relatedClass for $sourceObject.
     * This might also be an empty set (empty array). In case no related
     * objects are recorded, yet, null is returned.
     * 
     * @param ezcPersistentObject $sourceObject 
     * @param string $relatedClass 
     * @param string $relationName
     *
     * @return array(object($relatedClass))|null
     *
     * @throws ezcPersistentRelationNotFoundException
     *         if not relation between the class of $sourceObject and
     *         $relatedClass (with optionally $relationName) is defined.
     */
    public function getRelatedObjects( $sourceObject, $relatedClass, $relationName = null )
    {
        $srcClass = get_class( $sourceObject );
        $srcDef   = $this->definitionManager->fetchDefinition( $srcClass );
        $srcState = $sourceObject->getState();
        $srcId    = $srcState[$srcDef->idProperty->propertyName];

        if ( !isset( $srcDef->relations[$relatedClass] ) )
        {
            throw new ezcPersistentRelationNotFoundException(
                $srcClass,
                $relatedClass,
                $relationName
            );
        }

        $relationStoreName = $this->createRelationStoreName(
            $relatedClass,
            $relationName
        );

        // Sanity checks

        if ( !isset( $this->identities[$srcClass][$srcId] ) )
        {
            // No object identity
            return null;
        }

        $srcIdentity = $this->identities[$srcClass][$srcId];

        if ( isset( $srcIdentity->relatedObjects[$relationStoreName] ) )
        {
            // Return a real array here, not the ArrayObject stored
            return $srcIdentity->relatedObjects[$relationStoreName]->getArrayCopy();
        }
        return null;
    }

    /**
     * Returns the named set of related objects for $sourceObject with $setName.
     *
     * Returns the named set of related objects for $sourceObject identified by
     * $setName. This might also be an empty set (empty array). In case no
     * related objects with this name are recorded, yet, null is returned.
     * 
     * @param ezcPersistentObject $sourceObject 
     * @param string $setName 
     * @return array(object($relatedClass))|null
     */
    public function getRelatedObjectSet( $sourceObject, $setName )
    {
        $srcClass = get_class( $sourceObject );
        $srcDef   = $this->definitionManager->fetchDefinition( $srcClass );
        $srcState = $sourceObject->getState();
        $srcId    = $srcState[$srcDef->idProperty->propertyName];

        if ( !isset( $this->identities[$srcClass][$srcId] ) )
        {
            return null;
        }
        $identity = $this->identities[$srcClass][$srcId];

        if ( isset( $identity->namedRelatedObjectSets[$setName] ) )
        {
            return $identity->namedRelatedObjectSets[$setName]->getArrayCopy();
        }
        return null;
    }

    /**
     * Resets the complete identity map.
     *
     * Removes all stored identities from the map and resets it into its
     * initial state.
     */
    public function reset()
    {
        $this->identities = array();
    }

    /**
     * Creates the related object set identifier for $relatedClass and $relationName.
     *
     * Determines the unique name for relations of $relatedClass with
     * $relationName (can be null).
     * 
     * @param string $relatedClass 
     * @param string $relationName 
     * @return string
     */
    protected function createRelationStoreName( $relatedClass, $relationName )
    {
        return $relatedClass
            . ( $relationName !== null ? "__{$relationName}" : '' );
    }

    /**
     * Removes all references to all $sets from all objects in $sets.
     *
     * Removes all references to all object $sets from all objects contained in
     * each of the $sets.
     * 
     * @param array(ArrayObject) $sets 
     * @see removeReferences()
     */
    protected function removeAllReferences( array $sets )
    {
        foreach ( $sets as $set )
        {
            $this->removeReferences( $set );
        }
    }

    /**
     * Removes all references to $set from the objects in $set.
     *
     * Maintains the {@link ezcPersistentIdentity::$references} attribute by
     * removing all refereneces to $set from all object identities contained in
     * $set.
     *
     * @param ArrayObject $set 
     */
    protected function removeReferences( ArrayObject $set )
    {
        foreach ( $set as $obj )
        {
            $class = get_class( $obj );
            $def   = $this->definitionManager->fetchDefinition( $class );
            $state = $obj->getState();
            $id    = $state[$def->idProperty->propertyName];
            
            if ( $this->identities[$class][$id]->references->contains( $set ) )
            {
                $this->identities[$class][$id]->references->detach( $set );
            }
        }
    }

    /**
     * Replaces all references to $oldIdentity with a reference to $newIdentity.
     *
     * Scans all sets refered in {@link ezcPersistentIdentity::$references} of
     * the $oldIdentity and replaces the references to $oldIdentity with
     * $newIdentity in them. This mechanism is used whenever an identity is to
     * be replaced by a new one.
     * 
     * @param ezcPersistentIdentity $oldIdentity 
     * @param ezcPersistentIdentity $newIdentity 
     */
    protected function replaceIdentityReferences( ezcPersistentIdentity $oldIdentity, ezcPersistentIdentity $newIdentity )
    {
        foreach( $oldIdentity->references as $refList )
        {
            $replaceIds = array();
            // Needs iteration here, to determine key
            foreach ( $refList->getIterator() as $refId => $refItem )
            {
                if ( $refItem === $oldIdentity->object )
                {
                    $replaceIds[] = $refId;
                }
            }
            foreach ( $replaceIds as $replaceId )
            {
                // Replace object in related sets
                $refList[$replaceId] = $newIdentity->object;
            }
        }
    }

    /**
     * Removes all references to $identity.
     *
     * Scans all sets referred in {@link ezcPersistentIdentity::$references}
     * and removes $identity from them. This is used, if the $identity is to be
     * {@link removeIdenity()}, to ensure it does not occur in any related set
     * or named related set anymore.
     * 
     * @param ezcPersistentIdentity $identity 
     */
    protected function removeIdentityReferences( ezcPersistentIdentity $identity )
    {
        foreach( $identity->references as $refList )
        {
            $removeIds = array();
            // Needs iteration here, to determine key
            foreach ( $refList->getIterator() as $refId => $refItem )
            {
                if ( $refItem === $identity->object )
                {
                    $removeIds[] = $refId;
                }
            }
            foreach ( $removeIds as $removeId )
            {
                // Remove object from related set
                unset( $refList[$removeId] );
            }
        }
    }
}

?>
