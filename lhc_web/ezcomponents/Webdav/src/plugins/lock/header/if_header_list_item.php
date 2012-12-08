<?php
/**
 * File containing the abstract ezcWebdavLockIfHeaderListItem class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Item that occured in the If header of the request.
 *
 * The If header (described in RFC 2518) may consist of a list of items, which
 * contain ETags, lock tokens and may be negated. Each item is either assigned
 * to a certain resource or must be globally applied to all resources affected
 * by a request. In the first case, instances of this class occur in a {@link
 * ezcWebdavLockIfHeaderTaggedList}, in the latter case, a single instance
 * occurs in a {@link ezcWebdavLockIfHeaderNoTagList}
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @access private
 */
class ezcWebdavLockIfHeaderListItem
{
    /**
     * Array of lock tokens. 
     * 
     * @var array(string)
     */
    protected $lockTokens;

    /**
     * Array of ETags. 
     * 
     * @var array(string)
     */
    protected $eTags;

    /**
     * Creates a new list item that occurs in an If header list.
     *
     * An item may consist of an arbitrary number of $lockTokens and $eTags and
     * might be indicated to be $negated. If the item is $negated, no resource
     * affected by the current request may fit into the conditions defined by
     * $lockTokens and $eTags.
     * 
     * @param array(ezcWebdavIfHeaderCondition) $lockTokens 
     * @param array(ezcWebdavIfHeaderCondition) $eTags 
     */
    public function __construct( array $lockTokens = array(), array $eTags = array() )
    {
        $this->lockTokens = $lockTokens;
        $this->eTags      = $eTags;
    }
    
    /**
     * Property get access.
     *
     * Simply returns a given property.
     * 
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
        if ( $this->__isset( $propertyName ) )
        {
            return $this->$propertyName;
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

    /**
     * Sets a property.
     *
     * This method is called when an property is to be set.
     * 
     * @param string $propertyName The name of the property to set.
     * @param mixed $propertyValue The property value.
     * @return void
     * @ignore
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the given property does not exist.
     * @throws ezcBaseValueException
     *         if the value to be assigned to a property is invalid.
     * @throws ezcBasePropertyPermissionException
     *         if the property to be set is a read-only property.
     */
    public function __set( $propertyName, $propertyValue )
    {
        if ( $this->__isset( $propertyName ) )
        {
            throw new ezcBasePropertyPermissionException(
                $propertyName,
                ezcBasePropertyPermissionException::READ
            );
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
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
        switch ( $propertyName )
        {
            case 'lockTokens':
            case 'eTags':
                return true;
        }
        return false;
    }
}

?>
