<?php
/**
 * File containing the options class for the ezcDocumentPdfOptions class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing the basic options for the ezcDocumentDocbook class.
 *
 * @property ezcDocumentPdfHyphenator $hyphenator
 *           Hyphenator to use for word hyphenation
 * @property ezcDocumentPdfTokenizer $tokenizer
 *           Tokenizer used to split strings into single words
 * @property ezcDocumentPdfDriver $driver
 *           Driver used to generate the actual PDF
 * @property ezcDocumentPdfTableColumnWidthCalculator $tableColumnWidthCalculator
 *           Class responsible to obtain sensible column width values from a 
 *           table specification by introspecting its contents.
 * @property bool $compress
 *           Indicates whether to compress the generated PDF.
 * @property int $permissions
 *           User permissions for the document. Defaults to all permissions. 
 *           May be any combination of the following flags: 
 *           ezcDocumentPdfOptions::EDIT Edit annotations and form fields
 *           ezcDocumentPdfOptions::PRINTABLE User may print the document
 *           ezcDocumentPdfOptions::COPY User may copy contents
 *           ezcDocumentPdfOptions::MODIFY User may edit the contents
 * @property string $ownerPassword
 *           Password, which will be required to chnage the permissions of the 
 *           PDF document.
 * @property string $userPassword
 *           Password, which will be required to access the PDF document.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentPdfOptions extends ezcDocumentOptions
{
    /**
     * User may edit annotations and form field in the PDF
     */
    const EDIT = 1;

    /**
     * User may print the PDF document
     */
    const PRINTABLE = 2;

    /**
     * User may copy contents from the PDF document
     */
    const COPY = 4;

    /**
     * User may modify the contents of the PDF document
     */
    const MODIFY = 8;

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
        $this->hyphenator                 = new ezcDocumentPdfDefaultHyphenator();
        $this->tokenizer                  = new ezcDocumentPdfDefaultTokenizer();
        $this->tableColumnWidthCalculator = new ezcDocumentPdfDefaultTableColumnWidthCalculator();
        $this->compress                   = false;
        $this->permissions                = -1;
        $this->ownerPassword              = null;
        $this->userPassword               = null;

        $this->properties['driver']       = null;

        parent::__construct( $options );
    }

    /**
     * Sets the option $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name is not defined
     * @throws ezcBaseValueException
     *         if $value is not correct for the property $name
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'hyphenator':
                if ( !$value instanceof ezcDocumentPdfHyphenator )
                {
                    throw new ezcBaseValueException( $name, $value, 'instanceof ezcDocumentPdfHyphenator' );
                }

                $this->properties[$name] = $value;
                break;

            case 'tokenizer':
                if ( !$value instanceof ezcDocumentPdfTokenizer )
                {
                    throw new ezcBaseValueException( $name, $value, 'instanceof ezcDocumentPdfTokenizer' );
                }

                $this->properties[$name] = $value;
                break;

            case 'driver':
                if ( !$value instanceof ezcDocumentPdfDriver )
                {
                    throw new ezcBaseValueException( $name, $value, 'instanceof ezcDocumentPdfDriver' );
                }

                $this->properties[$name] = $value;
                break;

            case 'tableColumnWidthCalculator':
                if ( !$value instanceof ezcDocumentPdfTableColumnWidthCalculator )
                {
                    throw new ezcBaseValueException( $name, $value, 'instanceof ezcDocumentPdfTableColumnWidthCalculator' );
                }

                $this->properties[$name] = $value;
                break;

            case 'compress':
                if ( !is_bool( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'bool' );
                }

                $this->properties[$name] = $value;
                break;

            case 'ownerPassword':
            case 'userPassword':
                if ( !is_string( $value ) && !is_null( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'string OR null' );
                }

                if ( ( $name === 'userPassword' ) &&
                     ( $value !== null ) &&
                     ( $this->properties['ownerPassword'] === null ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'ownerPassword must be specified, before a userPassword can be set.' );
                }

                $this->properties[$name] = $value;
                break;

            case 'permissions':
                if ( !is_int( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'Vitwise combination of ezcDocumentPdfOptions::EDIT, ezcDocumentPdfOptions::PRINTABLE, ezcDocumentPdfOptions::MODIFY, ezcDocumentPdfOptions::COPY' );
                }

                $this->properties[$name] = (int) $value;
                break;

            default:
                parent::__set( $name, $value );
        }
    }
}

?>
