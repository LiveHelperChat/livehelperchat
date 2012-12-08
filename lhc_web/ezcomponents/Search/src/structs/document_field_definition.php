<?php
/**
 * File containing the ezcSearchDefinitionDocumentField class.
 *
 * @package Search
 * @version 1.0.9
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The struct contains a field definition.
 *
 * @package Search
 * @version 1.0.9
 */
class ezcSearchDefinitionDocumentField
{
    /**
     * The field name
     *
     * @var string
     */
    public $field;

    /**
     * The type, see {@link ezcSearchDocumentDefinition} for the allowed types.
     *
     * @var int
     */
    public $type;

    /**
     * How much priority to give to a specific field.
     *
     * @var float
     */
    public $boost;

    /**
     * Whether the field should be part of the result set
     *
     * @var bool
     */
    public $inResult;

    /**
     * Whether there can be multiple values for this field
     *
     * @var bool
     */
    public $multi;

    /**
     * Whether this field should be used for highlighting
     *
     * @var bool
     */
    public $highlight;

    /**
     * Contructs a new ezcSearchDefinitionDocumentField.
     *
     * @param string $field
     * @param int $type
     * @param float $boost
     * @param bool $inResult
     * @param bool $multi
     * @param bool $highlight
     */
    public function __construct( $field, $type = ezcSearchDocumentDefinition::TEXT, $boost = 1.0, $inResult = true, $multi = false, $highlight = false )
    {
        $this->field = $field;
        $this->type = $type;
        $this->boost = $boost;
        $this->inResult = $inResult;
        $this->multi = $multi;
        $this->highlight = $highlight;
    }

    /**
     * Returns a new instance of this class with the data specified by $array.
     *
     * $array contains all the data members of this class in the form:
     * array('member_name'=>value).
     *
     * __set_state makes this class exportable with var_export.
     * var_export() generates code, that calls this method when it
     * is parsed with PHP.
     *
     * @param array(string=>mixed) $array
     * @return ezcSearchDefinitionDocumentField
     */
    static public function __set_state( array $array )
    {
        return new ezcSearchDefinitionDocumentField( $array['field'], $array['type'], $array['boost'], $array['inResult'], $array['multi'], $array['highlight'] );
    }
}
?>
