<?php
/**
 * File containing the ezcConsoleQuestionDialogRegexValidator class.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Regex validator for ezcConsoleQuestionDialog
 * Validator class for ezcConsoleQuestionDialog objects that validates by
 * matching a certain regular expression.
 * 
 * @package ConsoleTools
 * @version 1.6.1
 *
 * @property string $pattern
 *           The pattern to use for validation. Delimiters and modifiers
 *           included.
 * @property mixed $default
 *           A default value if no (an empty string) result given.
 */
class ezcConsoleQuestionDialogRegexValidator implements ezcConsoleQuestionDialogValidator
{

    /**
     * Properties
     * 
     * @var array(string=>mixed)
     */
    protected $properties = array(
        "pattern" => null,
        "default" => null,
    );

    /**
     * Create a new question dialog regex validator.
     * Create a new question dialog regex validator, which validates the result
     * specified against a given regular expression. The delimiters and
     * eventual modifiers must be included in the pattern. If not value is
     * provided by the user a possibly set $default value is used instead.
     *
     * 
     * @param string $pattern Pattern to validate against.
     * @param mixed $default  Default value.
     * @return void
     */
    public function __construct( $pattern, $default = null )
    {
        $this->pattern = $pattern;
        $this->default = $default;
    }

    /**
     * Returns if the given result is valid. 
     * Returns if the result matches the regular expression.
     * 
     * @param mixed $result The received result.
     * @return bool If the result is valid.
     */
    public function validate( $result )
    {
        if ( $result === "" )
        {
            return $this->default !== null;
        }
        return preg_match( $this->pattern, $result ) > 0;
    }

    /**
     * Returns a fixed version of the result, if possible.
     * If no result was provided by the user, the default value will be
     * returned, if set.
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
        return $result;
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
        return "(match {$this->pattern})" . ( $this->default !== null ? " [{$this->default}]" : "" );
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
        if ( $this->__isset( $propertyName ) )
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
            case "pattern":
                if ( is_string( $propertyValue ) === false || strlen( $propertyValue ) < 2 )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, "string, length > 1" );
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
