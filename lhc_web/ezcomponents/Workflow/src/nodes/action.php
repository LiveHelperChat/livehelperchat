<?php
/**
 * File containing the ezcWorkflowNodeAction class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * An object of the ezcWorkflowNodeAction class represents an activity node holding business logic.
 *
 * When the node is reached during execution of the workflow, the business logic that is implemented
 * by the associated service object is executed.
 *
 * Service objects can return true to resume execution of the
 * workflow or false to suspend the workflow (unless there are other active nodes)
 * and be re-executed later
 *
 * Incoming nodes: 1
 * Outgoing nodes: 1
 *
 * The following example displays how to create a workflow with a very
 * simple service object that prints the argument it was given to the
 * constructor:
 *
 * <code>
 * <?php
 * class MyPrintAction implements ezcWorkflowServiceObject
 * {
 *     private $whatToSay;
 *
 *     public function __construct( $whatToSay )
 *     {
 *         $this->whatToSay = $whatToSay;
 *     }
 *
 *     public function execute( ezcWorkflowExecution $execution )
 *     {
 *         print $this->whatToSay;
 *         return true; // we're finished, activate next node
 *     }
 *
 *     public function __toString()
 *     {
 *         return 'action description';
 *     }
 * }
 *
 * $workflow = new ezcWorkflow( 'Test' );
 *
 * $action = new ezcWorkflowNodeAction( array( "class" => "MyPrintAction",
 *                                             "arguments" => "No. 1 The larch!" ) );
 * $action->addOutNode( $workflow->endNode );
 * $workflow->startNode->addOutNode( $action );
 * ?>
 * </code>
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowNodeAction extends ezcWorkflowNode
{
    /**
     * Constructs a new action node with the configuration $configuration.
     *
     * Configuration format
     * <ul>
     * <li>
     *   <b>String:</b>
     *   The class name of the service object. Must implement ezcWorkflowServiceObject. No
     *   arguments are passed to the constructor.
     * </li>
     *
     * <li>
     *   <b>Array:</b>
     *   <ul>
     *     <li><i>class:</i> The class name of the service object. Must implement ezcWorkflowServiceObject.</li>
     *     <li><i>arguments:</i> Array of values that are passed to the constructor of the service object.</li>
     *   </ul>
     * <li>
     * </ul>
     *
     * @param mixed $configuration
     * @throws ezcWorkflowDefinitionStorageException
     */
    public function __construct( $configuration )
    {
        if ( is_string( $configuration ) )
        {
            $configuration = array( 'class' => $configuration );
        }

        if ( !isset( $configuration['arguments'] ) )
        {
            $configuration['arguments'] = array();
        }

        parent::__construct( $configuration );
    }

    /**
     * Executes this node by creating the service object and calling its execute() method.
     *
     * If the service object returns true, the output node will be activated.
     * If the service node returns false the workflow will be suspended
     * unless there are other activated nodes. An action node suspended this way
     * will be executed again the next time the workflow is resumed.
     *
     * @param ezcWorkflowExecution $execution
     * @return boolean true when the node finished execution,
     *                 and false otherwise
     * @ignore
     */
    public function execute( ezcWorkflowExecution $execution )
    {
        $object   = $this->createObject();
        $finished = $object->execute( $execution );

        // Execution of the Service Object has finished.
        if ( $finished !== false )
        {
            $this->activateNode( $execution, $this->outNodes[0] );

            return parent::execute( $execution );
        }
        // Execution of the Service Object has not finished.
        else
        {
            return false;
        }
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
        $configuration = array(
          'class'     => $element->getAttribute( 'serviceObjectClass' ),
          'arguments' => array()
        );

        $childNode = ezcWorkflowUtil::getChildNode( $element );

        if ( $childNode->tagName == 'arguments' )
        {
            foreach ( ezcWorkflowUtil::getChildNodes( $childNode ) as $argument )
            {
                $configuration['arguments'][] = ezcWorkflowDefinitionStorageXml::xmlToVariable( $argument );
            }
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
        $element->setAttribute( 'serviceObjectClass', $this->configuration['class'] );

        if ( !empty( $this->configuration['arguments'] ) )
        {
            $xmlArguments = $element->appendChild(
              $element->ownerDocument->createElement( 'arguments' )
            );

            foreach ( $this->configuration['arguments'] as $argument )
            {
                $xmlArguments->appendChild(
                  ezcWorkflowDefinitionStorageXml::variableToXml(
                    $argument, $element->ownerDocument
                  )
                );
            }
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
        try
        {
            $buffer = (string)$this->createObject();
        }
        catch ( ezcBaseAutoloadException $e )
        {
            return 'Class not found.';
        }
        catch ( ezcWorkflowExecutionException $e )
        {
            return $e->getMessage();
        }

        return $buffer;
    }

    /**
     * Returns the service object as specified by the configuration.
     *
     * @return ezcWorkflowServiceObject
     */
    protected function createObject()
    {
        if ( !class_exists( $this->configuration['class'] ) )
        {
            throw new ezcWorkflowExecutionException(
              sprintf(
                'Class "%s" not found.',
                $this->configuration['class']
              )
            );
        }

        $class = new ReflectionClass( $this->configuration['class'] );

        if ( !$class->implementsInterface( 'ezcWorkflowServiceObject' ) )
        {
            throw new ezcWorkflowExecutionException(
              sprintf(
                'Class "%s" does not implement the ezcWorkflowServiceObject interface.',
                $this->configuration['class']
              )
            );
        }

        if ( !empty( $this->configuration['arguments'] ) )
        {
            return $class->newInstanceArgs( $this->configuration['arguments'] );
        }
        else
        {
            return $class->newInstance();
        }
    }
}
?>
