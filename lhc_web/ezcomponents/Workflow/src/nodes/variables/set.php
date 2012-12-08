<?php
/**
 * File containing the ezcWorkflowNodeVariableSet class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * An object of the ezcWorkflowNodeVariableSet class sets the specified workflow variable to
 * a given value.
 *
 * <code>
 * <?php
 * $set = new ezcWorkflowNodeVariableSet( array( 'variable name' = > $value ) );
 * ?>
 * </code>
 *
 * Incoming nodes: 1
 * Outgoing nodes: 1
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowNodeVariableSet extends ezcWorkflowNode
{
    /**
     * Constructs a new variable set node with the configuration $configuration.
     *
     * The configuration is an array of keys and values of the format:
     * array( 'workflow variable name' => value )
     *
     * @param mixed $configuration
     * @throws ezcBaseValueException
     */
    public function __construct( $configuration = '' )
    {
        if ( !is_array( $configuration ) )
        {
            throw new ezcBaseValueException(
              'configuration', $configuration, 'array'
            );
        }

        parent::__construct( $configuration );
    }

    /**
     * Executes this by setting all the variables specified by the
     * configuration.
     *
     * @param ezcWorkflowExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute( ezcWorkflowExecution $execution )
    {
        foreach ( $this->configuration as $variable => $value )
        {
            $execution->setVariable( $variable, $value );
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
            $configuration[$variable->getAttribute( 'name' )] = ezcWorkflowDefinitionStorageXml::xmlToVariable(
              ezcWorkflowUtil::getChildNode( $variable )
            );
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
        foreach ( $this->configuration as $variable => $value )
        {
            $variableXml = $element->appendChild(
              $element->ownerDocument->createElement( 'variable' )
            );

            $variableXml->setAttribute( 'name', $variable );

            $variableXml->appendChild(
              ezcWorkflowDefinitionStorageXml::variableToXml(
                $value, $element->ownerDocument
              )
            );
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
        $buffer = array();

        foreach ( $this->configuration as $variable => $value )
        {
            $buffer[] = $variable . ' = ' . ezcWorkflowUtil::variableToString( $value );
        }

        return implode( ', ', $buffer );
    }
}
?>
