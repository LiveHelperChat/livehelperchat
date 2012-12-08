<?php
/**
 * File containing the ezcConsoleQuestionDialogTypeValidator class.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Validator class to validate a certain data type.
 * Validator class for ezcConsoleQuestionDialog objects that validates a
 * certain datatype.
 * 
 * @package ConsoleTools
 * @version 1.6.1
 *
 * @property int $type
 *           One of ezcConsoleQuestionDialogTypeValidator::TYPE_*. The type to
 *           check against and convert results to.
 * @property mixed $default
 *           A default value if no result given.
 */
class ezcConsoleQuestionDialogTypeValidator implements ezcConsoleQuestionDialogValidator
{
    /**
     * Data type string. 
     */
    const TYPE_STRING = 0;
    /**
     * Data type int. 
     */
    const TYPE_INT    = 1;
    /**
     * Data type float. 
     */
    const TYPE_FLOAT  = 2;
    /**
     * Data type bool. 
     * The results 1 and "true" will be cased to true, 0 and "false" to false.
     */
    const TYPE_BOOL   = 3;

    /**
     * Properties.
     * 
     * @var array
     */
    protected $properties = array(
        "type"      => self::TYPE_STRING,
        "default"   => null,
    );

    /**
     * Creates a new question dialog type validator.
     * Creates a new question dialog type validator, which validates the result
     * specified to be of a certaon type. The $type must be one of the TYPE_*
     * constants in this class. If no value is provided by the user a possibly
     * set $default value is used instead.
     * 
     * @param int $type      One of ezcConsoleQuestionDialogTypeValidator::TYPE_*.
     * @param mixed $default Default value according to $type.
     * @return void
     */
    public function __construct( $type = self::TYPE_STRING, $default = null )
    {
        $this->type = $type;
        $this->default = $default;
    }

    /**
     * Returns if the given result is valid. 
     * Returns if the result is of the given type.
     * 
     * @param mixed $result The result to check.
     * @return bool True if the result is valid. Otherwise false.
     */
    public function validate( $result )
    {
        if ( $result === "" )
        {
            return $this->default !== null;
        }
        switch ( $this->type )
        {
            case self::TYPE_INT:
                return is_int( $result );
            case self::TYPE_FLOAT:
                return is_float( $result );
            case self::TYPE_BOOL:
                return is_bool( $result );
            case self::TYPE_STRING:
            default:
                return is_string( $result );
        }
    }

    /**
     * Returns a fixed version of the result, if possible.
     * Returns the value casted into the correct type or the default value, if
     * it exists and the result is empty.
     * 
     * @param mixed $result The result received.
     * @return mixed The manipulated result.
     */
    public function fixup( $result )
    {
        if ( $result === "" && $this->default !== null )
        {
            return $this->default;
        }
        switch ( $this->type )
        {
            case self::TYPE_INT:
                return ( preg_match( "/^[0-9\-]+$/", $result ) !== 0 ) ? (int) $result : $result;
            case self::TYPE_FLOAT:
                return ( preg_match( "/^[0-9.E\-]+$/i", $result ) !== 0 ) ? (float) $result : $result;
            case self::TYPE_BOOL:
                switch ( $result )
                {
                    case "1":
                    case "true":
                        return true;
                    case "0":
                    case "false":
                        return false;
                }
            case self::TYPE_STRING:
            default:
                return $result;
        }
    }

    /**
     * Returns a string representing valid results.
     * Returns the string that can will be displayed with the question to
     * indicate valid results to the user and a possibly set default, if
     * available.
     * 
     * @return string
     */
    public function getResultString()
    {
        $res = "(<%s>)" . ( $this->default !== null ? " [{$this->default}]" : "" );
        switch ( $this->type )
        {
            case self::TYPE_INT:
                return sprintf( $res, "int" );
            case self::TYPE_FLOAT:
                return sprintf( $res, "float" );
            case self::TYPE_BOOL:
                return sprintf( $res, "bool" );
            case self::TYPE_STRING:
            default:
                return sprintf( $res, "string" );
        }
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
            case "type":
                if ( $propertyValue !== self::TYPE_STRING && $propertyValue !== self::TYPE_INT && $propertyValue !== self::TYPE_FLOAT && $propertyValue !== self::TYPE_BOOL )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, "ezcConsoleQuestionDialogTypeValidator::TYPE_*" );
                }
                break;
            case "default":
                if ( is_scalar( $propertyValue ) === false && $propertyValue !== null )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, "scalar" );
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
