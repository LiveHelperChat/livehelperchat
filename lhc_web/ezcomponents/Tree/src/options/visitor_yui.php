<?php
/**
 * File containing the ezcTreeVisitorYUIOptions class.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.4
 * @filesource
 * @package Tree
 */

/**
 * Class containing the options for the ezcTreeVisitorYUIOptions class.
 *
 * @property string $basePath
 *           Which string to prefix the href-targets with.
 * @property bool $displayRootNode
 *           Whether the root node should be displayed. The root node will
 *           still be disabled from the links that the visitor create when
 *           $selectedNodeLink is set to true.
 * @property array(string) $highlightNodeIds
 *           Which IDs should have the 'highlight' CSS class added.
 * @property bool $selectedNodeLink
 *           If enabled, only the requested node is shown in links, and not the full path.
 *
 * @package Tree
 * @version 1.1.4
 */
class ezcTreeVisitorYUIOptions extends ezcBaseOptions
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
        $this->basePath = '';
        $this->displayRootNode = false;
        $this->highlightNodeIds = array();
        $this->selectedNodeLink = false;

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
            case 'basePath':
                if ( !is_string( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'string' );
                }
                $this->properties[$name] = $value;
                break;

            case 'displayRootNode':
            case 'selectedNodeLink':
                if ( !is_bool( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'bool' );
                }
                $this->properties[$name] = $value;
                break;

            case 'highlightNodeIds':
                if ( !is_array( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'array(string)' );
                }
                $this->properties[$name] = $value;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }
}
?>
