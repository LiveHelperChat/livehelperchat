<?php
/**
 * File containing the ezcConsoleTableOptions class.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Struct class to store the options of the ezcConsoleTable class.
 * This class stores the options for the {@link ezcConsoleTable} class.
 *
 * @property mixed $colWidth
 *           Either 'auto' for automatic selection of column widths, or an
 *           array of column widths like array( 10, 30, 10 ).
 * @property int $colWrap
 *           Wrap style of text contained in strings. See {@link
 *           ezcConsoleTable::WRAP_AUTO}, {@link ezcConsoleTable::WRAP_NONE}
 *           and {@link ezcConsoleTable::WRAP_CUT}.
 * @property int $defaultAlign
 *           Standard column alignment, applied to cells that have to explicit
 *           alignment assigned. See {@link ezcConsoleTable::ALIGN_LEFT},
 *           {@link ezcConsoleTable::ALIGN_RIGHT}, {@link
 *           ezcConsoleTable::ALIGN_CENTER} and {@link
 *           ezcConsoleTable::ALIGN_DEFAULT}.
 * @property string $colPadding
 *           Padding characters for side padding between data and lines.
 * @property int $widthType
 *           Type of the given table width (fixed or maximal value).
 * @property string $lineVertical
 *           Character to use for drawing vertical lines. Null to switch off.
 * @property string $lineHorizontal
 *           Character to use for drawing horizontal lines. Null to switch off.
 * @property string $corner
 *           Character to use for drawing line corners. Null to switch off.
 * @property string $defaultFormat
 *           Standard column content format, applied to cells that have
 *           "default" as the content format.
 * @property string $defaultBorderFormat
 *           Standard border format, applied to rows that have 'default' as the
 *           border format.
 * 
 * @package ConsoleTools
 * @version 1.6.1
 */
class ezcConsoleTableOptions extends ezcBaseOptions
{
    protected $properties = array(
        'colWidth'            => 'auto',
        'colWrap'             => ezcConsoleTable::WRAP_AUTO,
        'defaultAlign'        => ezcConsoleTable::ALIGN_LEFT,
        'colPadding'          => ' ',
        'widthType'           => ezcConsoleTable::WIDTH_MAX,
        'lineVertical'        => '-',
        'lineHorizontal'      => '|',
        'corner'              => '+',
        'defaultFormat'       => 'default',
        'defaultBorderFormat' => 'default',
    );

    /**
     * Construct a new options object.
     *
     * NOTE: For backwards compatibility reasons the old method of instantiating this class is kept,
     * but the usage of the new version (providing an option array) is highly encouraged.
     * 
     * @param array(string=>mixed) $options The initial options to set.
     * @return void
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the value for the property options is not an instance of
     * @throws ezcBaseValueException
     *         If the value for a property is out of range.
     */
    public function __construct()
    {
        $args = func_get_args();
        if ( func_num_args() === 1 && is_array( $args[0] ) && !is_int( key( $args[0] ) ) )
        {
            parent::__construct( $args[0] );
        }
        else
        {
            foreach ( $args as $id => $val )
            {
                switch ( $id )
                {
                    case 0:
                        $this->__set( 'colWidth', $val );
                        break;
                    case 1:
                        $this->__set( 'colWrap', $val );
                        break;
                    case 2:
                        $this->__set( 'defaultAlign', $val );
                        break;
                    case 3:
                        $this->__set( 'colPadding', $val );
                        break;
                    case 4:
                        $this->__set( 'widthType', $val );
                        break;
                    case 5:
                        $this->__set( 'lineVertical', $val );
                        break;
                    case 6:
                        $this->__set( 'lineHorizontal', $val );
                        break;
                    case 7:
                        $this->__set( 'corner', $val );
                        break;
                    case 8:
                        $this->__set( 'defaultFormat', $val );
                        break;
                    case 9:
                        $this->__set( 'defaultBorderFormat', $val );
                        break;
                }
            }
        }
    }

    /**
     * Property write access.
     * 
     * @throws ezcBasePropertyNotFoundException
     *         If a desired property could not be found.
     * @throws ezcBaseValueException
     *         If a desired property value is out of range.
     *
     * @param string $propertyName Name of the property.
     * @param mixed $val  The value for the property.
     * @ignore
     */
    public function __set( $propertyName, $val )
    {
        switch ( $propertyName )
        {
            case 'colWidth':
                if ( is_array( $val ) === false && ( is_string( $val ) && $val !== 'auto' ) )
                {
                    throw new ezcBaseValueException( $propertyName, $val, 'array(int) or "auto"' );
                }
                break;
            case 'colWrap':
                if ( $val !== ezcConsoleTable::WRAP_AUTO && $val !== ezcConsoleTable::WRAP_NONE && $val !== ezcConsoleTable::WRAP_CUT )
                {
                    throw new ezcBaseValueException( $propertyName, $val, 'ezcConsoleTable::WRAP_AUTO, ezcConsoleTable::WRAP_NONE, ezcConsoleTable::WRAP_CUT' );
                }
                break;
            case 'defaultAlign':
                if ( $val !== ezcConsoleTable::ALIGN_DEFAULT && $val !== ezcConsoleTable::ALIGN_LEFT && $val !== ezcConsoleTable::ALIGN_CENTER && $val !== ezcConsoleTable::ALIGN_RIGHT )
                {
                    throw new ezcBaseValueException( $propertyName, $val, 'ezcConsoleTable::ALIGN_DEFAULT, ezcConsoleTable::ALIGN_LEFT, ezcConsoleTable::ALIGN_CENTER, ezcConsoleTable::ALIGN_RIGHT' );
                }
                break;
            case 'colPadding':
                if ( !is_string( $val ) )
                {
                    throw new ezcBaseValueException( $propertyName, $val, 'string' );
                }
                break;
            case 'widthType':
                if ( $val !== ezcConsoleTable::WIDTH_MAX && $val !== ezcConsoleTable::WIDTH_FIXED )
                {
                    throw new ezcBaseValueException( $propertyName, $val, 'ezcConsoleTable::WIDTH_MAX, ezcConsoleTable::WIDTH_FIXED' );
                }
                break;
            case 'lineVertical':
            case 'lineHorizontal':
            case 'corner':
                if ( ( is_string( $val ) === false || strlen( $val ) !== 1 ) && $val !== null )
                {
                    throw new ezcBaseValueException( $propertyName, $val, 'string, length = 1, or null' );
                }
                break;
            case 'defaultFormat':
                if ( is_string( $val ) === false || strlen( $val ) < 1 )
                {
                    throw new ezcBaseValueException( $propertyName, $val, 'string, length = 1' );
                }
                break;
            case 'defaultBorderFormat':
                if ( is_string( $val ) === false || strlen( $val ) < 1 )
                {
                    throw new ezcBaseValueException( $propertyName, $val, 'string, length = 1' );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
        $this->properties[$propertyName] = $val;
    }
}

?>
