<?php
/**
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.4.4
 * @filesource
 * @package DatabaseSchema
 */
/**
 * A container to store a table index' field in.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaIndexField extends ezcBaseStruct
{
    /**
     * The sorting of the index (false = descending, true = ascending)
     *
     * @var int
     */
    public $sorting;

    /**
     * Constructs an ezcDbSchemaIndexField object.
     *
     * @param int $sorting
     */
    function __construct( $sorting = null )
    {
        if ( !is_null( $sorting ) && !is_int( $sorting ) )
        {
            $sorting = (int) ( $sorting == 'ascending' ? true : false );
        }
        $this->sorting = $sorting;
    }

    static public function __set_state( array $array )
    {
        return new ezcDbSchemaIndexField(
            $array['sorting']
        );
    }
}
?>
