<?php
/**
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.4.4
 * @filesource
 * @package DatabaseSchema
 */
/**
 * A container to store table difference information in.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaTableDiff extends ezcBaseStruct
{
    /**
     * All added fields
     *
     * @var array(string=>ezcDbSchemaField)
     */
    public $addedFields;

    /**
     * All changed fields
     *
     * @var array(string=>ezcDbSchemaField)
     */
    public $changedFields;

    /**
     * All removed fields
     *
     * @var array(string=>bool)
     */
    public $removedFields;

    /**
     * All added indexes
     *
     * @var array(string=>ezcDbSchemaIndex)
     */
    public $addedIndexes;

    /**
     * All changed indexes
     *
     * @var array(string=>ezcDbSchemaIndex)
     */
    public $changedIndexes;

    /**
     * All removed indexes
     *
     * @var array(string=>bool)
     */
    public $removedIndexes;

    /**
     * Constructs an ezcDbSchemaTableDiff object.
     *
     * @param array(string=>ezcDbSchemaField) $addedFields
     * @param array(string=>ezcDbSchemaField) $changedFields
     * @param array(string=>bool)             $removedFields
     * @param array(string=>ezcDbSchemaIndex) $addedIndexes
     * @param array(string=>ezcDbSchemaIndex) $changedIndexes
     * @param array(string=>bool)             $removedIndexes
     */
    function __construct( $addedFields = array(), $changedFields = array(),
            $removedFields = array(), $addedIndexes = array(), $changedIndexes =
            array(), $removedIndexes = array() )
    {
        $this->addedFields = $addedFields;
        $this->changedFields = $changedFields;
        $this->removedFields = $removedFields;
        $this->addedIndexes = $addedIndexes;
        $this->changedIndexes = $changedIndexes;
        $this->removedIndexes = $removedIndexes;
    }

    static public function __set_state( array $array )
    {
        return new ezcDbSchemaTableDiff(
             $array['addedFields'], $array['changedFields'], $array['removedFields'],
             $array['addedIndexes'], $array['changedIndexes'], $array['removedIndexes']
        );
    }
}
?>
