<?php
/**
 * File containing the ezcPersistentObject interface
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcPersistentObject is an (optional) interface for classes that provide persistent objects.
 *
 * The PersistentObject component does not require a class to inherit from a
 * certain base class or implement a certain interface to be used with the
 * component. However, this interface can (optionally) be implemented by your
 * persistent classes, to ensure they provide all necessary methods.
 *
 * @package PersistentObject
 * @version 1.7.1
 */
interface ezcPersistentObject
{
    /**
     * Returns the current state of an object.
     *
     * This method returns an array representing the current state of the
     * object. The array must contain a key for every attribute of the
     * object, assigned to the value of the attribute. The key must be the name
     * of the object property, not the database column name.
     * 
     * @return array(string=>mixed) The state of the object.
     */
    public function getState();

    /**
     * Sets the state of the object.
     *
     * This method sets the state of the object accoring to a given array,
     * which must conform to the standards defined at {@link getState()}. The
     * $state array is indexed by object property names (not database column
     * names) which have the desired property value assigned.
     * 
     * @param array $state The new state for the object.
     * @return void
     */
    public function setState( array $state );
}

?>
