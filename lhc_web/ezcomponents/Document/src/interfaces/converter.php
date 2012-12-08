<?php
/**
 * File containing the abstract ezcDocumentConverter base class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * A base class for document type converters.
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocumentConverter implements ezcDocumentErrorReporting
{
    /**
     * XML document base options.
     *
     * @var ezcDocumentXmlOptions
     */
    protected $options;

    /**
     * Additional parser properties.
     *
     * @var array
     */
    protected $properties = array(
        'errors' => array(),
    );

    /**
     * Construct new document
     *
     * @param ezcDocumentConverterOptions $options
     */
    public function __construct( ezcDocumentConverterOptions $options = null )
    {
        $this->options = ( $options === null ?
            new ezcDocumentConverterOptions() :
            $options );
    }

    /**
     * Convert documents between two formats
     *
     * Convert documents of the given type to the requested type.
     *
     * @param ezcDocument $doc
     * @return ezcDocument
     */
    abstract public function convert( $doc );

    /**
     * Trigger parser error
     *
     * Emit a parser error and handle it dependiing on the current error
     * reporting settings.
     *
     * @param int $level
     * @param string $message
     * @param string $file
     * @param int $line
     * @param int $position
     * @return void
     */
    public function triggerError( $level, $message, $file = null, $line = null, $position = null )
    {
        if ( $level & $this->options->errorReporting )
        {
            throw new ezcDocumentConversionException( $level, $message, $file, $line, $position );
        }

        // For lower error level settings, just aggregate errors
        $this->properties['errors'][] = new ezcDocumentParserException( $level, $message, $file, $line, $position );
    }

    /**
     * Return list of errors occured during visiting the document.
     *
     * May be an empty array, if on errors occured, or a list of
     * ezcDocumentVisitException objects.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->properties['errors'];
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     * @param string $name
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'options':
                return $this->options;
            case 'errors':
                return $this->properties['errors'];
        }

        throw new ezcBasePropertyNotFoundException( $name );
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     * @throws ezcBaseValueException
     *         if $value is not accepted for the property $name
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'options':
                if ( !( $value instanceof ezcDocumentConverterOptions ) )
                {
                    throw new ezcBaseValueException( 'options', $value, 'instanceof ezcDocumentConverterOptions' );
                }

                $this->options = $value;
                break;

            case 'errors':
                throw new ezcBasePropertyPermissionException( $name, ezcBasePropertyPermissionException::READ );

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns true if the property $name is set, otherwise false.
     *
     * @param string $name
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        switch ( $name )
        {
            case 'options':
                return true;

            default:
                return false;
        }
    }
}

?>
