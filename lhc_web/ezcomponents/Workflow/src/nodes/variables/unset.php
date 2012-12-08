<?php
/**
 * File containing the ezcWorkflowNodeVariableUnset class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * An object of the ezcWorkflowNodeVariableUnset class unset the specified workflow variable.
 *
 * <code>
 * <?php
 * $unset = new ezcWorkflowNodeVariableUnset( 'variable name' );
 * ?>
 * </code>
 *
 * Incoming nodes: 1
 * Outgoing nodes: 1
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowNodeVariableUnset extends ezcWorkflowNode
{
    /**
     * Constructs a new unset node.
     *
     * Configuration format:
     * String:
     *    The name of the workflow variable to unset.
     *
     * Array:
     *    An array of names of the workflow variables to unset.
     *
     * @param mixed $configuration
     * @throws ezcBaseValueException
     */
    public function __construct( $configuration = '' )
    {
        if ( is_string( $configuration ) )
        {
            $configuration = array( $configuration );
        }

        if ( !is_array( $configuration ) )
        {
            throw new ezcBaseValueException(
              'configuration', $configuration, 'array'
            );
        }

        parent::__construct( $configuration );
    }

    /**
     * Executes this node.
     *
     * @param ezcWorkflowExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute( ezcWorkflowExecution $execution )
    {
        foreach ( $this->configuration as $variable )
        {
            $execution->unsetVariable( $variable );
        }

        $this->activateNode( $execution, $this->outNodes[0] );

        return parent::execute( $execution );
    }

    /**
     * Generate node configuration from XML representation.
     *
     * @param DOMElement $element
     * @return array
     * @ignore
     */
    public static function configurationFromXML( DOMElement $element )
    {
        $configuration = array();

        foreach ( $element->getElementsByTagName( 'variable' ) as $variable )
        {
            $configuration[] = $variable->getAttribute( 'name' );
        }

        return $configuration;
    }

    /**
     * Generate XML representation of this node's configuration.
     *
     * @param DOMElement $element
     * @ignore
     */
    public function configurationToXML( DOMElement $element )
    {
        foreach ( $this->configuration as $variable )
        {
            $variableXml = $element->appendChild(
              $element->ownerDocument->createElement( 'variable' )
            );

            $variableXml->setAttribute( 'name', $variable );
        }
    }

    /**
     * Returns a textual representation of this node.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return 'unset(' . implode( ', ', $this->configuration ) . ')';
    }
}
?>
