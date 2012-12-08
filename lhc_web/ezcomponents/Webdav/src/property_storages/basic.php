<?php
/**
 * File containing the ezcWebdavBasicPropertyStorage class.
 * 
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Container class for ezcWebdavProperty objects.
 *
 * An instance of this class is used to manage WebDAV properties, namely
 * instances of {@link ezcWebdavProperty}. Properties are structured by their
 * name and the namespace they reside in.
 * 
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavBasicPropertyStorage implements ezcWebdavPropertyStorage
{
    /**
     * Stores the WebDAV properties.
     *
     * The structure of this array is:
     * <code>
     * array(
     *     'DAV:' => array(
     *         '<live property name>' => ezcWebdavLiveProperty,
     *         // ...
     *     ),
     *     '<another namespace URI>'array(
     *         '<dead property name>' => ezcWebdavDeadProperty,
     *         // ...
     *     ),
     *     // ...
     * )
     * </code>
     * 
     * @var array
     */
    protected $properties = array();

    /**
     * Stores a list of the assigned properties in the order they were
     * assigned, to make this order accessible for the Iterator.
     * 
     * @var array
     */
    protected $propertyOrder = array();

    /**
     * Current position of the iterator in the ordered property list.
     * 
     * @var int
     */
    protected $propertyOrderPosition = 0;

    /**
     * Next ID for a element in the ordered property list, to generate valid
     * IDs even when some contents has been removed.
     * 
     * @var int
     */
    protected $propertyOrderNextId = 0;

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
    public function attach( ezcWebdavProperty $property )
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
        }
    }
    
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
    public function contains( $name, $namespace = 'DAV:' )
    {
        return isset( $this->properties[$namespace][$name] );
    }

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
    public function get( $name, $namespace = 'DAV:' )
    {
        if ( isset( $this->properties[$namespace][$name] ) )
        {
            return $this->properties[$namespace][$name];
        }
        return null;
    }

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
    public function getProperties( $namespace = 'DAV:' )
    {
        if ( !isset( $this->properties[$namespace] ) )
        {
            return array();
        }
        return $this->properties[$namespace];
    }

    /**
     * Returns all properties contained in the storage.
     *
     * Returns the complete array stored in {@link $properties}.
     * 
     * @return array(string=>array(string=>ezcWebdavProperty))
     */
    public function getAllProperties()
    {
        return $this->properties;
    }

    /**
     * Diff two property storages.
     *
     * Returns a property storage, which does only contain properties that are
     * not present in the $properties parameter.
     * 
     * @param ezcWebdavPropertyStorage $properties 
     * @return ezcWebdavBasicPropertyStorage
     */
    public function diff( ezcWebdavPropertyStorage $properties )
    {
        $foreign = $properties->getAllProperties();

        $diffedProperties = new ezcWebdavBasicPropertyStorage();
        foreach ( $this->properties as $namespace => $properties )
        {
            foreach ( $properties as $name => $property )
            {
                if ( !isset( $foreign[$namespace][$name] ) )
                {
                    // Only add properties to new property storage, which could
                    // not be found in the foreign property storage.
                    $diffedProperties->attach( $property );
                }
            }
        }

        return $diffedProperties;
    }

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
    public function intersect( ezcWebdavPropertyStorage $properties )
    {
        $foreign = $properties->getAllProperties();

        $intersection = new ezcWebdavBasicPropertyStorage();
        foreach ( $this->properties as $namespace => $properties )
        {
            foreach ( $properties as $name => $property )
            {
                if ( isset( $foreign[$namespace][$name] ) )
                {
                    // Only add properties to new property storage, which could
                    // be found in both property storages.
                    $intersection->attach( $property );
                }
            }
        }

        return $intersection;
    }

    /*
     * Methods required for Countable
     */

    /**
     * Return property count.
     *
     * Implementation required by interface Countable. Count the numbers of
     * items contained by the instance. Will return the overall item count
     * ignoring different namespaces.
     * 
     * @return int
     */
    public function count()
    {
        $count = 0;
        foreach ( $this->properties as $properties )
        {
            $count += count( $properties );
        }
        
        return $count;
    }

    /**
     * Methods required for Iterator
     */

    /**
     * Implements current() for Iterator.
     *
     * Returns the currently selected element during iteration with foreach.
     * 
     * @return ezcWebdavProperty
     */
    public function current()
    {
        list( $namespace, $name ) = $this->propertyOrder[$this->propertyOrderPosition];

        // Skip detached properties
        while ( !isset( $this->properties[$namespace][$name] ) )
        {
            if ( !isset( $this->propertyOrder[++$this->propertyOrderPosition] ) )
            {
                // We reached the end.
                return false;
            }

            list( $namespace, $name ) = $this->propertyOrder[$this->propertyOrderPosition];
        }

        return $this->properties[$namespace][$name];
    }

    /**
     * Implements key() for Iterator
     *
     * Returns the key of the currently selected element during iteration with
     * foreach.
     * 
     * @return int
     */
    public function key()
    {
        return $this->propertyOrderPosition;
    }

    /**
     * Implements next() for Iterator
     *
     * Advances the internal pointer to the next element during iteration with
     * foreach.
     * 
     * @return mixed
     */
    public function next()
    {
        ++$this->propertyOrderPosition;
    }

    /**
     * Implements rewind() for Iterator
     *
     * Resets the internal pointer to the first element before iteration with
     * foreach.
     * 
     * @return void
     */
    public function rewind()
    {
        $this->propertyOrderPosition = 0;
    }

    /**
     * Implements valid() for Iterator
     *
     * Returns if the internal pointer still points to a valid element when
     * iteration with foreach. If this method returns false, iteration ends.
     * 
     * @return boolean
     */
    public function valid()
    {
        do
        {
            if ( !isset( $this->propertyOrder[$this->propertyOrderPosition] ) )
            {
                // We reached the end.
                return false;
            }

            list( $namespace, $name ) = $this->propertyOrder[$this->propertyOrderPosition];

            if ( isset( $this->properties[$namespace][$name] ) )
            {
                // Found next valid property
                return true;
            }
            ++$this->propertyOrderPosition;
        }
        while ( !isset( $this->properties[$namespace][$name] ) );

        return true;
    }

    /**
     * Clones the property storage deeply.
     * 
     * @return void
     */
    public function __clone()
    {
        foreach ( $this->properties as $namespace => $props )
        {
            foreach ( $props as $name => $prop )
            {
                $this->properties[$namespace][$name] = clone $prop;
            }
        }
    }
}

?>
