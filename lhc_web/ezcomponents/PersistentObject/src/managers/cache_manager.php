<?php
/**
 * File containing the ezcPersistentCacheManager class.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Caches fetched definitions so they don't have to be read from the original source
 * for each use.
 *
 * The cache is typically used to wrap around another ezcPersistentDefinitionManager
 * of your choice.
 *
 * @version 1.7.1
 * @package PersistentObject
 */
class ezcPersistentCacheManager extends ezcPersistentDefinitionManager
{
    /**
     * Holds the manager that fetches definitions.
     *
     * @var ezcPersistentDefinitionManager
     */
    private $manager;

    /**
     * Holds the persistent object definitions that are currently cached.
     *
     * @var array($className=>ezcPersistentObjectDefinition)
     */
    private $cache = array();

    /**
     * Constructs a new definition cache.
     *
     * @param ezcPersistentDefinitionManager $manager
     */
    public function __construct( ezcPersistentDefinitionManager $manager )
    {
        $this->manager = $manager;
    }

    /**
     * Returns the definition of the persistent object with the class $class.
     *
     * If a definition has been requested already the definition will be served from
     * the cache.
     *
     * @throws ezcPersistentDefinitionNotFoundException if no such definition can be found.
     * @param string $class
     * @return ezcPersistentObjectDefinition
     */
    public function fetchDefinition( $class )
    {
        if ( isset( $this->cache[$class] ) )
        {
            return $this->cache[$class];
        }

        $def = $this->manager->fetchDefinition( $class );

        // cache it
        $this->cache[$class] = $def;
        return $def;
    }
}
?>
