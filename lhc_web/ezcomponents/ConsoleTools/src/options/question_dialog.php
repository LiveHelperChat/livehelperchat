<?php
/**
 * File containing the ezcConsoleQuestionDialogOptions class.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Basic options class for ezcConsoleDialog implementations.
 * 
 * @package ConsoleTools
 * @version 1.6.1
 *
 * @property string $text
 *           The question itself.
 * @property ezcConsoleQuestionDialogValidator $validator
 *           The validator to use with this dialog.
 * @property bool $showResults
 *           Wether to display the possible results and the default selection.
 * @property string $format
 *           The output format for the dialog.
 */
class ezcConsoleQuestionDialogOptions extends ezcConsoleDialogOptions
{

    /**
     * Construct a new options object.
     * Options are constructed from an option array by default. The constructor
     * automatically passes the given options to the __set() method to set them 
     * in the class.
     * 
     * @throws ezcBasePropertyNotFoundException
     *         If trying to access a non existent property.
     * @throws ezcBaseValueException
     *         If the value for a property is out of range.
     * @param array(string=>mixed) $options The initial options to set.
     */
    public function __construct( array $options = array() )
    {
        $this->properties["text"]           = "Please enter a value: ";
        $this->properties["validator"]      = new ezcConsoleQuestionDialogTypeValidator();
        $this->properties["showResults"] = false;
        parent::__construct( $options );
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
            case "text":
                if ( is_string( $propertyValue ) === false || strlen( $propertyValue ) < 1 )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        "string, length > 0"
                    );
                }
                break;
            case "showResults":
                if ( is_bool( $propertyValue ) === false )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, "bool" );
                }
                break;
            case "validator":
                if ( ( $propertyValue instanceof ezcConsoleQuestionDialogValidator ) === false )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, "ezcConsoleQuestionDialogValidator" );
                }
                break;
            default:
                parent::__set( $propertyName, $propertyValue );
                return;
        }
        $this->properties[$propertyName] = $propertyValue;
    }
}

?>
