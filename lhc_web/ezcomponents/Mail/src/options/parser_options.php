<?php
/**
 * File containing the ezcMailParserOption class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing the basic options for the mail parser.
 *
 * Example of how to use the parser options:
 * <code>
 * $options = new ezcMailParserOptions();
 * $options->mailClass = 'myCustomMailClass'; // extends ezcMail
 * $options->fileClass = 'myCustomFileClass'; // extends ezcMailFile
 * $options->parseTextAttachmentsAsFiles = true; // to get the text attachments in ezcMailFile objects
 *
 * $parser = new ezcMailParser( $options );
 * </code>
 *
 * Another way to specify the options is:
 * <code>
 * $parser = new ezcMailParser();
 * $parser->options->mailClass = 'myCustomMailClass'; // extends ezcMail
 * $parser->options->fileClass = 'myCustomFileClass'; // extends ezcMailFile
 * $parser->options->parseTextAttachmentsAsFiles = true;
 * </code>
 *
 * @property string $mailClass
 *           Specifies a class descending from ezcMail which can be returned by the
 *           parser if you plan to use another class instead of ezcMail. The default
 *           value is ezcMail.
 * @property string $fileClass
 *           Specifies a class descending from ezcMailFile which can be instanciated
 *           by the parser to handle file attachments. The default value is
 *           ezcMailFile.
 * @property string $parseTextAttachmentsAsFiles
 *           Specifies whether to parse the text attachments in an ezcMailTextPart
 *           (default) or in an ezcMailFile (by setting the option to true).
 * @package Mail
 * @version 1.7.1
 */
class ezcMailParserOptions extends ezcBaseOptions
{
    /**
     * Constructs an object with the specified values.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if $options contains a property not defined
     * @throws ezcBaseValueException
     *         if $options contains a property with a value not allowed
     * @param array(string=>mixed) $options
     */
    public function __construct( array $options = array() )
    {
        $this->mailClass = 'ezcMail'; // default value for mail class is 'ezcMail'
        $this->fileClass = 'ezcMailFile'; // default value for file attachment class is 'ezcMailFile'
        $this->parseTextAttachmentsAsFiles = false; // default is to parse text attachments in ezcMailTextPart objects

        parent::__construct( $options );
    }

    /**
     * Sets the option $propertyName to $propertyValue.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $propertyName is not defined
     * @throws ezcBaseValueException
     *         if $propertyValue is not correct for the property $propertyName
     * @throws ezcBaseInvalidParentClassException
     *         if the class name passed as replacement mailClass does not
     *         inherit from ezcMail.
     * @throws ezcBaseInvalidParentClassException
     *         if the class name passed as replacement fileClass does not
     *         inherit from ezcMailFile.
     * @param string $propertyName
     * @param mixed  $propertyValue
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'mailClass':
                if ( !is_string( $propertyValue ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'string that contains a class name' );
                }

                // Check if the passed classname actually implements the
                // correct parent class.
                if ( 'ezcMail' !== $propertyValue && !in_array( 'ezcMail', class_parents( $propertyValue ) ) )
                {
                    throw new ezcBaseInvalidParentClassException( 'ezcMail', $propertyValue );
                }
                $this->properties[$propertyName] = $propertyValue;
                break;

            case 'fileClass':
                if ( !is_string( $propertyValue ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'string that contains a class name' );
                }

                // Check if the passed classname actually implements the
                // correct parent class.
                if ( 'ezcMailFile' !== $propertyValue && !in_array( 'ezcMailFile', class_parents( $propertyValue ) ) )
                {
                    throw new ezcBaseInvalidParentClassException( 'ezcMailFile', $propertyValue );
                }
                $this->properties[$propertyName] = $propertyValue;
                ezcMailFileParser::$fileClass = $propertyValue;
                break;

            case 'parseTextAttachmentsAsFiles':
                if ( !is_bool( $propertyValue ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'bool' );
                }
                $this->properties[$propertyName] = $propertyValue;
                ezcMailPartParser::$parseTextAttachmentsAsFiles = $propertyValue;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
    }
}
?>
