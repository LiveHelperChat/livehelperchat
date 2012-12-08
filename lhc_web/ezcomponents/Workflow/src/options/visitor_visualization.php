<?php
/**
 * This file contains the ezcWorkflowVisitorVisualizationOptions class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Options class for ezcWorkflowVisitorVisualization.
 *
 * @property string $colorHighlighted
 *           The color for highlighted nodes.
 * @property string $colorNormal
 *           The normal color for nodes.
 * @property array  $highlightedNodes
 *           The array of nodes that are to be highlighted.
 * @property array  $workflowVariables
 *           The workflow variables that are to be displayed.
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowVisitorVisualizationOptions extends ezcBaseOptions
{
    /**
     * Properties.
     *
     * @var array(string=>mixed)
     */
    protected $properties = array(
        'colorHighlighted'  => '#cc0000',
        'colorNormal'       => '#2e3436',
        'highlightedNodes'  => array(),
        'workflowVariables' => array(),
    );

    /**
     * Property write access.
     *
     * @param string $propertyName  Name of the property.
     * @param mixed  $propertyValue The value for the property.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the the desired property is not found.
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'colorHighlighted':
            case 'colorNormal':
                if ( !is_string( $propertyValue ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'string'
                    );
                }
                break;
            case 'highlightedNodes':
            case 'workflowVariables':
                if ( !is_array( $propertyValue ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'array'
                    );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
        $this->properties[$propertyName] = $propertyValue;
    }
}
?>
