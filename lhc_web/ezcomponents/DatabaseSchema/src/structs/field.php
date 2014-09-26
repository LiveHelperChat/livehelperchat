<?php
/**
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.4.4
 * @filesource
 * @package DatabaseSchema
 */
/**
 * A container to store a field definition in.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaField extends ezcBaseStruct
{
    /**
     * The type of this field
     *
     * @var string
     */
    public $type;

    /**
     * The length of this field.
     *
     * @var integer
     */
    public $length;

    /**
     * Whether this field can store NULL values.
     *
     * @var bool
     */
    public $notNull;

    /**
     * The default value for this field.
     *
     * @var mixed
     */
    public $default;

    /**
     * Whether this field is an auto increment field.
     *
     * @var bool
     */
    public $autoIncrement;

    /**
     * Whether the values for this field contain unsigned integers.
     *
     * @var bool
     */
    public $unsigned;

    /**
     * Constructs an ezcDbSchemaField object.
     *
     * @param string  $type
     * @param integer $length
     * @param bool    $notNull
     * @param mixed   $default
     * @param bool    $autoIncrement
     * @param bool    $unsigned
     */
    function __construct( $type, $length = false, $notNull = false, $default = null, $autoIncrement = false, $unsigned = false )
    {
        $this->type = (string) $type;
        $this->length = (int) $length;
        $this->notNull = (bool) $notNull;
        $this->default = $default;
        $this->autoIncrement = (bool) $autoIncrement;
        $this->unsigned = (bool) $unsigned;

        if ( $type == 'integer' && $notNull && $default === null && $autoIncrement == false )
        {
            $this->default = 0;
        }

        if ( $type == 'integer' && is_numeric( $this->default ) )
        {
            $this->default = (int) $this->default;
        }
    }

    static public function __set_state( array $array )
    {
        return new ezcDbSchemaField(
            $array['type'], $array['length'], $array['notNull'],
            $array['default'], $array['autoIncrement'], $array['unsigned']
        );
    }
}
?>
