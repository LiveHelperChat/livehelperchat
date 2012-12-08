<?php
/**
 * File containing the options class for the ezcDocumentPdfFooterOptions class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing the basic options for the ezcDocumentDocbook class
 *
 * @property string $height
 *           Height of the footer, using the common measures, default: 15mm
 * @property bool $footer
 *           Set true to be rendered as a footer, and false to be
 *           rendered as header. Default: true.
 * @property bool $showDocumentTitle
 *           Display the document title in the footer, default true
 * @property bool $showDocumentAuthor
 *           Display the document author in the footer, default true
 * @property bool $showPageNumber
 *           Display the page number in the footer, default true
 * @property int $pageNumberOffset
 *           Offset for page numbers, default 0
 * @property bool $centerPageNumber
 *           Render page number in the center, by default they are
 *           rendered at the outer side of the page.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentPdfFooterOptions extends ezcDocumentOptions
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
        $this->height             = '15mm';
        $this->footer             = true;
        $this->showDocumentTitle  = true;
        $this->showDocumentAuthor = true;
        $this->showPageNumber     = true;
        $this->pageNumberOffset   = 0;
        $this->centerPageNumber   = false;

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
            case 'footer':
            case 'showDocumentTitle':
            case 'showDocumentAuthor':
            case 'showPageNumber':
            case 'centerPageNumber':
                if ( !is_bool( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'boolean' );
                }

                $this->properties[$name] = $value;
                break;

            case 'height':
                $this->properties[$name] = ezcDocumentPcssMeasure::create( $value );
                break;

            case 'pageNumberOffset':
                if ( !is_int( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'int' );
                }

                $this->properties[$name] = $value;
                break;

            default:
                parent::__set( $name, $value );
        }
    }
}

?>
