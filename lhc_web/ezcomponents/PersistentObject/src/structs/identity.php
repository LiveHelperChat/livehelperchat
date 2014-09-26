<?php
/**
 * File containing the ezcPersistentIdentity struct.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Struct representing an object identity in ezcPersistentIdentityMap.
 * 
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentIdentity extends ezcBaseStruct
{
    /**
     * The object.
     * 
     * @var object
     */
    public $object;

    /**
     * Related objects of $object. 
     *
     * Structure:
     *
     * <code>
     * <?php
     * array(
     *     '<relatedClassName>' => ArrayObject(
     *         '<id1>' => ezcPersistentObject,
     *         '<id2>' => ezcPersistentObject,
     *         // ...
     *     ),
     *     '<anotherRelatedClassName>' => ArrayObject(
     *         '<idA>' => ezcPersistentObject,
     *         '<idB>' => ezcPersistentObject,
     *         // ...
     *     ),
     *     // ...
     * );
     * ?>
     * </code>
     * 
     * @var array(string=>ArrayObject(mixed=>ezcPersistentObject))
     */
    public $relatedObjects;

    /**
     * Named sets of related objects. 
     *
     * Structure:
     *
     * <code>
     * <?php
     * array(
     *     '<relatedClassName>' => ArrayObject(
     *         '<setName' => array(
     *             '<id1>' => ezcPersistentObject,
     *             '<id2>' => ezcPersistentObject,
     *             // ...
     *         ),
     *         '<anotherSetName' => ArrayObject(
     *             '<idA>' => ezcPersistentObject,
     *             '<idB>' => ezcPersistentObject,
     *             // ...
     *         ),
     *     ),
     *     // ...
     * );
     * ?>
     * </code>
     * 
     * @var array(string=>ArrayObject(mixed=>ezcPersistentObject))
     */
    public $namedRelatedObjectSets;

    /**
     * Stores all references to $object in other identities. 
     *
     * This attribute stores references to all $relatedObjects and
     * $namedRelatedObjectSets sets, the $object of this identity is referenced
     * in.
     * 
     * @var SplObjectStorage(ArrayObject)
     */
    public $references;

    /**
     * Creates a new object identity.
     *
     * Creates an identity struct for $object with relations to its
     * $relatedObjects and $namedRelatedObjectSets. The $references object is
     * used to keep track of places where the $object is referenced (related
     * object sets of other identities).
     * 
     * @param object $object 
     * @param array $relatedObjects 
     * @param array $namedRelatedObjectSets
     * @param SplObjectStorage $references
     */
    public function __construct(
        $object = null,
        array $relatedObjects = array(),
        array $namedRelatedObjectSets = array(),
        SplObjectStorage $references = null
    )
    {
        $this->object                 = $object;
        $this->relatedObjects         = $relatedObjects;
        $this->namedRelatedObjectSets = $namedRelatedObjectSets;
        $this->references             = (
            $references === null
                ? new SplObjectStorage()
                : $references
        );
    }
}

?>
