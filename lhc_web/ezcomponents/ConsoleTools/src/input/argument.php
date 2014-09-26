<?php
/**
 * File containing the ezcConsoleArgument class.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * The ezcConsoleArgument class represents an argument on the console.
 * This class is the container to store information about an argument submitted
 * to a shell application. It is used to define the appearance of an argument
 * before parsing the parameter string and contains the received value
 * afterwards. Argument objects are stored in an instance of the set class
 * {@link ezcConsoleArguments} which is stored in
 * {ezcConsoleInput::$argumentDefinition}.
 * 
 * @property string $name      The name for the argument. Must be unique.
 * @property int $type         The value type. 
 * @property string $shorthelp A short help text. 
 * @property string $longhelp  A long help text-
 * @property bool $mandatory   Whether the argument is mandatory.
 * @property mixed $default    A default value, if not mandatory.
 * @property bool $multiple    Whether the argument accepts multiple values.
 * @property-read mixed $value The value parsed from the parameter string, using
 *                             {@link ezcConsoleInput::process()}.
 * @package ConsoleTools
 * @version 1.6.1
 */
class ezcConsoleArgument
{
    /**
     * Properties 
     * 
     * @var array
     */
    protected $properties = array(
        "name"      => null,
        "type"      => ezcConsoleInput::TYPE_STRING,
        "shorthelp" => "No help available.",
        "longhelp"  => "There is no help for this argument available.",
        "mandatory" => true,
        "multiple"  => false,
        "default"   => null,
        "value"     => null,
    );

    /**
     * Creates a new console argument object.
     * Creates a new console argument object, which represents a single
     * argument on the shell. Arguments are stored insiede 
     * {@link ezcConsoleArguments} which is used with {@link ezcConsoleInput}.
     *
     * For the type property see {@link ezcConsoleInput::TYPE_STRING} and
     * {@link ezcConsoleInput::TYPE_INT}. If 1 argument is defined as optional
     * ($mandatory = false), all following arguments are autolamtically
     * considered optional, too.
     * 
     * @param string $name      The name for the argument. Must be unique.
     * @param int $type         The value type. 
     * @param string $shorthelp A short help text. 
     * @param string $longhelp  A long help text-
     * @param bool $mandatory   Whether the argument is mandatory.
     * @param bool $multiple    Whether the argument accepts multiple values.
     * @param mixed $default    A default value, if not mandatory.
     * @return void
     */
    public function __construct(
        $name      = null,
        $type      = ezcConsoleInput::TYPE_STRING,
        $shorthelp = "No help available.",
        $longhelp  = "There is no help for this argument available.",
        $mandatory = true,
        $multiple  = false,
        $default   = null
    )
    {
        if ( !is_string( $name ) || strlen( $name ) < 1 )
        {
            throw new ezcBaseValueException( "name", $name, "string, length > 0" );
        }
        $this->properties["name"] = $name;

        $this->type               = $type;
        $this->shorthelp          = $shorthelp;
        $this->longhelp           = $longhelp;
        $this->mandatory          = $mandatory;
        $this->multiple           = $multiple;
        $this->default            = $default;
    }

    /**
     * Property set access.
     * 
     * @param string $propertyName  Name of the property.
     * @param string $propertyValue Valze for the property.
     * @return void
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case "name":
                throw new ezcBasePropertyPermissionException( $propertyName, ezcBasePropertyPermissionException::READ );
                break;
            case "type":
                if ( $propertyValue !== ezcConsoleInput::TYPE_INT && $propertyValue !== ezcConsoleInput::TYPE_STRING )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, "string, length > 0" );
                }
                break;
            case "shorthelp":
            case "longhelp":
                if ( is_string( $propertyValue ) === false )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, "string" );
                }
                break;
            case "mandatory":
            case "multiple":
                if ( is_bool( $propertyValue ) === false )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, "bool" );
                }
                break;
            case "default":
                if ( is_scalar( $propertyValue ) === false && is_array( $propertyValue ) === false && $propertyValue !== null )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, "array, scalar or null" );
                }
                break;
            case "value":
                if ( is_scalar( $propertyValue ) === false && is_array( $propertyValue ) === false && $propertyValue !== null )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, "string or null" );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
        $this->properties[$propertyName] = $propertyValue;
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
        if ( isset( $this->$propertyName ) )
        {
            return $this->properties[$propertyName];
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
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
