<?php
/**
 * File containing the ezcPersistentSingleTableMap.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class to create {ezcPersistentRelation::$columnMap} properties.
 * Maps a source table and column and to a destination table and column, to
 * establish a relation between the 2 tables.
 * 
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentSingleTableMap extends ezcBaseStruct
{

    /**
     * Column of the first table used for mapping. 
     * 
     * @var string
     */
    public $sourceColumn;

    /**
     * Column of the second table, which should be mapped to the first column. 
     * 
     * @var string
     */
    public $destinationColumn;

    /**
     * Create a new ezcPersistentSingleTableMap. 
     * 
     * @param string $sourceColumn      {@link $sourceColumn}
     * @param string $destinationColumn {@link $destinationColumn}
     */
    public function __construct( $sourceColumn, $destinationColumn )
    {
        $this->sourceColumn         = $sourceColumn;
        $this->destinationColumn    = $destinationColumn;
    }

    /**
     * Sets the state of this map.
     *
     * @param array(key=>value) $state
     * @ignore
     */
    public static function __set_state( array $state )
    {
        return new ezcPersistentSingleTableMap(
            $state["sourceColumn"],
            $state["destinationColumn"]
        );
    }
}

?>
