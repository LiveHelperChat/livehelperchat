<?php
/**
 * File containing the ezcWebdavFlaggedPropertyStorage class.
 * 
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Container class for ezcWebdavProperty objects with associated flags.
 *
 * An instance of this class is used to manage WebDAV properties, namely
 * instances of {@link ezcWebdavProperty}. Properties are structured by their
 * name and the namespace they reside in.
 *
 * The stored objects are associated with some user defined flags. Other then
 * that the class behaves like {@link ezcWebdavBasicPropertyStorage} does.
 * 
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavFlaggedPropertyStorage extends ezcWebdavBasicPropertyStorage
{
    /**
     * Next ID for a element in the ordered property list, to generate valid
     * IDs even when some contents has been removed.
     * 
     * @var array
     */
    protected $flags = array();

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
     * The $flags can be used to attach additional information to the property.
     * This is used by {@link ezcWebdavTransport} when parsing a {@link
     * ezcWebavPropPatchRequest}, to indicate what should happen with the
     * affected property in the {@link ezcWebdavBackend}. Flags used there are:
     *
     * <ul>
     *    <li>{@link ezcWebdavPropPatchRequest::SET}</li>
     *    <li>{@link ezcWebdavPropPatchRequest::REMOVE}</li>
     * </ul>
     * 
     * The default for this parameter does not have a meaning in terms of
     * {@link ezcWebdavPropPatchRequest}.
     * 
     * @param ezcWebdavProperty $property 
     * @param mixed $flag 
     * @return void
     */
    public function attach( ezcWebdavProperty $property, $flag = 0 )
    {
        $namespace = $property->namespace;
        $name      = $property->name;

        // Update list of ordered properties
        if ( !isset( $this->properties[$namespace] ) ||
             !isset( $this->properties[$namespace][$name] ) )
        {
            $this->propertyOrder[$this->propertyOrderNextId++] = array( $namespace, $name );
        }

        // Add property
        $this->properties[$namespace][$name] = $property;
        $this->flags[$namespace][$name] = $flag;
    }
    
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
    public function detach( $name, $namespace = 'DAV:' )
    {
        if ( isset( $this->properties[$namespace] ) &&
             isset( $this->properties[$namespace][$name] ) )
        {
            unset( $this->properties[$namespace][$name] );
            unset( $this->flags[$namespace][$name] );
        }
    }
    
    /**
     * Returns the flags for property.
     *
     * Returns the flags of a proerpty from the flagged property storage by the
     * $name and optionally the $naemspace of the property. If no $namespace is
     * given, the default namespace for live properties ('DAV:') is used.
     *
     * The method returns 0, if no flag has been explicitly assigned, and null,
     * if the property does not exist in the storage.
     * 
     * @param string $name 
     * @param string $namespace
     * @return mixed
     */
    public function getFlag( $name, $namespace = 'DAV:' )
    {
        if ( isset( $this->properties[$namespace] ) &&
             isset( $this->properties[$namespace][$name] ) )
        {
            return $this->flags[$namespace][$name];
        }

        return null;
    }
}

?>
