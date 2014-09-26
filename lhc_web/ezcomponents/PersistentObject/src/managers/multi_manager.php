<?php
/**
 * File containing the ezcPersistentMultiManager class
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Makes it possible to fetch persistent object definitions from several sources.
 *
 * The multimanager will try each of the provided ezcPersistentDefinitionManagers
 * to locate a valid definition for a class.
 *
 * For best performance add the managers which are most likely to contain the definitions
 * first.
 *
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentMultiManager extends ezcPersistentDefinitionManager
{
    /**
     * Holds the list of managers.
     *
     * @var array(ezcPersistentDefinitionManager)
     */
    private $managers;

    /**
     * Constructs a new multimanager that will look for persistent object definitions
     * in all $managers.
     *
     * @param array(ezcPersistentDefinitionManager) $managers
     */
    public function __construct( array $managers = array() )
    {
        $this->managers = $managers;
    }

    /**
     * Adds a manager that can provide persistent object definitions.
     *
     * @param ezcPersistentDefinitionManager $manager
     * @return void
     */
    public function addManager( ezcPersistentDefinitionManager $manager )
    {
        $this->managers[] = $manager;
    }

    /**
     * Returns the definition of the persistent object with the class $class.
     *
     * @throws ezcPersistentDefinitionNotFoundException if no such definition can be found.
     * @param string $class
     * @return ezcPersistentDefinition
     */
    public function fetchDefinition( $class )
    {
        $def = null;
        $errors = "";
        foreach ( $this->managers as $man )
        {
            try
            {
                $def = $man->fetchDefinition( $class );
            }
            catch ( ezcPersistentDefinitionNotFoundException $e )
            {
                $errors = $e->getMessage() . "\n";
            }

            if ( $def !== null )
            {
                return $def;
            }
        }
        throw new ezcPersistentDefinitionNotFoundException( $class, $errors );
    }
}
?>
