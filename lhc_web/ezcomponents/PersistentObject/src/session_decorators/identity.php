<?php
/**
 * File containing the ezcPersistentSessionIdentityDecorator class.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This class decorates ezcPersistentSession with facilities of the identity map pattern.
 *
 * An instance of this class is used to decorate an {@link
 * ezcPersistentSession} with the facilities of the identity map pattern
 * (similar to {@see http://martinfowler.com/eaaCatalog/identityMap.html}).
 *
 * The identity map pattern avoids inconsistencies in your application, by
 * avoiding that the same database object exists in multiple different object
 * instances. If your request the same object a second time, the already
 * existing instance is returned instead of creating a new one. This can also
 * save you some SQL queries and therefore database load, but this is not the
 * primary target of the pattern.
 *
 * In addition to the identity map pattern, this class caches sets of related
 * objects (see {@link getRelatedObjects()}) and allows you to pre-fetch nested
 * related objects, using SQL joins ({@link loadWithRelatedObjects()}, {@link
 * createFindQueryWithRelations()}). This can reduce database load
 * significantly.
 *
 * An instance of this class can replace an {@link ezcPersistentSession}
 * transparently, since it fulfills the same interface. To use it, you need the
 * original session, an instance of {@link ezcPersistentIdentityMap} and
 * potentially {@link ezcPersistentSessionIdentityDecoratorOptions}. The
 * creation of the decorated session works as follows:
 *
 * <code>
 * <?php
 *     // $originalSession contains a valid instance of ezcPersistentSession
 *     $identityMap = new ezcPersistentBasicIdentityMap(
 *         $originalSession->definitionManager
 *     );
 *     $identitySession = new ezcPersistentSessionIdentityDecorator(
 *         $originalSession,
 *         $identityMap
 *     );
 * ?>
 * </code>
 *
 * You can now transparently replace $originalSession and $identitySession in
 * most cases. Only the methods {@link updateFromQuery()} and {@link
 * deleteFromQuery()} won't work properly, since the identity map cannot trace
 * the changes produced by this method in the database. Attention: Calling
 * these methods will result in a complete reset of the identity map!
 *
 * Using the $options property, you can temporarely activate refetching of
 * objects. Be careful with this option! While this is set to true, all object
 * identities will be created from scratch and existing ones will be replaced.
 * This is usually not desired! Use {@link refresh()} to update object values
 * from database instead.
 *
 * @property-read ezcPersistentIdentityMap $identityMap
 *                Identity map used by this session. You should usually not
 *                access this property directly.
 * @property ezcPersistentSessionIdentityDecoratorOptions $options
 *                Options to influence the behaviour of the session.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @mainclass
 */
class ezcPersistentSessionIdentityDecorator implements ezcPersistentSessionFoundation
{
    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    private $properties = array();

    /**
     * The persistent session this object wraps.
     * 
     * @var ezcPersistentSession
     */
    protected $session;

    /**
     * Query creator for relation pre-fetching. 
     * 
     * @var ezcPersistentIdentityRelationQueryCreator
     */
    private $queryCreator;

    /**
     * Related object extractor used for pre-fetching. 
     * 
     * @var ezcPersistentIdentityRelationObjectExtractor
     */
    private $objectExtractor;

    /**
     * Creates a new identity map decorator.
     *
     * This identity map decorator wraps around $session and makes use of this
     * to issue the actual database operations. Object identities are stored in
     * the $identityMap. The $options influence the behavior of the identity
     * session, like setting the $refetch option to force reloading of objects.
     * 
     * @param ezcPersistentSession $session 
     * @param ezcPersistentIdentityMap $identityMap 
     * @param ezcPersistentSessionIdentityDecoratorOptions $options
     */
    public function __construct( ezcPersistentSession $session, ezcPersistentIdentityMap $identityMap, ezcPersistentSessionIdentityDecoratorOptions $options = null )
    {
        $this->session                   = $session;
        $this->properties['identityMap'] = $identityMap;
        $this->properties['options']     = (
            $options === null ? new ezcPersistentSessionIdentityDecoratorOptions() : $options
        );
    }

    /**
     * Returns the persistent object of class $class with id $id.
     *
     * Checks if the object of $class with $id has already been loaded. If this
     * is the case, the existing identity is returned. Otherwise the desired
     * object is loaded from the database and its identity is recorded for
     * later uses.
     *
     * @throws ezcPersistentObjectException
     *         if the object is not available.
     * @throws ezcPersistentObjectException
     *         if there is no such persistent $class.
     *
     * @param string $class
     * @param mixed $id
     *
     * @return ezcPersistentObject
     */
    public function load( $class, $id )
    {
        $idMap = $this->properties['identityMap'];

        if ( !$this->properties['options']->refetch )
        {
            $identity = $idMap->getIdentity( $class, $id );

            if ( $identity !== null )
            {
                return $identity;
            }
        }

        $identity = $this->session->load( $class, $id );
        $idMap->setIdentity( $identity );
        
        return $identity;
    }

    /**
     * Returns the persistent object of class $class with id $id or null.
     *
     * This method is equivalent to {@link load()} except that it returns null
     * instead of throwing an exception, if the desired object does not exist.
     * A null value will not be recorded in the identity map, so a second
     * attempt to load the object of $class with $id will result in another
     * database query.
     *
     * @param string $class
     * @param int $id
     *
     * @return ezcPersistentObject|null
     */
    public function loadIfExists( $class, $id )
    {
        $idMap = $this->properties['identityMap'];

        if ( !$this->properties['options']->refetch )
        {
            $identity = $idMap->getIdentity( $class, $id );

            if ( $identity !== null )
            {
                return $identity;
            }
        }

        $identity = $this->session->loadIfExists( $class, $id );

        if ( $identity !== null )
        {
            $idMap->setIdentity( $identity );
        }
        
        return $identity;
    }

    /**
     * Loads the persistent object of $class with $id into the given $object.
     *
     * The class of the persistent object to load is determined from $object.
     * In case an identity for the given $id has already been recorded in the
     * identity map and $object is not the same instance, an exception is
     * thrown.
     *
     * @throws ezcPersistentObjectException
     *         if the object is not available.
     * @throws ezcPersistentDefinitionNotFoundException
     *         if $object is not of a valid persistent object type.
     * @throws ezcPersistentQueryException
     *         if the find query failed.
     * @throws ezcPersistentIdentityAlreadyExistsException
     *         if a different identity of the class of $object with $id already
     *         exists.
     *
     * @param ezcPersistentObject $object
     * @param mixed $id
     */
    public function loadIntoObject( $object, $id )
    {
        $idMap = $this->properties['identityMap'];
        
        $class = get_class( $object );

        if ( !$this->properties['options']->refetch )
        {
            $identity = $idMap->getIdentity( $class, $id );

            if ( $identity !== null )
            {
                throw new ezcPersistentIdentityAlreadyExistsException(
                    $class,
                    $id
                );
            }
        }

        $this->session->loadIntoObject( $object, $id );

        $idMap->setIdentity( $object );
    }

    /**
     * Syncronizes the contents of $object with the database.
     *
     * Note that calling this method is equavalent with calling {@link
     * loadIntoObject()} on $object with the ID of $object. Any changes made
     * to $object prior to calling refresh() will be discarded.
     *
     * The refreshing of an object will result in its identity being refreshed
     * automatically.
     *
     * @throws ezcPersistentObjectException
     *         if $object is not of a valid persistent object type.
     * @throws ezcPersistentObjectException
     *         if $object is not persistent already.
     * @throws ezcPersistentObjectException
     *         if the select query failed.
     *
     * @param ezcPersistentObject $object
     */
    public function refresh( $object )
    {
        $this->session->refresh( $object );
    }

    /**
     * Returns the result of the $query as an array of objects.
     *
     * Returns the persistent objects found for $class using the submitted
     * $query. $query should be created using {@link createFindQuery()} to
     * ensure correct alias mappings and can be manipulated as needed. The
     * $class parameter is optional, since {@link ezcPersistentFindQuery} now
     * stores this information on creation using {@link createFindQuery()}.
     *
     * The array returned by this method is indexed by the IDs of the contained
     * objects. The order of the array reflects the order in the database or as
     * indicated by the ORDER BY clause of the query.
     *
     * The results fetched will be checked for identities that have already
     * been recorded before. If an existing identity is found for an object,
     * this identity will be used in the result set. Note: This does not
     * prevent the database query at all, but just ensures consistency.
     *
     * Example:
     * <code>
     * <?php
     *
     * $q = $session->createFindQuery( 'Person' );
     * $allPersons = $session->find( $q );
     *
     * ?>
     * </code>
     *
     * If you are retrieving large result set, consider using {@link
     * findIterator()} instead.
     *
     * Example:
     * <code>
     * <?php
     *
     * $q = $session->createFindQuery( 'Person' );
     * $objects = $session->findIterator( $q, 'Person' );
     *
     * foreach( $objects as $object )
     * {
     *     // ...
     * }
     *
     * ?>
     * </code>
     *
     * Identity mapping comes into action in the following example:
     * <code>
     * <?php
     * 
     * $person = $session->load( 'Person', 23 );
     *
     * $q = $session->createFindQuery( 'Person' );
     * $allPersons = $session->find( $q );
     *
     * ?>
     * </code>
     * In $allPersons, the object with ID 23 will not be a new instance, but
     * the existing instance, that was already fetched by the call to {@link
     * load()}.
     *
     * @throws ezcPersistentDefinitionNotFoundException
     *         if there is no such persistent class.
     * @throws ezcPersistentQueryException
     *         if the find query failed.
     * @throws ezcBaseValueException
     *         if $query parameter is not an instance of ezcPersistentFindQuery
     *         or ezcQuerySelect. Or if $class is missing if you use
     *         ezcQuerySelect.
     *
     * @param ezcPersistentFindQuery|ezcQuerySelect $query
     * @param string $class
     *
     * @return array(object($class))
     * @apichange This method will only accept an instance of
     *            ezcPersistentFindQuery as the $query parameter in future
     *            major releases. The $class parameter will be removed.
     * @apichange Since version 1.6, the returned array is indexed by the
     *            object IDs and not by ascending integers anymore.
     */
    public function find( $query, $class = null )
    {
        if ( $query instanceof ezcPersistentFindWithRelationsQuery )
        {
            return $this->findWithRelations( $query );
        }
        else
        {
            return $this->findDefault( $query, $class );
        }
    }

    /**
     * Performs a find with related objects.
     *
     * Performs the find operation and also registeres the related objects in
     * the {@link ezcPersistentIdentityMap} as defined by the query.
     * 
     * @param ezcPersistentFindWithRelationsQuery $query 
     * @param string $class 
     * @return array(ezcPersistentObject)
     */
    private function findWithRelations( ezcPersistentFindWithRelationsQuery $query )
    {
        $this->initializeObjectExtractor();

        $stmt = $query->prepare();
        $stmt->execute();

        return $this->objectExtractor->extractObjectsWithRelatedObjects(
            $stmt,
            $query
        );
    }

    /**
     * Performs the default find behaviour.
     *
     * Warps around {@link ezcPersistentSession::find()} and registeres found
     * objects with the {@link ezcPersistentIdentityMap}.
     * 
     * @param ezcQuerySelect|ezcFindQuery $query 
     * @param string $class 
     * @return array(object($class))
     */
    private function findDefault( $query, $class )
    {
        $isRelFindQueryWithSetName = $query instanceof ezcPersistentRelationFindQuery
            && $query->relationSetName !== null;
        if ( !$this->options->refetch && $isRelFindQueryWithSetName )
        {
            // Check if such a subset already exisist
            $objects = $this->identityMap->getRelatedObjectSet(
                $query->relationSource,
                $query->relationSetName
            );
            if ( $objects !== null )
            {
                return $objects;
            }
        }

        $objects = $this->performFind( $query, $class );

        // Query for createRelationFindQuery() with sub-set name assigned
        // No refetch check needed anymore
        if ( $isRelFindQueryWithSetName )
        {
            $objects = $this->identityMap->setRelatedObjectSet(
                $query->relationSource,
                $objects,
                $query->relationSetName
            );
        }

        return $objects;
    }

    /**
     * Performs the actual find.
     * 
     * @param ezcQuerySelect|ezcPersistentFindQuery $query 
     * @param string $class 
     * @return array(object($class))
     */
    private function performFind( $query, $class = null )
    {
        $objects = $this->session->find( $query, $class );

        $defs = array();

        foreach ( $objects as $i => $object )
        {
            $class = get_class( $object );

            if ( !isset( $defs[$class] ) )
            {
                $defs[$class] = $this->session->definitionManager->fetchDefinition(
                    $class
                );
            }

            $state = $object->getState();
            $id    = $state[$defs[$class]->idProperty->propertyName];
            
            $identity = null;

            if ( !$this->properties['options']->refetch )
            {
                $identity = $this->properties['identityMap']->getIdentity(
                    $class,
                    $id
                );
            }

            if ( $identity !== null )
            {
                $objects[$i] = $identity;
            }
            else
            {
                $this->properties['identityMap']->setIdentity(
                    $object
                );
            }
        }

        return $objects;
    }

    /**
     * Returns the result of $query for the $class as an iterator.
     *
     * This method is similar to {@link find()} but returns an {@link
     * ezcPersistentIdentityFindIterator} instead of an array of objects. This
     * is useful if you are going to loop over the objects and just need them
     * one at the time. Because you only instantiate one object it is faster
     * than {@link find()}. In addition, only 1 record is retrieved from the
     * database in each iteration, which may reduce the data transfered between
     * the database and PHP, if you iterate only through a small subset of the
     * affected records.
     *
     * Note that if you do not loop over the complete result set you must call
     * {@link ezcPersistentFindIterator::flush()} before issuing another query.
     *
     * The find interator will automatically look up result objects in the
     * identity map and return existing identities, if they have already been
     * recorded.
     *
     * @throws ezcPersistentDefinitionNotFoundException
     *         if there is no such persistent class.
     * @throws ezcPersistentQueryException
     *         if the find query failed.
     * @throws ezcBaseValueException
     *         if $query parameter is not an instance of ezcPersistentFindQuery
     *         or ezcQuerySelect. Or if $class is missing if you use
     *         ezcQuerySelect.
     *
     * @param ezcPersistentFindQuery|ezcQuerySelect $query
     * @param string $class
     *
     * @return ezcPersistentIdentityFindIterator
     * @apichange This method will only accept an instance of
     *            ezcPersistentFindQuery as the $query parameter in future
     *            major releases. The $class parameter will be removed.
     */
    public function findIterator( $query, $class = null )
    {
        // Sanity checks
        if ( !is_object( $query )
             || ( !( $query instanceof ezcPersistentFindQuery )
                  && !( $query instanceof ezcQuerySelect )
                )
           )
        {
            throw new ezcBaseValueException(
                'query',
                $query,
                'ezcPersistentFindQuery (or ezcQuerySelect)'
            );
        }
        if ( $query instanceof ezcQuerySelect && $class === null )
        {
            throw new ezcBaseValueException(
                'class',
                $class,
                'string (mandatory, if ezcQuerySelect is used)'
            );
        }

        // Extract class name and select query form parameter
        if ( $query instanceof ezcPersistentFindQuery )
        {
            $class = $query->className;
            $query = $query->query;
        }

        $def  = $this->definitionManager->fetchDefinition( $class );
        $stmt = $this->session->performQuery( $query );
        return new ezcPersistentIdentityFindIterator(
            $stmt,
            $def,
            $this->identityMap,
            $this->properties['options']
        );
    }

    /**
     * Returns the related objects of a given $relatedClass for $object.
     *
     * This method returns the related objects of type $relatedClass for the
     * given $object. This method (in contrast to {@link getRelatedObject()})
     * always returns an array of found objects, no matter if only 1 object
     * was found (e.g. {@link ezcPersistentManyToOneRelation}), none or several
     * ({@link ezcPersistentManyToManyRelation}).
     *
     * In case the set of related objects has already been fetched earlier, the
     * request to the database is not repeated, but the recorded object set is
     * returned. If the set of related objects was not recorded, yet, it is
     * fetched from the database and recorded afterwards.
     *
     * Example:
     * <code>
     * $person = $session->load( "Person", 1 );
     * $relatedAddresses = $session->getRelatedObjects( $person, "Address" );
     * echo "Number of addresses found: " . count( $relatedAddresses );
     * </code>
     *
     * Relations that should preferably be used with this method are:
     * <ul>
     * <li>{@link ezcPersistentOneToManyRelation}</li>
     * <li>{@link ezcPersistentManyToManyRelation}</li>
     * </ul>
     * For other relation types {@link getRelatedObject()} is recommended.
     *
     * If multiple relations are defined for the $relatedClass (using {@link
     * ezcPersistentRelationCollection}), the parameter $relationName becomes
     * mandatory to determine which relation definition to use. For normal
     * relations, this parameter is silently ignored.
     *
     * @param object $object
     * @param string $relatedClass
     * @param string $relationName
     *
     * @return array(int=>object($relatedClass))
     *
     * @throws ezcPersistentRelationNotFoundException
     *         if the given $object does not have a relation to $relatedClass.
     */
    public function getRelatedObjects( $object, $relatedClass, $relationName = null )
    {
        if ( !$this->properties['options']->refetch )
        {
            $relatedObjs = $this->identityMap->getRelatedObjects(
                $object,
                $relatedClass,
                $relationName
            );
            if ( $relatedObjs !== null )
            {
                return $relatedObjs;
            }
        }

        $relatedObjs = $this->session->getRelatedObjects(
            $object,
            $relatedClass,
            $relationName
        );

        $storedRelatedObjs = $this->identityMap->setRelatedObjects(
            $object,
            $relatedObjs,
            $relatedClass,
            $relationName,
            $this->properties['options']->refetch
        );

        return $storedRelatedObjs;
    }

    /**
     * Returns the related object of a given $relatedClass for $object.
     *
     * This method returns the related object of type $relatedClass for the
     * object $object. This method (in contrast to {@link getRelatedObjects()})
     * always returns a single result object, no matter if more related objects
     * could be found (e.g. {@link ezcPersistentOneToManyRelation}). If no
     * related object is found, an exception is thrown, while {@link
     * getRelatedObjects()} just returns an empty array in this case.
     *
     * In case the related object has already been fetched earlier, the request
     * to the database is not repeated, but the recorded object is returned. If
     * the related object was not recorded, yet, it is fetched from the
     * database and recorded afterwards.
     *
     * Example:
     * <code>
     * $person = $session->load( "Person", 1 );
     * $relatedAddress = $session->getRelatedObject( $person, "Address" );
     * echo "Address of this person: " . $relatedAddress->__toString();
     * </code>
     *
     * Relations that should preferably be used with this method are:
     * <ul>
     * <li>{@link ezcPersistentManyToOneRelation}</li>
     * <li>{@link ezcPersistentOneToOneRelation}</li>
     * </ul>
     * For other relation types {@link getRelatedObjects()} is recommended.
     *
     * If multiple relations are defined for the $relatedClass (using {@link
     * ezcPersistentRelationCollection}), the parameter $relationName becomes
     * mandatory to determine which relation definition to use. For normal
     * relations, this parameter is silently ignored.
     *
     * @param object $object
     * @param string $relatedClass
     * @param string $relationName
     *
     * @return object($relatedClass)
     *
     * @throws ezcPersistentRelationNotFoundException
     *         if the given $object does not have a relation to $relatedClass.
     */
    public function getRelatedObject( $object, $relatedClass, $relationName = null )
    {
        $relObjs = $this->getRelatedObjects(
            $object,
            $relatedClass,
            $relationName
        );
        return reset( $relObjs );
    }

    /**
     * Returns the named related object subset with $setName for $object.
     *
     * This method is used to retrieve named subsets of related objects created
     * by using {@link find()} with a restricted {@link
     * ezcPersistentFindWithRelationsQuery} created by {@link
     * createFindQueryWithRelations()}.
     *
     * @see ezcPersistentFindWithRelationsQuery
     * @see find()
     * @see createFindQueryWithRelations()
     * 
     * @param ezcPersistentObject $object 
     * @param string $setName 
     * @return array(ezcPersistentObject)|null
     *
     * @apichange This method does not require ezcPersistentObject as a type
     *            hint for BC reasons. In the next major version, this type
     *            hint will be added.
     */
    public function getRelatedObjectSubset( $object, $setName )
    {
        return $this->identityMap->getRelatedObjectSet(
            $object,
            $setName
        );
    }

    /**
     * Returns a select query for the given persistent object $class.
     *
     * The query is initialized to fetch all columns from the correct table and
     * has correct alias mappings between columns and property names of the
     * persistent $class. The alias mapping allows you to use property names in
     * WHERE conditions, instead of column names. These aliases will
     * automatically be resolved before querying the database.
     *
     * Example:
     * <code>
     * <?php
     *
     * $q = $session->createFindQuery( 'Person' );
     * $allPersons = $session->find( $q, 'Person' );
     *
     * ?>
     * </code>
     *
     * Example with aliases:
     * <code>
     * <?php
     * $q = $session->createFindQuery( 'Person' );
     * $q->where(
     *     $q->expr->eq(
     *         'zipCode',
     *         $q->bindValue( 12345 )
     *     )
     * );
     * $somePersons = $session->find( $q, 'Person' );
     * 
     * ?>
     * </code>
     * $zipCode is the property name in the Person class, while the
     * corresponding database column is named zip_code.
     *
     * @throws ezcPersistentObjectException
     *         if there is no such persistent class.
     *
     * @param string $class
     *
     * @return ezcPersistentFindQuery
     */
    public function createFindQuery( $class )
    {
        return $this->session->createFindQuery( $class );
    }

    /**
     * Returns a sub-select for the given $class to be used with $parentQuery.
     *
     * This method creates an {@link ezcPersistentFindQuery} as a {@link 
     * ezcQuerySubSelect} for the given $class. The returned query has already
     * set aliases for the properties of $class, but (in contrast to the query
     * returned by {@link createFindQuery()}) does not have the selection of all
     * properties set. You need to do
     *
     * <code>
     * <?php
     * $subSelect = $session->subSelect( $existingSelectQuery, 'MyClass' );
     * $subSelect->select( 'myField' );
     * ?>
     * </code>
     *
     * manually to select the fields you desire.
     * 
     * @param ezcPersistentFindQuery $parentQuery 
     * @param string $class 
     * @return ezcQuerySubSelect
     */
    public function createSubQuery( ezcPersistentFindQuery $parentQuery, $class )
    {
        return $this->loadHandler->createSubQuery( $parentQuery, $class );
    }

    /**
     * Returns a select query for the given $class and its related objects as
     * defined in $relations.
     *
     * This method creates an instance of {@link
     * ezcPersistentFindWithRelationsQuery}, which can basically be used like
     * {@link ezcPersistentFindQuery} aka {@link ezcQuerySelect}. The query
     * object is configured to load objects of $class and has JOIN statements
     * to load related objects as defined in $relations, in addition. You can
     * use the {@link find()} method to perform the actual find operation. This
     * one will return the objects of $class. The related objects can simply be
     * obtained using {@link getRelatedObjects()}, {@link getRelatedObject()}
     * or {@link getRelatedObjectSet()} (see below). Calls to these methods the
     * work without issuing a new database queries, since the desired objects
     * are already stored in the {@link ezcPersistentIdentityMap}.
     *
     * The $relations array has the following structure:
     * <code>
     * <?php
     *  array(
     *      'relationAlias_1' => new ezcPersistentRelationFindDefinition(
     *          'relatedClass_1',
     *          null,
     *          array(
     *              'deeperAlias_1' => new ezcPersistentRelationFindDefinition(
     *                  'deeperRelatedClass_1'
     *              )
     *          )
     *      ),
     *      'relationAlias_2' => new ezcPersistentRelationFindDefinition(
     *          'relatedClass_2'
     *      )
     *  );
     * ?>
     * </code>
     *
     * The keys of the array define aliases for relations to be used in the
     * local context. Each key has an object of {@link
     * ezcPersistentRelationFindDefinition} assigned, that defines which
     * relation is meant to be fetched. The first entry above assignes the
     * alias 'relationAlias_1' to the related class 'relatedClass_1'. The
     * second parameter to the constructor of {@linke
     * ezcPersistentRelationFindDefinition} can be a relation name, if multiple
     * relations to the same class exist. The third parameter defines deeper
     * relations.
     *
     * A call to this method with $class set to 'myClass' and $relations
     * defined as seen above creates a find query that by default finds:
     *
     * - All objects of myClass
     * - Foreach object of myClass, all related objects of relatedClass_1
     * - Foreach object of myClass, all related objects of relatedClass_2
     * - Foreach object of relatedClass_1, all related objects of deeperRelatedClass_1
     *
     * The aliases defined as the keys of the $relations array can be used to
     * add where() conditions to the created query. Properties of the objects
     * of relatedClass_1 can be accessed by prefixing their name with
     * 'relationAlias_1_' (for example 'relationAlias_1_id' to access the 'id'
     * property).
     *
     * NOTE: If you restrict the objects to be found by a WHERE condition, not
     * the full set of related objects might be returned. To avoid
     * inconsistencies in the identity map, the extracted sets of related
     * objects will then not be registered as usual, but as <b>named related
     * sets</b>. You can retrieve these using the {@link getRelatedObjectSet()}
     * method (instead of using {@link getRelatedObjects()}), with the chosen
     * relation alias as the set name.
     *
     * @see find()
     * @see ezcPersistentFindWithRelationsQuery
     * @see ezcPersistentRelationFindDefinition
     * @see ezcPersistentIdentityMap
     * 
     * @param string $class 
     * @param array(ezcPersistentRelationFindDefinition) $relations 
     * @return ezcPersistentFindWithRelationsQuery
     */
    public function createFindQueryWithRelations( $class, array $relations )
    {
        $this->initializeQueryCreator();
        return $this->queryCreator->createFindQuery( $class, $relations );
    }

    /**
     * Returns the base query for retrieving related objects.
     *
     * See {@link getRelatedObject()} and {@link getRelatedObjects()}. Can be
     * modified by additional where conditions and simply be used with
     * {@link find()} and the related class name, to retrieve a sub-set of
     * related objects.
     *
     * If multiple relations are defined for the $relatedClass (using {@link
     * ezcPersistentRelationCollection}), the parameter $relationName becomes
     * mandatory to determine which relation definition to use. For normal
     * relations, this parameter is silently ignored.
     *
     * If you provide a $setName, the resulting set of related objects fetched
     * by {@link find()} is cached under the given name for $object. You can
     * retrieve this set either through {@link getRelatedObjectSubset()} or by
     * issueing the same query (or a query with the same $object and $setName)
     * again. Overwriting a once created named set can be enfored using the
     * 'refetch' option in {@link ezcPersistentSessionIdentityDecoratorOptions}.
     *
     * @param ezcPersistentObject $object
     * @param string $relatedClass
     * @param string $relationName
     * @param string $setName
     *
     * @return ezcPersistentRelationFindQuery
     *
     * @throws ezcPersistentRelationNotFoundException
     *         if the given $object does not have a relation to $relatedClass.
     */
    public function createRelationFindQuery( $object, $relatedClass, $relationName = null, $setName = null )
    {
        $originalQuery = $this->session->createRelationFindQuery( $object, $relatedClass, $relationName );

        $q = new ezcPersistentRelationFindQuery(
            $originalQuery->query,
            $originalQuery->className
        );
        
        if ( $setName !== null )
        {
            $q->relationSetName = $setName;
            $q->relationSource  = $object;
        }

        return $q;
    }

    /**
     * Saves the new persistent object $object to the database using an INSERT INTO query.
     *
     * The correct ID is set to $object, if not using the {@link
     * ezcPersistentManualGenerator} (then you need to define the ID yourself).
     *
     * Newly saved objects are stored in the identity map.
     *
     * @throws ezcPersistentObjectException if $object
     *         is not of a valid persistent object type.
     * @throws ezcPersistentObjectException if $object
     *         is already stored to the database.
     * @throws ezcPersistentObjectException
     *         if it was not possible to generate a unique identifier for the
     *         new object.
     * @throws ezcPersistentObjectException
     *         if the insert query failed.
     *
     * @param ezcPersistentObject $object
     */
    public function save( $object )
    {
        $class = get_class( $object );
        $def   = $this->definitionManager->fetchDefinition( $class );
        $state = $object->getState();
        
        // Sanity checks
        if ( !$this->properties['options']->refetch && isset( $state[$def->idProperty->propertyName] ) )
        {
            $id       = $state[$def->idProperty->propertyName];
            $identity = $this->identityMap->getIdentity( $class, $id );

            if ( $identity !== null )
            {
                if ( $identity === $object )
                {
                    throw new ezcPersistentObjectAlreadyPersistentException( $class );
                }
                throw new ezcPersistentIdentityAlreadyExistsException( $class, $id );
            }
        }

        $this->session->save( $object );

        $this->identityMap->setIdentity( $object );
    }

    /**
     * Updates $object in the database using an UPDATE query.
     *
     * Stores the changes made to $object into the database. Updates are
     * automatically reflected in the identity map.
     *
     * @throws ezcPersistentDefinitionNotFoundException
     *         if $object is not of a valid persistent object type.
     * @throws ezcPersistentObjectNotPersistentException
     *         if $object is not stored in the database already.
     * @throws ezcPersistentQueryException
     *
     * @param ezcPersistentObject $object
     */
    public function update( $object )
    {
        // The object already must have been fetched before here, so an
        // identity is already recorded.
        $this->session->update( $object );
    }

    /**
     * Saves or updates the persistent $object to the database.
     *
     * If the object is a new object an INSERT INTO query will be executed. If
     * the object is persistent already it will be updated with an UPDATE
     * query.
     *
     * Newly saved objects are automatically recorded in the identity map.
     * Updates to existing objects are reflected automatically, too.
     *
     * @throws ezcPersistentDefinitionNotFoundException
     *         if the definition of the persistent object could not be loaded.
     * @throws ezcPersistentObjectException
     *         if $object is not of a valid persistent object type.
     * @throws ezcPersistentObjectException
     *         if any of the definition requirements are not met.
     * @throws ezcPersistentObjectException
     *         if the insert or update query failed.
     *
     * @param ezcPersistentObject $object
     */
    public function saveOrUpdate( $object )
    {
        $this->session->saveOrUpdate( $object );

        $class = get_class( $object );
        $def   = $this->definitionManager->fetchDefinition( $class );
        $state = $object->getState();
        $id    = $state[$def->idProperty->propertyName];

        if ( $this->properties['options']->refetch || $this->identityMap->getIdentity( $class, $id ) === null )
        {
            $this->identityMap->setIdentity( $object );
        }
    }

    /**
     * Create a relation between $object and $relatedObject.
     *
     * This method is used to create a relation between the given source
     * $object and the desired $relatedObject. The related object is not stored
     * in the database automatically, only the desired properties are set. An
     * exception is {@ezcPersistentManyToManyRelation}s, where the relation
     * record is stored automatically and there is no need to store
     * $relatedObject explicitly after establishing the relation.
     *
     * If there are multiple relations defined between the class of $object and
     * $relatedObject (via {@link ezcPersistentRelationCollection}), the
     * $relationName parameter becomes mandatory to determine, which exact
     * relation should be used.
     *
     * Newly added related objects are stored in the identity map and added to
     * recorded relation sets. If not set of related object set is recorded,
     * yet, the adding is ignored.
     *
     * Note: All named related object sets (see {@link
     * ezcPersistentFindWithRelationsQuery}) for $object are invalidated and
     * removed from the identity map, to avoid inconsistencies.
     *
     * @param ezcPersistentObject $object
     * @param ezcPersistentObject $relatedObject
     * @param string $relationName
     *
     * @throws ezcPersistentRelationOperationNotSupportedException
     *         if a relation to create is marked as "reverse" {@link
     *         ezcPersistentRelation->reverse}.
     * @throws ezcPersistentRelationNotFoundException
     *         if the deisred relation is not defined.
     */
    public function addRelatedObject( $object, $relatedObject, $relationName = null )
    {
        $this->session->addRelatedObject( $object, $relatedObject, $relationName );
        $this->identityMap->addRelatedObject( $object, $relatedObject );
    }


    /**
     * Returns an update query for the given persistent object $class.
     *
     * The query is initialized to update the correct table and
     * it is only neccessary to set the correct values.
     *
     * Attention: If you use a query generated by this method to update
     * objects, the internal {@link ezcPersistentIdentityMap} will be completly
     * reset. This is neccessary to avoid inconsistencies, because the session
     * cannot trace which objects are updated by the query.
     *
     * @throws ezcPersistentDefinitionNotFoundException
     *         if there is no such persistent class.
     *
     * @param string $class
     *
     * @return ezcQueryUpdate
     */
    public function createUpdateQuery( $class )
    {
        return $this->session->createUpdateQuery( $class );
    }

    /**
     * Updates persistent objects using the query $query.
     *
     * The $query should be created using createUpdateQuery().
     *
     * Currently this method only executes the provided query. Future
     * releases PersistentSession may introduce caching of persistent objects.
     * When caching is introduced it will be required to use this method to run
     * cusom delete queries. To avoid being incompatible with future releases it is
     * advisable to always use this method when running custom delete queries on
     * persistent objects.
     *
     * Attention: Every call to this method will cause the internal {@link
     * ezcPersistentIdentityMap} to be completly reset. This is neccessary to
     * avoid inconsistencies, because the session cannot trace which objects
     * are updated by the query.
     *
     * @throws ezcPersistentQueryException
     *         if the update query failed.
     *
     * @param ezcQueryUpdate $query
     */
    public function updateFromQuery( ezcQueryUpdate $query )
    {
        $this->identityMap->reset();
        return $this->session->updateFromQuery( $query );
    }

    /**
     * Deletes the persistent $object.
     *
     * This method will perform a DELETE query based on the identifier of the
     * persistent $object. After delete() the ID property of $object will be
     * reset to null. It is possible to {@link save()} $object afterwards.
     * $object will then be stored with a new ID.
     *
     * If you defined relations for the given object, these will be checked to
     * be defined as cascading. If cascading is configured, the related objects
     * with this relation will be deleted, too.
     *
     * The object will also be removed from the identity map and all related
     * object sets in it.
     *
     * Relations that support cascading are:
     * <ul>
     * <li>{@link ezcPersistenOneToManyRelation}</li>
     * <li>{@link ezcPersistenOneToOne}</li>
     * </ul>
     *
     * @throws ezcPersistentDefinitionNotFoundxception
     *         if $the object is not recognized as a persistent object.
     * @throws ezcPersistentObjectNotPersistentException
     *         if the object is not persistent already.
     * @throws ezcPersistentQueryException
     *         if the object could not be deleted.
     *
     * @param ezcPersistentObject $object The persistent object to delete.
     */
    public function delete( $object )
    {
        $this->session->delete( $object );

        $class = get_class( $object );
        $def = $this->session->definitionManager->fetchDefinition( $class );
        $state = $object->getState();
        $id = $state[$def->idProperty->propertyName];
        
        $this->identityMap->removeIdentity( $class, $id );
    }

    /**
     * Removes the relation between $object and $relatedObject.
     *
     * This method is used to delete an existing relation between 2 objects.
     * Like {@link addRelatedObject()} this method does not store the related
     * object after removing its relation properties (unset), except for {@link
     * ezcPersistentManyToManyRelation()}s, for which the relation record is
     * deleted from the database.
     *
     * If between the classes of $object and $relatedObject multiple relations
     * are defined using a {@link ezcPersistentRelationCollection}, the
     * $relationName parameter becomes necessary. It defines which exact
     * relation to affect here.
     *
     * Removal of related objects is reflected in the identity map
     * automatically and also in named related object sets.
     *
     * @param ezcPersistentObject $object        Source object of the relation.
     * @param ezcPersistentObject $relatedObject Related object.
     * @param string $relationName
     *
     * @throws ezcPersistentRelationOperationNotSupportedException
     *         if a relation to create is marked as "reverse".
     * @throws ezcPersistentRelationNotFoundException
     *         if the deisred relation is not defined.
     */
    public function removeRelatedObject( $object, $relatedObject, $relationName = null )
    {
        $this->session->removeRelatedObject( $object, $relatedObject, $relationName );
        $this->identityMap->removeRelatedObject( $object, $relatedObject, $relationName );
    }

    /**
     * Deletes persistent objects using the given $query.
     *
     * The $query should be created using {@link createDeleteQuery()}.
     *
     * Attention: Every call to this method will cause the internal {@link
     * ezcPersistentIdentityMap} to be completly reset. This is neccessary to
     * avoid inconsistencies, because the session cannot trace which objects
     * are updated by the query.
     *
     * @throws ezcPersistentQueryException
     *         if the delete query failed.
     *
     * @param ezcQueryDelete $query
     */
    public function deleteFromQuery( ezcQueryDelete $query )
    {
        $this->identityMap->reset();
        return $this->session->deleteFromQuery( $query );
    }

    /**
     * Returns a delete query for the given persistent object $class.
     *
     * The query is initialized to delete from the correct table and
     * it is only neccessary to set the where clause.
     *
     * Example:
     * <code>
     * $q = $session->createDeleteQuery( 'Person' );
     * $q->where( $q->expr->gt( 'age', $q->bindValue( 15 ) ) );
     * $session->deleteFromQuery( $q );
     * </code>
     *
     * Attention: If you use a query generated by this method to delete objects,
     * the internal {@link ezcPersistentIdentityMap} will be completly reset.
     * This is neccessary to avoid inconsistencies, because the session cannot
     * trace which objects are deleted by the query.
     *
     * @throws ezcPersistentObjectException
     *         if there is no such persistent class.
     *
     * @param string $class
     *
     * @return ezcQueryDelete
     */
    public function createDeleteQuery( $class )
    {
        return $this->session->createDeleteQuery( $class );
    }

    /**
     * Loads an object of $class with $id and related objects defined by $relations.
     *
     * This method loads and returns the object of $class with $id. In
     * addition, all objects defined by $relations are loaded and fetched into
     * the identity map. Those can then be retrieved using {@link
     * getRelatedObjects()}, without issueing further database queries.
     *
     * Example for the $relations parameter:
     * <code>
     * <?php
     *  array(
     *      'relationAlias_1' => new ezcPersistentRelationFindDefinition(
     *          'relatedClass_1',
     *          null,
     *          array(
     *              'deeperAlias_1' => new ezcPersistentRelationFindDefinition(
     *                  'deeperRelatedClass_1'
     *              )
     *          )
     *      ),
     *      'relationAlias_2' => new ezcPersistentRelationFindDefinition(
     *          'relatedClass_2'
     *      )
     *  );
     * ?>
     * </code>
     *
     * Each relation is defined by an {@link
     * ezcPersistentRelationFindDefinition} struct This defines the related
     * class to load objects for, optionally a relation name (second parameter)
     * and possibly an array of deeper relations. All array keys on all levels
     * of the $relations parameter must be unique!
     *
     * In the example, if $class is 'myClass' and $id is 23, the object of
     * myClass with ID 23 is loaded and returned. In addition, all objects of
     * relatedClass_1 and relatedClass_2, that related to the loaded object,
     * are loaded and stored in the identity map. For each of these objects of
     * class relatedClass_1, all related objects of class deeperRelatedClass_1
     * are loaded and also stored in the identity map.
     * 
     * @see createFindQueryWithRelations()
     *
     * @param string $class 
     * @param string $id 
     * @param array(string=>ezcPersistentRelationFindDefinition) $relations 
     * @return ezcPersistentObject
     */
    public function loadWithRelatedObjects( $class, $id, array $relations )
    {
        $this->initializeQueryCreator();

        $select = $this->queryCreator->createLoadQuery( $class, $id, $relations );

        $stmt = $select->prepare();
        $stmt->execute();

        $this->initializeObjectExtractor();

        return $this->objectExtractor->extractObjectWithRelatedObjects(
            $stmt,
            $class,
            $id,
            $relations
        );
    }

    /**
     * Returns if $relatedObject is related to $sourceObject.
     *
     * Checks the relation conditions between $sourceObject and $relatedObject 
     * and returns true, if $relatedObject is related to $sourceObject, 
     * otherwise false. Relation state is determined through the identity map,
     * in case the relation between $sourceObject and $relatedObject has been
     * recorded there. Otherwise this method dispatches to
     * {@link ezcPersistentSession::isRelated()}.
     *
     * In case multiple relations are defined between the
     * classes of $sourceObject and $relatedObject, the $relationName parameter
     * becomes mandatory. If it is not provided in this case, an {@link 
     * ezcPersistentUndeterministicRelationException} is thrown.
     *
     * Note that checking relations of type {@link 
     * ezcPersistentManyToManyRelation} will issue a database query, if the 
     * relation is not recorded in the identity map. Other relations will not 
     * perform this query at all.
     * 
     * @param ezcPersistentObject $sourceObj 
     * @param ezcPersistentObject $relatedObj 
     * @param string $relationName
     * @return bool
     */
    public function isRelated( $sourceObject, $relatedObject, $relationName = null )
    {
        $srcClass = get_class( $sourceObject );
        $relClass = get_class( $relatedObject );

        $storedRelatedObjects = $this->identityMap->getRelatedObjects(
            $sourceObject,
            $relClass,
            $relationName
        );

        if ( $storedRelatedObjects !== null )
        {
            return in_array( $relatedObject, $storedRelatedObjects );
        }

        return $this->session->isRelated( $sourceObject, $relatedObject );
    }

    /**
     * Initializes the global query creator for this session.
     *
     * Checks if the query creator already exists and instantiates it, if not.
     */
    private function initializeQueryCreator()
    {
        if ( $this->queryCreator === null )
        {
            $this->queryCreator = new ezcPersistentIdentityRelationQueryCreator(
                $this->session->definitionManager,
                $this->session->database
            );
        }
    }

    /**
     * Initializes the global object extractor for this session.
     *
     * Checks if the object extractor already exists and instantiates it, if not.
     */
    private function initializeObjectExtractor()
    {
        if ( $this->objectExtractor === null )
        {
            $this->objectExtractor = new ezcPersistentIdentityRelationObjectExtractor(
                $this->properties['identityMap'],
                $this->session->definitionManager,
                $this->properties['options']
            );
        }
    }

    /**
     * Returns a hash map between property and column name for the given
     * definition $def.
     *
     * The alias map can be used with the query classes. If $prefixTableName is
     * set to false, only the column names are used as alias targets.
     *
     * @param ezcPersistentObjectDefinition $def Definition.
     * @param bool $prefixTableName
     * @return array(string=>string)
     */
    public function generateAliasMap( ezcPersistentObjectDefinition $def, $prefixTableName = true )
    {
        return $this->session->generateAliasMap( $def, $prefixTableName );
    }

    /**
     * Returns all the columns defined in the persistent object.
     *
     * If $prefixTableName is set to false, raw column names will be used,
     * without prefixed table name.
     *
     * @param ezcPersistentObjectDefinition $def Defintion.
     * @param bool $prefixTableName
     * @return array(int=>string)
     */
    public function getColumnsFromDefinition( ezcPersistentObjectDefinition $def, $prefixTableName = true )
    {
        return $this->session->getColumnsFromDefinition( $def, $prefixTableName );
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property does not exist.
     *
     * @param string $name
     * @param mixed $value
     *
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'identityMap':
                throw new ezcBasePropertyPermissionException(
                    $name,
                    ezcBasePropertyPermissionException::READ
                );

            case 'options':
                if ( !( $value instanceof ezcPersistentSessionIdentityDecoratorOptions ) )
                {
                    throw new ezcBaseValueException(
                        $name,
                        $value,
                        'ezcPersistentSessionIdentityDecoratorOptions'
                    );
                }
                break;

            default:
                // Decorator: Dispatch unknown options to inner session
                $this->session->$name = $value;
                return;
        }
        $this->properties[$name] = $value;
    }

    /**
     * Property get access.
     *
     * Simply returns a given property.
     * 
     * @throws ezcBasePropertyNotFoundException
     *         If a the value for the property propertys is not an instance of
     * @param string $propertyName The name of the property to get.
     * @return mixed The property value.
     *
     * @ignore
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the given property does not exist.
     * @throws ezcBasePropertyPermissionException
     *         if the property to be set is a write-only property.
     */
    public function __get( $propertyName )
    {
        if ( array_key_exists( $propertyName, $this->properties ) )
        {
            return $this->properties[$propertyName];
        }
        return $this->session->$propertyName;
    }

    /**
     * Returns if a property exists.
     *
     * Returns true if the property exists in the {@link $properties} array
     * (even if it is null) and false otherwise. 
     *
     * @param string $propertyName Option name to check for.
     * @return void
     * @ignore
     */
    public function __isset( $propertyName )
    {
        return (
            array_key_exists( $propertyName, $this->properties )
            || isset( $this->session->$propertyName )
        );
    }
}
?>
