<?php
/**
 * File containing the ezcDocumentOdtFormattingProperties class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class for representing formatting properties of a certain type.
 *
 * An instance of this class represents formatting properties of a certain type 
 * (indicated by a PROPERTIES_* constant). The formatting properties set inside 
 * such an object must obay to the ODF specification.
 *
 * @property-read string $type The type of the formatting properties. Set in 
 *                the constructor
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentOdtFormattingProperties extends ArrayObject
{
    /**
     * May be contained only in <style:page-layout>. 
     */
    const PROPERTIES_PAGE_LAYOUT = 'page-layout-properties';

    /**
     * May be contained in <style:header-style> and <style:footer-style>, which 
     * are sub-elements of <style:page-layout>.
     */
    const PROPERTIES_HEADER_FOOTER = 'header-footer-properties';

    /**
     * May be contained in <style:style> for families "text", "paragraph" and 
     * "cell", but might also occur in arbitrary style families (specs not 
     * clear).
     */
    const PROPERTIES_TEXT = 'text-properties';

    /**
     * May be contained in <style:style> for families "paragraph" and "cell", 
     * but might also occur in arbitrary style families (specs not clear).
     */
    const PROPERTIES_PARAGRAPH = 'paragraph-properties';

    /**
     * May be contained in <style:style> for the family "ruby".
     */
    const PROPERTIES_RUBY_TEXT = 'ruby-properties';

    /**
     * May be contained in <style:style> for the family "section".
     */
    const PROPERTIES_SECTION = 'section-properties';

    /**
     * May be contained in <style:style> for the family "table".
     */
    const PROPERTIES_TABLE = 'table-properties';

    /**
     * May be contained in <style:style> for the family "table-column".
     */
    const PROPERTIES_COLUMN = 'table-column-properties';

    /**
     * May be contained in <style:style> for the family "table-row".
     */
    const PROPERTIES_TABLE_ROW = 'table-row-properties';

    /**
     * May be contained in <style:style> for the family "table-cell".
     */
    const PROPERTIES_TABLE_CELL = 'table-cell-properties';

    /**
     * May be contained in <text:list-style> and others inside 
     * <text:list-level-style-*> elements, no matter which kind.
     */
    const PROPERTIES_LIST_LEVEL = 'list-level-properties';

    /**
     * May be contained in <style:style> for the families "graphic" and "presentation".
     *
     * Note: Most graphic properties are only used to define inline graphics, 
     * created directly in the office document (e.g. presentations) and 
     * therefore not supported. Supported are, e.g., graphic properties that 
     * apply to a frame.
     */
    const PROPERTIES_GRAPHIC = 'graphic-properties';

    /**
     * May be contained in <style:style> for the family "chart".
     *
     * Note: These properties are not supported!
     */
    const PROPERTIES_CHART = 'chart-properties';

    /**
     * Properties.
     *
     * @var array(string=>mixed)
     */
    protected $properties = array();

    /**
     * Creates a new property object of $type.
     *
     * $type must be one of the FAMILY_* constants.
     * 
     * @param const $type 
     */
    public function __construct( $type )
    {
        $this->properties['type'] = $type;
        parent::__construct( array(), ArrayObject::STD_PROP_LIST );
    }

    /**
     * Appending a new value is not allowed.
     *
     * Only {@link offsetSet()} is allowed, using a valid property type.
     * 
     * @param mixed $value 
     * @return void
     */
    public function append( $value )
    {
        throw new RuntimeException(
            'Cannot append values to this object. Must provide a property type as the key.'
        );
    }

    /**
     * Exchanging the array is not allowed.
     * 
     * @param array $array 
     * @return void
     */
    public function exchangeArray( $array )
    {
        throw new RuntimeException( 'Exchanging of array not allowed.' );
    }

    /**
     * Sets a formatting property.
     *
     * The $offset is the name of the formatting property, the $value the 
     * value to be assigned (usually string, but might be of different type).
     * 
     * @param string $offset 
     * @param mixed $value 
     * @return void
     */
    public function offsetSet( $offset, $value )
    {
        if ( !is_string( $offset ) )
        {
            throw new ezcBaseValueException(
                'offset',
                $offset,
                'string'
            );
        }
        parent::offsetSet( $offset, $value );
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
            case 'type':
                throw new ezcBasePropertyPermissionException( $name, ezcBasePropertyPermissionException::READ );
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
