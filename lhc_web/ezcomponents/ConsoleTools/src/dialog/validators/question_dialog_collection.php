<?php
/**
 * File containing the ezcConsoleQuestionDialogCollectionValidator class.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Validator class to validate against a set of valid results.
 * This validator class, for {@link ezcConsoleQuestionDialog} objects,
 * validates a given result against a set of predefined values.
 * 
 * @package ConsoleTools
 * @version 1.6.1
 *
 * @property array $collection
 *           The collection of valid answers.
 * @property mixed $default
 *           Default value.
 * @property int $conversion
 *           ezcConsoleDialogValidator::CONVERT_NONE (default) or
 *           ezcConsoleDialogValidator::CONVERT_LOWER or
 *           ezcConsoleDialogValidator::CONVERT_UPPER.
 */
class ezcConsoleQuestionDialogCollectionValidator implements ezcConsoleQuestionDialogValidator
{

    /**
     * Properties.
     * 
     * @var array
     */
    protected $properties  = array(
        "collection"    => array(),
        "default"       => null,
        "conversion"    => self::CONVERT_NONE,
    );

    /**
     * Creates a new question dialog collection validator. 
     * Creates a new question dialog collection validator, which validates the
     * result specified by the user against an array of valid results
     * ($collection). If not value is provided by the user a possibly set
     * $default value is used instead. The $conversion parameter can optionally
     * define a conversion to be performed on the result before validating it.
     * Valid conversions are defined by the CONVERT_* constants in this class.
     * 
     * @param array $collection The collection of valid results.
     * @param mixed $default    Optional default value.
     * @param int $conversion   CONVERT_* constant.
     * @return void
     */
    public function __construct( array $collection, $default = null, $conversion = self::CONVERT_NONE )
    {
        $this->collection = $collection;
        $this->default = $default;
        $this->conversion = $conversion;
    }

    /**
     * Returns if the given result is valid. 
     * Returns if the result is in the $collection array.
     * 
     * @param mixed $result The received result.
     * @return bool If the result is valid.
     */
    public function validate( $result )
    {
        return in_array( $result, $this->collection );
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
            case self::CONVERT_UPPER:
                return strtoupper( $result );
            case self::CONVERT_LOWER:
                return strtolower( $result );
            default:
                return $result;
        }
    }

    /**
     * Returns a string representing valid results.
     * Returns the string that will be displayed with the question to
     * indicate valid results to the user and a possibly set default, if
     * available.
     * 
     * @return string
     */
    public function getResultString()
    {
        return "(" . implode( "/", $this->collection ) . ")" . ( $this->default !== null ? " [{$this->default}]" : "" );
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
            case "collection":
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
                    throw new ezcBaseValueException( $propertyName, $propertyValue, "ezcConsoleQuestionDialogCollectionValidator::CONVERT_*" );
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
