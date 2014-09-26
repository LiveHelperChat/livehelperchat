<?php
/**
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.4.4
 * @filesource
 * @package DatabaseSchema
 */
/**
 * A container to store a table definition in.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaTable extends ezcBaseStruct
{
    /**
     * A list of all the fields in this table.
     *
     * The array is indexed with the field name.
     *
     * @var array(string=>ezcDbSchemaField)
     */
    public $fields;

    /**
     * A list of all the indexes on this table.
     *
     * The array is indexed with the index name, where the index with the name
     * 'primary' is a special one describing the primairy key.
     *
     * @var array(string=>ezcDbSchemaIndex)
     */
    public $indexes;
    
    /**
     * Constructs an ezcDbSchemaTable object.
     *
     * @param array(string=>ezcDbSchemaField) $fields
     * @param array(string=>ezcDbSchemaIndex) $indexes
     */
    function __construct( $fields, $indexes = array() )
    {
        $this->fields = $fields;
        $this->indexes = $indexes;
    }

    static public function __set_state( array $array )
    {
        return new ezcDbSchemaTable(
            $array['fields'], $array['indexes']
        );
    }
}
?>
