<?php
/**
 * File containing the ezcWebdavLockIfHeaderNoTagList class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * List class for If header values, if they are not tagged.
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @access private
 */
class ezcWebdavLockIfHeaderNoTagList extends ezcWebdavLockIfHeaderList
{
    /**
     * List items.
     * 
     * @var array(ezcWebdavLockIfHeaderListItem)
     */
    protected $items;

    /**
     * Creates a new tagged If header list.
     *
     * This list contains a single item of type {@link
     * ezcWebdavLockIfHeaderListItem} which is returned for every resource
     * path.
     *
     * @param array(ezcWebdavLockIfHeaderListItem) $items
     */
    public function __construct( array $items )
    {
        $this->items = $items;
    }

    /**
     * Returns if the given $offset exists.
     * 
     * @param string $offset 
     * @return bool
     *
     * @throws ezcBaseValueException
     *         if $offset is not a string with length > 0.
     */
    public function offsetExists( $offset )
    {
        if ( !is_string( $offset ) || strlen( $offset ) < 1 )
        {
            throw new ezcBaseValueException(
                'offset',
                $offset,
                'string, length > 0',
                'Offset must be a valid path.'
            );
        }

        return true;
    }

    /**
     * Returns the value of the given offset.
     *
     * Returns an instance of {@link ezcWebdavLockIfHeaderListItem}, if it
     * exists, null otherwise.
     * 
     * @param string $offset 
     * @return array(ezcWebdavLockIfHeaderListItem)
     *
     * @throws ezcBaseValueException
     *         if $offset is not a string with length > 0.
     */
    public function offsetGet( $offset )
    {
        if ( !is_string( $offset ) || strlen( $offset ) < 1 )
        {
            throw new ezcBaseValueException(
                'offset',
                $offset,
                'string, length > 0',
                'Offset must be a valid path.'
            );
        }

        return $this->items;
    }

    /**
     * Operation not allowed!
     * 
     * @param string $offset 
     * @param ezcWebdavLockIfHeaderListItem $value 
     * @return void
     *
     * @throws RuntimeException
     *         since this operation is not allowed.
     */
    public function offsetSet( $offset, $value )
    {
        throw new RuntimeException( 'Operation not allowed!' );
    }

    /**
     * Operation not allowed!
     * 
     * @param string $offset 
     * @return void
     *
     * @throws RuntimeException
     *         since this operation is not allowed.
     */
    public function offsetUnset( $offset )
    {
        throw new RuntimeException( 'Operation not allowed!' );
    }

    /**
     * Returns all lock tokens submitted in the header.
     *
     * This method returns a list of all lock tokens (without duplicates) that
     * are present in the If header represented by this list.
     * 
     * @return array(string)
     *
     * @todo This should be cached as long as the list is not changed.
     */
    public function getLockTokens()
    {
        $tokens = array();
        foreach ( $this->items as $item )
        {
            $tokens = array_merge( $tokens, $item->lockTokens );
        }
        return array_unique(
            array_map( 'strval', $tokens )
        );
    }
}

?>
