<?php
/**
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.4
 * @filesource
 * @package UserInput
 */

/**
 * Provides access to form variables.
 *
 * This class allows you to retrieve input variables from the request in a safe
 * way, by applying filters to allow only wanted data into your application. It
 * works by passing an array that describes your form definition to the
 * constructor of the class. The constructor will then initialize the class
 * with properties that contain the value of your request's input fields.
 *
 * Example:
 * <code>
 * <?php
 * if ( ezcInputForm::hasGetData() )
 * {
 *     $definition = array(
 *        'fieldname'  => new ezcInputFormDefinitionElement(
 *                            ezcInputFormDefinitionElement::REQUIRED, 'filtername'
 *                        ),
 *        'textfield'  => new ezcInputFormDefinitionElement(
 *                            ezcInputFormDefinitionElement::OPTIONAL, 'string'
 *                        ),
 *        'integer1'   => new ezcInputFormDefinitionElement(
 *                            ezcInputFormDefinitionElement::REQUIRED, 'int',
 *                            array( 'min_range' => 0, 'max_range' => 42 )
 *                        ),
 *        'xmlfield'   => new ezcInputFormDefinitionElement(
 *                            ezcInputFormDefinitionElement::REQUIRED, 'unsafe_raw'
 *                        ),
 *        'special'    => new ezcInputFormDefinitionElement(
 *                            ezcInputFormDefinitionElement::OPTIONAL, 'callback',
 *                            array( 'ezcInputFilter', 'special' )
 *                        ),
 *     );
 *     $form = new ezcInputForm( INPUT_GET, $definition );
 *     if ( $form->hasInputField( 'textfield' ) ) // check for optional field
 *     {
 *         $text = $form->textfield;
 *     }
 *
 *     try
 *     {
 *         $xml = $form->xmlfield; // Uses dynamic properties through __get().
 *         $field = $form->fieldname;
 *         $int = $form->integer1;
 *     }
 *     catch ( ezcInputFormException $e )
 *     {
 *         // one of the required fields didn't have valid data.
 *         $invalidProperties = $form->getInvalidProperties();
 *
 *         // Retrieve RAW data for invalid properties so that we can fill in the
 *         // forms online with this RAW data again - Make sure to escape it on
 *         // output though, but that should be done for all data anyway.
 *         if ( in_array( 'xmlfield', $invalidProperties ) )
 *         {
 *             $xml = $form->getUnsafeRawData( 'xmlfield' );
 *         }
 *     }
 *
 *     // Checking optional fields
 *     foreach ( $form->getOptionalProperties() as $property )
 *     {
 *         $name = "property_{$property}";
 *         if ( $form->hasValidData( $property ) )
 *         {
 *             $$name = $form->$property;
 *         }
 *     }
 * }
 * ?>
 * </code>
 *
 * @property-read string $formFields
 *                There is a read-only property for each field that is defined
 *                as input field.
 *
 * @package UserInput
 * @version 1.4
 * @mainclass
 */
class ezcInputForm
{
    /**
     * @var VALID is used in the $properties array to record whether the data
     *            in a specific input variable contained valid data according
     *            to the filter.
     */
    const VALID = 0;

    /**
     * @var INVALID is used in the $properties array to record whether the data
     *              in a specific input variable contained valid data according
     *              to the filter.
     */
    const INVALID = 1;

    const DEF_NO_ARRAY                      = 1;
    const DEF_EMPTY                         = 2;
    const DEF_ELEMENT_NO_DEFINITION_ELEMENT = 3;
    const DEF_NOT_REQUIRED_OR_OPTIONAL      = 5;
    const DEF_WRONG_FLAGS_TYPE              = 6;
    const DEF_UNSUPPORTED_FILTER            = 7;
    const DEF_FIELD_NAME_BROKEN             = 8;

    /**
     * Contains the definition for this form (as passed in the constructor).
     * @var array(string=>ezcInputFormDefinitionElement)
     */
    protected $definition;

    /**
     * Contains a list of all retrieved properties and their status.  The key
     * for each array element is the field name, and the value associated with
     * this key is one of the constants VALID or INVALID.
     * @var array
     */
    protected $properties;

    /**
     * Contains the values of the input variables.  The key for each array
     * element is the field name, and the value associated with this key is the
     * property's value. This array does not have an entry for input fields
     * that do not have valid data.
     * @var array
     */
    protected $propertyValues;

    /**
     * Contains the input source to be used.
     * @var int
     */
    protected $inputSource;

    /**
     * Whether all the input elements are valid
     */
    protected $allElementsValid;

    /**
     * Constructs a new ezcInputForm for $inputSource with $definition.
     *
     * This method constructs a new ezcInputForm with three parameters. The
     * $inputSource parameter selects the input source and should be one of the
     * constants INPUT_GET, INPUT_POST or INPUT_COOKIE. The $definition
     * parameter is an array of ezcInputFormDefinitionElement items and
     * determines which input variables make up this form (see the example at
     * the top of this class). The last parameter, $characterEncoding is the
     * character encoding to use while retrieving input variable data. This
     * parameter has currently no function as it will depend on PHP 6
     * functionality which does not exist yet in the input filter extension.
     *
     * @throws ezcInputFormVariableMissingException when one of the required
     *         input variables is missing.
     * @throws ezcInputFormInvalidDefinitionException when the definition array
     *         is invalid or when the input source was invalid.
     *
     * @param int $inputSource
     * @param array(ezcInputFormDefinitionElement) $definition
     * @param string $characterEncoding
     */
    public function __construct( $inputSource, $definition, $characterEncoding = null )
    {
        if ( ( $returnValue = ezcInputForm::validateDefinition( $definition ) ) !== true )
        {
            throw new ezcInputFormInvalidDefinitionException( $returnValue[1] );
        }
        $this->definition = $definition;
        $this->inputSource = $inputSource;

        $this->parseInput();
    }

    /**
     * Returns whether there is GET data available
     *
     * @return bool True if there is GET data available
     */
    static public function hasGetData()
    {
        return count( $_GET ) > 0;
    }

    /**
     * Returns whether there is POST data available
     *
     * @return bool True if there is POST data available
     */
    static public function hasPostData()
    {
        return count( $_POST ) > 0;
    }

    /**
     * Parses the input according to the definition array.
     *
     * @throws ezcInputFormInvalidDefinitionException when one of the required
     *         input variables is missing or when the input source was invalid.
      */
    protected function parseInput()
    {
        $this->allElementsValid = true;

        if (  !in_array( $this->inputSource, array( INPUT_GET, INPUT_POST, INPUT_COOKIE ) ) )
        {
            throw new ezcInputFormWrongInputSourceException( $this->inputSource );
        }

        foreach ( $this->definition as $elementName => $inputElement )
        {
            $hasVariable = filter_has_var( $this->inputSource, $elementName );
            if ( ! $hasVariable )
            {
                if ( $inputElement->type === ezcInputFormDefinitionElement::REQUIRED )
                {
                    throw new ezcInputFormVariableMissingException( $elementName );
                }
                else
                {
                    $this->properties[$elementName] = ezcInputForm::INVALID;
                    $this->allElementsValid = false;
                    continue;
                }
            }

            $flags = FILTER_NULL_ON_FAILURE | $inputElement->flags;
            $value = filter_input( $this->inputSource, $elementName, filter_id( $inputElement->filterName ), array( 'options' => $inputElement->options, 'flags' => $flags ) );

            if ( $value !== null )
            {
                $this->properties[$elementName] = ezcInputForm::VALID;
                $this->propertyValues[$elementName] = $value;
            }
            else
            {
                $this->properties[$elementName] = ezcInputForm::INVALID;
                $this->allElementsValid = false;
            }
        }
    }

    /**
     * Validates the definition array $definition.
     *
     * Before reading the values from the input source, the definition array
     * can be validated by this method to check whether all necessary
     * elements are correctly formed.
     *
     * With the following code you can check whether the definition is valid:
     * <code>
     * <?php
     * if ( ( $returnValue = ezcInputForm::validateDefinition( $definition ) ) !== true )
     * {
     *     // do something with the error type and error message in $returnValue
     * }
     * else
     * {
     *     // the definition was correct
     * }
     * ?>
     * </code>
     *
     * @param array $definition
     * @return array|bool If the definition is correct the method returns
     *                    boolean true. When an error is found the function
     *                    returns an array where the first element is the type,
     *                    and the second element the error message.
     */
    static public function validateDefinition( $definition )
    {
        // The definition parameter should be an array
        if ( !is_array( $definition ) )
        {
            return array( ezcInputForm::DEF_NO_ARRAY, "The definition array is not an array" );
        }

        // There should be atleast one element
        if ( count( $definition ) === 0 )
        {
            return array( ezcInputForm::DEF_EMPTY, "The definition array is empty" );
        }

        foreach ( $definition as $name => $element )
        {
            // Each element should be an ezcInputFormDefinitionElement
            if ( !$element instanceof ezcInputFormDefinitionElement )
            {
                return array( ezcInputForm::DEF_ELEMENT_NO_DEFINITION_ELEMENT, "The definition for element '{$name}' is not an ezcInputFormDefinitionElement" );
            }

            // The first value in an element should be REQUIRED or OPTIONAL
            if ( !in_array( $element->type, array( ezcInputFormDefinitionElement::OPTIONAL, ezcInputFormDefinitionElement::REQUIRED ), true ) )
            {
                return array( ezcInputForm::DEF_NOT_REQUIRED_OR_OPTIONAL, "The first element definition for element '{$name}' is not ezcInputFormDefinitionElement::OPTIONAL or ezcInputFormDefinitionElement::REQUIRED" );
            }

            // The options should either be an array, a string, or an int
            if ( $element->options !== null )
            {
                $filterOptionsType = gettype( $element->options );
                if ( !in_array( $filterOptionsType, array( 'integer', 'string', 'array' ) ) )
                {
                    return array( ezcInputForm::DEF_WRONG_FLAGS_TYPE, "The options to the definition for element '{$name}' is not of type integer, string or array" );
                }

                // A callback filter should have the form "string" or "array(string, string)"
                if ( $element->filterName == 'callback' )
                {
                    if ( $filterOptionsType == 'integer' )
                    {
                        return array( ezcInputForm::DEF_WRONG_FLAGS_TYPE, "The callback filter for element '{$name}' should not be an integer" );
                    }
                    if ( $filterOptionsType == 'array' )
                    {
                        if ( count( $element->options ) != 2 )
                        {
                            return array( ezcInputForm::DEF_WRONG_FLAGS_TYPE, "The array parameter for the callback filter for element '{$name}' should have exactly two elements" );
                        }
                        if ( gettype( $element->options[0] ) != 'string' || gettype( $element->options[1] ) != 'string' )
                        {
                            return array( ezcInputForm::DEF_WRONG_FLAGS_TYPE, "The array elements for the callback filter for element '{$name}' should both be a string" );
                        }
                    }
                }
            }

            // The options should either be an int
            if ( $element->flags !== null )
            {
                if ( gettype( $element->flags ) !== 'integer' )
                {
                    return array( ezcInputForm::DEF_WRONG_FLAGS_TYPE, "The flags to the definition for element '{$name}' is not of type integer, string or array" );
                }
            }

            // The filter should be an existing filter
            if ( !in_array( $element->filterName, filter_list() ) )
            {
                $filters = join( ', ', filter_list() );
                return array( ezcInputForm::DEF_UNSUPPORTED_FILTER, "The filter '{$element->filterName}' for element '{$name}' does not exist. Pick one of: $filters" );
            }

            // The input field name should have a sane format
            if ( gettype( $name ) != 'string' )
            {
                return array( ezcInputForm::DEF_FIELD_NAME_BROKEN, "The element name '{$name}' is not a string" );
            }
            if (! preg_match( '@^[a-z][a-z0-9_]*$@i', $name ) )
            {
                return array( ezcInputForm::DEF_FIELD_NAME_BROKEN, "The element name '{$name}' has an unsupported format. It should start with an a-z and followed by a-z0-9_" );
            }

        }
        return true;
    }

    /**
     * This function is called when a variable is assigned to a magic property.
     *
     * When the value of a property is requested this function checks with the
     * $properties array whether it contains valid data or not. If there is no
     * valid data, the UserInputInValidData exception is thrown, otherwise the
     * function returns the value associated with the input variable.
     *
     * @throws ezcInputFormInvalidDataException when trying to read a property
     *         which has no valid data.
     * @throws ezcInputFormUnknownFieldException when a property is being
     *         accessed which is not defined in the definition array.
     *
     * @param string $propertyName
     * @return mixed The value of the input variable.
     * @ignore
     */
    public function __get( $propertyName )
    {
        if ( isset( $this->properties[$propertyName] ) )
        {
            if ( $this->properties[$propertyName] === ezcInputForm::VALID )
            {
                return $this->propertyValues[$propertyName];
            }
            else
            {
                throw new ezcInputFormNoValidDataException( $propertyName );
            }
        }
        throw new ezcInputFormUnknownFieldException( $propertyName );
    }

    /**
     * Returns whether a magic property was is used on a magic property.
     *
     * This method checks whether a magic property exists and returns true of
     * it does and false if it doesn't. The list of properties which exist is
     * determined by the $definition array that was passed to the constructor.
     *
     * @param string $propertyName
     * @return bool  Whether the $propertyName exists or not.
     * @ignore
     */
    public function __isset( $propertyName )
    {
        return isset( $this->properties[$propertyName] );
    }

    /**
     * Sets a new magic property.
     *
     * This function is called when one of the magic properties was assigned a
     * new value.  As all magic properties are read-only for this class, all
     * that this function does is return the exception
     * ezcBasePropertyReadOnlyException.
     *
     * @throws ezcBasePropertyPermissionException for every call to this
     *         function.
     * @param string $propertyName
     * @param mixed  $newValue
     * @ignore
     */
    public function __set( $propertyName, $newValue )
    {
        throw new ezcBasePropertyPermissionException( $propertyName, ezcBasePropertyPermissionException::READ );
    }

    /**
     * Returns whether the optional field $fieldName exists.
     *
     * @param string $fieldName
     * @return bool true if the input field was available and false otherwise.
     */
    public function hasInputField( $fieldName )
    {
        if ( isset( $this->properties[$fieldName] ) )
        {
            return true;
        }
        return false;
    }

    /**
     * Returns whether the filters for required field $fieldName returned valid data.
     *
     * @param string $fieldName
     * @return bool true if the input field was available and false otherwise.
     */
    public function hasValidData( $fieldName )
    {
        if ( isset( $this->properties[$fieldName] ) && $this->properties[$fieldName] === ezcInputForm::VALID )
        {
            return true;
        }
        return false;
    }

    /**
     * Returns RAW input variable values for invalid field $fieldName.
     *
     * The return value of this function can be used to prefill forms on the
     * next request. It will only work for invalid input fields, as for valid
     * input fields you should never have to get to the original RAW data. In
     * the case a $fieldName is passed that has valid data, an
     * ezcInputFormException will be thrown.
     *
     * @throws ezcInputFormValidDataException when trying to get unsafe raw
     *         data from a input field with valid data.
     * @throws ezcInputFormFieldNotFoundException when trying to get data from a
     *         property that does not exist.
     * @param string $fieldName
     * @return string The original RAW data of the specified input field.
     */
    public function getUnsafeRawData( $fieldName )
    {
        if ( isset( $this->properties[$fieldName] ) )
        {
            if ( $this->properties[$fieldName] === ezcInputForm::VALID )
            {
                throw new ezcInputFormValidDataAvailableException( $fieldName );
            }
            else
            {
                if ( filter_has_var( $this->inputSource, $fieldName ) )
                {
                    return filter_input( $this->inputSource, $fieldName, FILTER_UNSAFE_RAW );
                }
                else
                {
                    throw new ezcInputFormFieldNotFoundException( $fieldName );
                }
            }
        }
        throw new ezcInputFormUnknownFieldException( $fieldName );
    }

    /**
     * Returns a list with all optional properties.
     * @return array(string)
     */
    public function getOptionalProperties()
    {
        $properties = array();
        foreach ( $this->definition as $fieldName => $fieldDefinition )
        {
            if ( $fieldDefinition->type === ezcInputFormDefinitionElement::OPTIONAL )
            {
                $properties[] = $fieldName;
            }
        }
        return $properties;
    }

    /**
     * Returns a list with all required properties.
     * @return array(string)
     */
    public function getRequiredProperties()
    {
        $properties = array();
        foreach ( $this->definition as $fieldName => $fieldDefinition )
        {
            if ( $fieldDefinition->type === ezcInputFormDefinitionElement::REQUIRED )
            {
                $properties[] = $fieldName;
            }
        }
        return $properties;
    }

    /**
     * Returns a list with all properties that have valid data.
     * @return array(string)
     */
    public function getValidProperties()
    {
        $properties = array();
        foreach ( $this->properties as $fieldName => $fieldStatus )
        {
            if ( $fieldStatus === ezcInputForm::VALID )
            {
                $properties[] = $fieldName;
            }
        }
        return $properties;
    }

    /**
     * Returns a list with all properties having invalid data.
     * @return array(string)
     */
    public function getInvalidProperties()
    {
        $properties = array();
        foreach ( $this->properties as $fieldName => $fieldStatus )
        {
            if ( $fieldStatus === ezcInputForm::INVALID )
            {
                $properties[] = $fieldName;
            }
        }
        return $properties;
    }

    /**
     * Returns whether all the input elements were valid or not.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->allElementsValid;
    }
}
?>