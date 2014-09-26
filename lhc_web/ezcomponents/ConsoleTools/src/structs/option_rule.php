<?php
/**
 * File containing the ezcConsoleOptionRule class.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Struct class to store a parameter rule.
 *
 * This struct stores relation rules between parameters. A relation consists of
 * a parameter that the relation refers to and optionally the value(s) the 
 * referred parameter may have assigned. Rules may be used for dependencies and 
 * exclusions between parameters.
 *
 * The ezcConsoleOptionRule class has the following properties:
 * - <b>option</b> <i>ezcConsoleOption</i>, contains the parameter that this rule refers to.
 * - <b>values</b> <i>array(string)</i>, contains a list of values that are accepted.
 *
 * @see ezcConsoleOption
 * 
 * @package ConsoleTools
 * @version 1.6.1
 */
class ezcConsoleOptionRule
{
    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = array( 
        'option' => null,
        'values' => array(),
        'ifSet'  => true
    );

    /**
     * Creates a new option rule.
     *
     * Creates a new option rule. Per default the $values parameter
     * is an empty array, which determines that the option may accept any
     * value. To indicate that a option may only have certain values,
     * place them inside tha $values array. For example to indicate an option
     * may have the values 'a', 'b' and 'c' use:
     *
     * <code>
     * $rule = new ezcConsoleOptionRule( $option, array( 'a', 'b', 'c' ) );
     * </code>
     *
     * If you want to allow only 1 specific value for an option, you do not
     * need to wrap this into an array, when creating the rule. Simply use
     *
     * <code>
     * $rule = new ezcConsoleOptionRule( $option, 'a' );
     * </code>
     *
     * to create a rule, that allows the desired option only to accept the
     * value 'a'.
     *
     * The $ifSet parameter determines, if the rule is validated when its option
     * is set or left out. If $ifSet is true, the rule is validated when the 
     * option is set. Otherwise the rule is validated if the option was not set 
     * by the user.
     *
     * @param ezcConsoleOption $option The option to refer to.
     * @param mixed $values            The affected values.
     * @param bool $ifSet
     */
    public function __construct( ezcConsoleOption $option, array $values = array(), $ifSet = true )
    {
        $this->__set( 'option', $option );
        $this->__set( 'values', $values );
        $this->__set( 'ifSet', $ifSet );
    }
    
    /**
     * Property read access.
     *
     * @throws ezcBasePropertyNotFoundException 
     *         If the the desired property is not found.
     * 
     * @param string $propertyName Name of the property.
     * @return mixed Value of the property or null.
     * @ignore
     */
    public function __get( $propertyName ) 
    {
        switch ( $propertyName )
        {
            case 'option':
                return $this->properties['option'];
            case 'values':
                return $this->properties['values'];
            case 'ifSet':
                return $this->properties['ifSet'];
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
    }
    
    /**
     * Property write access.
     * 
     * @param string $propertyName Name of the property.
     * @param mixed $propertyValue The value for the property.
     *
     * @throws ezcBasePropertyPermissionException
     *         If the property you try to access is read-only.
     * @throws ezcBasePropertyNotFoundException 
     *         If the the desired property is not found.
     * @ignore
     */
    public function __set( $propertyName, $propertyValue ) 
    {
        switch ( $propertyName )
        {
            case 'option':
                if ( !( $propertyValue instanceof ezcConsoleOption ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'ezcConsoleOption' );
                }
                $this->properties['option'] = $propertyValue;
                return;
            case 'values':
                if ( !is_array( $propertyValue ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'array' );
                }
                $this->properties['values'] = $propertyValue;
                return;
            case 'ifSet':
                if ( !is_bool( $propertyValue ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'bool' );
                }
                $this->properties['ifSet'] = $propertyValue;
                return;
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
    }
 
    /**
     * Property isset access.
     * 
     * @param string $propertyName Name of the property to check.
     * @return bool If the property exists or not.
     * @ignore
     */
    public function __isset( $propertyName )
    {
        switch ( $propertyName )
        {
            case 'option':
            case 'values':
            case 'ifSet':
                return true;
        }
        return false;
    }

}

?>
