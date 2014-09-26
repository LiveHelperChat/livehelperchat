<?php
/**
 * File containing the ezcConsoleMenuDialogDefaultValidator class.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Default validator for ezcConsoleMenuDialog.
 * This dialog contains a set of menu entries, defined in the $elements
 * property. The result, provided by the user, is checked against the keys of
 * this array. A conversion can be specified to relax the rules for validation
 * (like coverting the result to lower case first). For possibly conversions
 * see the CONVERT_* constants in this class If the user does not provide an
 * answer, a possibly set default value is used.
 * 
 * @package ConsoleTools
 * @version 1.6.1
 *
 * @property array $elements The elements of the menu.
 * @property string $default The default value.
 * @property int $conversion
 *           ezcConsoleDialogValidator::CONVERT_NONE (default) or
 *           ezcConsoleDialogValidator::CONVERT_LOWER or
 *           ezcConsoleDialogValidator::CONVERT_UPPER.
 */
class ezcConsoleMenuDialogDefaultValidator implements ezcConsoleMenuDialogValidator
{
    /**
     * Properties 
     * 
     * @var array
     */
    protected $properties = array(
        "elements"      => array(),
        "default"       => null,
        "conversion"    => self::CONVERT_NONE,
    );

    /**
     * Creates a new menu default validator. 
     * Creates a validator specified by the given parameters. The $elements
     * array specifies the possible menu items to select from. The item
     * identifier (the key of the array) is used to validate the result. The
     * assigned text is displayed as the menu item text. If no result is
     * provided and an optionally provided default value is used. The
     * $conversion parameter can be used to get a conversion applied to the
     * result before validating it.
     * 
     * @param array $elements The elements of the menu.
     * @param mixed $default  The default value.
     * @param int $conversion The conversion to apply.
     * @return void
     */
    public function __construct( array $elements = array(), $default = null, $conversion = self::CONVERT_NONE )
    {
        $this->elements     = $elements;
        $this->default      = $default;
        $this->conversion   = $conversion;
    }

    /**
     * Returns if the given result is valid. 
     * Checks if the given result is a valid key in the $elements property.
     * 
     * @param mixed $result The received result.
     * @return bool If the result is valid.
     */
    public function validate( $result )
    {
        return isset( $this->elements[$result] );
    }

    /**
     * Returns a fixed version of the result, if possible.
     * Converts the given result according to the conversion defined in the
     * $conversion property.
     * 
     * @param mixed $result The received result.
     * @return mixed The manipulated result.
     */
    public function fixup( $result )
    {
        if ( $result === "" && $this->default !== null )
        {
            return $this->default;

        }
        switch ( $this->conversion )
        {
            case self::CONVERT_LOWER:
                return strtolower( $result );
            case self::CONVERT_UPPER:
                return strtoupper( $result );
            case self::CONVERT_NONE:
            default:
                return $result;
        }
    }

    /**
     * Returns a string representing the default value.
     * For example "[y]" to indicate that "y" is the preselected result and
     * will be chosen if no result is provided.
     *
     * @return string The result string.
     */
    public function getResultString()
    {
        return $this->default === null ? "" : "[{$this->default}]";
   }

    /**
     * Returns an array of the elements to display. 
     * 
     * @return array(string=>string) Elements to display.
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * Property read access.
     * 
     * @param string $propertyName Name of the property.
     * @return mixed Value of the property or null.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the the desired property is not found.
     * @ignore
     */
    public function __get( $propertyName )
    {
        if ( isset( $this->$propertyName ) )
        {
            return $this->properties[$propertyName];
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

    /**
     * Property write access.
     * 
     * @param string $propertyName Name of the property.
     * @param mixed $propertyValue The value for the property.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If a the value for the property options is not an instance of
     * @throws ezcBaseValueException
     *         If a the value for a property is out of range.
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case "elements":
                if ( is_array( $propertyValue ) === false )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, "array" );
                }
                break;
            case "default":
                if ( is_scalar( $propertyValue ) === false && $propertyValue !== null )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, "scalar" );
                }
                break;
            case "conversion":
                if ( $propertyValue !== self::CONVERT_NONE && $propertyValue !== self::CONVERT_UPPER && $propertyValue !== self::CONVERT_LOWER )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, "ezcConsoleMenuDialogDefaultValidator::CONVERT_*" );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
        $this->properties[$propertyName] = $propertyValue;
    }

    /**
     * Property isset access.
     * 
     * @param string $propertyName Name of the property.
     * @return bool True is the property is set, otherwise false.
     * @ignore
     */
    public function __isset( $propertyName )
    {
        return array_key_exists( $propertyName, $this->properties );
    }
}

?>
