<?php
/**
 * File containing the ezcWebdavPropertyStorage interface.
 * 
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Interface to define interaction with property storage classes.
 *
 * This interface defines how a property storage should react. It allows you to
 * implemenet your whole own way of property storages (possibly lazy-loading?)
 * and integrate it. A more common way would be to extend the shipped {@link
 * ezcWebdavBasicPropertyStorage}.
 *
 * An instance of a class implementing this interface is used to manage WebDAV
 * properties, namely instances of {@link ezcWebdavProperty}. Properties are
 * usually structured by their name and the namespace they belong to. The
 * WebDAV RFC requires that proprties keep their order in some cases, what is
 * reflected in all shipped implementations.
 *
 * @see ezcWebdavBasicPropertyStorage
 * @see ezcWebdavFlaggedPropertyStorage
 * 
 * @package Webdav
 * @version 1.1.4
 */
interface ezcWebdavPropertyStorage extends Countable, Iterator 
{
    /**
     * Attaches a property to the storage.
     *
     * Adds the given $property to the storage. The property can later be
     * accessed by its name in combination with the namespace through the
     * {@link get()} method. Live properties (and only these) reside in the
     * namespace DAV:, which is the default for all accessor methods.
     *
     * If a property with the same namespace and name is already contained in
     * the storage, it will be overwritten.
     * 
     * @param ezcWebdavProperty $property 
     * @return void
     */
    public function attach( ezcWebdavProperty $property );
    
    /**
     * Detaches a property from the storage.
     *
     * Removes the property with the given $name and $namespace from the
     * storage. If the property does not exist in the storage, the call is
     * silently ignored. If no $namespace is given, the default namespace for
     * live properties ('DAV:') is used.
     * 
     * @param string $name 
     * @param string $namespace
     * @return void
     */
    public function detach( $name, $namespace = 'DAV:' );
    
    /**
     * Returns if the given property exists in the storage. 
     *
     * Returns if the property with the given $name and $namespace is contained
     * in the storage.  If the $namespace parameter is omited, the default live
     * property namespace ('DAV:') is used.
     *
     * @param string $name
     * @param string $namespace
     * @return bool
     */
    public function contains( $name, $namespace = 'DAV:' );

    /**
     * Returns a property from the storage.
     *
     * Returns the property with the given $name and $namespace. If the
     * $namespace parameter is omitted, the default live property namespace
     * ('DAV:') namespace is used. If the desired property is not contained in
     * the storage, null is returned.
     * 
     * @param string $name
     * @param string $namespace
     * @return ezcWebdavProperty|null
     */
    public function get( $name, $namespace = 'DAV:' );

    /**
     * Returns all properties of a given namespace.
     *
     * The returned array is indexed by the property names. Live properties can
     * be accessed by simply ommiting the $namespace parameter, since  then the
     * default namespace for live properties ('DAV:') is used.
     * 
     * @param string $namespace 
     * @return array(string=>ezcWebdavProperty)
     */
    public function getProperties( $namespace = 'DAV:' );

    /**
     * Returns all properties contained in the storage.
     *
     * Returns the complete array stored in {@link $properties}.
     * 
     * @return array(string=>array(string=>ezcWebdavProperty))
     */
    public function getAllProperties();

    /**
     * Diff two property storages.
     *
     * Returns a property storage, which does only contain properties that are
     * not present in the $properties parameter.
     * 
     * @param ezcWebdavPropertyStorage $properties 
     * @return ezcWebdavBasicPropertyStorage
     */
    public function diff( ezcWebdavPropertyStorage $properties );

    /**
     * Intersects between two property storages.
     *
     * Calculate and return an instance of {@link
     * ezcWebdavBasicPropertyStorage} which contains the intersection of two
     * property storages. This means a new property storage will be return
     * which contains all values, which are present in the current and the
     * given $properties property storage.
     * 
     * @param ezcWebdavPropertyStorage $properties 
     * @return ezcWebdavBasicPropertyStorage
     */
    public function intersect( ezcWebdavPropertyStorage $properties );
}

?>
