<?php
/**
 * File containing the ezcDocumentOdtStyle class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Class for ODT styles.
 *
 * Note that list styles are represented by the dedicated {@link 
 * ezcDocumentOdtListStyle} class.
 *
 * @property-read string $name The style name.
 * @property-read constant $family The style family.
 * @property ezcDocumentOdtFormattingPropertyCollection $formattingProperties
 *           Formatting properties valid for this style.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentOdtStyle
{
    /**
     * Table column style. 
     */
    const FAMILY_COLUMN       = 'column';

    /**
     * Graphic style. Only supported for graphic frames.
     */
    const FAMILY_GRAPHIC      = 'graphic';

    /**
     * Paragraph style.
     */
    const FAMILY_PARAGRAPH    = 'paragraph';

    /**
     * Section style.
     */
    const FAMILY_SECTION      = 'section';

    /**
     * Table cell style.
     */
    const FAMILY_TABLE_CELL   = 'table-cell';

    /**
     * Table column style. 
     */
    const FAMILY_TABLE_COLUMN = 'table-column';
    
    /**
     * Table row style. 
     */
    const FAMILY_TABLE_ROW    = 'table-row';
    
    /**
     * Tabke style. 
     */
    const FAMILY_TABLE        = 'table';

    /**
     * General text style 
     */
    const FAMILY_TEXT         = 'text';

    /**
     * Char style. Not supported 
     */
    const FAMILY_CHART        = 'chart';

    /**
     * Form control style. Not supported. 
     */
    const FAMILY_CONTROL      = 'control';

    /**
     * Style for a drawing page. Not supported. 
     */
    const FAMILY_DRAWING_PAGE = 'drawing-page';

    /**
     * Presentation style. Not supported. 
     */
    const FAMILY_PRESENTATION = 'presentation';

    /**
     * Ruby style.
     *
     * @todo: Do we need to support this? 
     */
    const FAMILY_RUBY         = 'ruby';

    /**
     * Table page style. Only for spreadsheets, therefore not supported.
     */
    const FAMILY_TABLE_PAGE   = 'table-page';

    /**
     * Properties
     * 
     * @var array(string=>mixed)
     */
    protected $properties = array(
        'name'                 => null,
        'family'               => null,
        'formattingProperties' => null,
    );

    /**
     * Creates a new style.
     *
     * Creates a style in the given style $family with the given $name. $family 
     * must be one of the FAMILY_* constants. $name can be an arbitrary string. 
     * Note that $name and $family properties can not be changed at a later 
     * time.
     * 
     * @param const $family 
     * @param string $name 
     */
    public function __construct( $family, $name )
    {
        $this->properties['family'] = $family;
        $this->properties['name']   = $name;
        $this->formattingProperties = new ezcDocumentOdtFormattingPropertyCollection();
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'name':
            case 'family':
                throw new ezcBasePropertyPermissionException( $name, ezcBasePropertyPermissionException::READ );
            case 'formattingProperties':
                if ( !is_object( $value ) || !( $value instanceof ezcDocumentOdtFormattingPropertyCollection ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'ezcDocumentOdtFormattingPropertyCollection' );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
        $this->properties[$name] = $value;
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @ignore
     */
    public function __get( $name )
    {
        if ( $this->__isset( $name ) )
        {
            return $this->properties[$name];
        }
        throw new ezcBasePropertyNotFoundException( $name );
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
        return array_key_exists( $name, $this->properties );
    }
}

?>
