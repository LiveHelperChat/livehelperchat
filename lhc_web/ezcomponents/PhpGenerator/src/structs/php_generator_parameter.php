<?php
/**
 * File containing the ezcPhpGeneratorParameter class
 *
 * @package PhpGenerator
 * @version 1.0.6
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcPhpGeneratorParameter holds information about a function or method parameter.
 *
 * @see ezcPhpGenerator::appendMethodCal()
 * @see ezcPhpGenerator::appendFunctionCall()
 *
 * @package PhpGenerator
 * @version 1.0.6
 */
class ezcPhpGeneratorParameter extends ezcBaseStruct
{
    /**
     * Specifies that $variable contains the name of a variable which exists in
     * the generated code.
     */
    const VARIABLE = 1;

    /**
     * Specifies that $variable contains data that should be inserted directly
     * into the generated code using var_export.
     */
    const VALUE = 2;

    /**
     * The type of the parameter. Use either VARIABLE or VALUE.
     * @var int
     */
    public $type;

    /**
     * The actual data of the variable if it is a VALUE type or the name of the variable if it is a VARIABLE type.
     * @var mixed
     */
    public $variable = '';

    /**
     * Constructs a new ezcPhpGeneratorParameter.
     *
     * @param string $variable Either the name of a variable or variable data.
     * @param int $type Controls if $variable contains the name of a variable or actual data.
     */
    public function __construct( $variable = '', $type = ezcPhpGeneratorParameter::VARIABLE )
    {
        $this->type = $type;
        $this->variable = $variable;
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
     * @return ezcPhpGeneratorParameter
     */
    public static function __set_state( array $array )
    {
        return new ezcPhpGeneratorParameter( $array['variable'], $array['type'] );
    }
}
?>
