<?php
/**
 * File containing the ezcWorkflowExecutionVisualizerPlugin class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Execution plugin that visualizes the execution.
 *
 * <code>
 * <?php
 * $db         = ezcDbFactory::create( 'mysql://test@localhost/test' );
 * $definition = new ezcWorkflowDatabaseDefinitionStorage( $db );
 * $workflow   = $definition->loadByName( 'Test' );
 * $execution  = new ezcWorkflowDatabaseExecution( $db );
 *
 * $execution->workflow = $workflow;
 * $execution->addPlugin( new ezcWorkflowExecutionVisualizerPlugin( '/tmp' ) );
 * $execution->start();
 * ?>
 * </code>
 *
 * @property ezcWorkflowExecutionVisualizerPluginOptions $options
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowExecutionVisualizerPlugin extends ezcWorkflowExecutionPlugin
{
    /**
     * Filename counter.
     *
     * @var integer
     */
    protected $fileCounter = 0;

    /**
     * Properties.
     *
     * @var array(string=>mixed)
     */
    protected $properties = array();

    /**
     * Constructor.
     *
     * @param string $directory The directory to which the DOT files are written.
     */
    public function __construct( $directory )
    {
        $this->options = new ezcWorkflowExecutionVisualizerPluginOptions;
        $this->options['directory'] = $directory;
    }

    /**
     * Property get access.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the given property could not be found.
     * @param string $propertyName
     * @ignore
     */
    public function __get( $propertyName )
    {
        if ( $this->__isset( $propertyName ) )
        {
            return $this->properties[$propertyName];
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

    /**
     * Property set access.
     *
     * @throws ezcBasePropertyNotFoundException
     * @param string $propertyName
     * @param string $propertyValue
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'options':
                if ( !( $propertyValue instanceof ezcWorkflowExecutionVisualizerPluginOptions ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'ezcWorkflowExecutionVisualizerPluginOptions'
                    );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
        $this->properties[$propertyName] = $propertyValue;
    }

    /**
     * Property isset access.
     *
     * @param string $propertyName
     * @return bool
     * @ignore
     */
    public function __isset( $propertyName )
    {
        return array_key_exists( $propertyName, $this->properties );
    }

    /**
     * Called after a node has been activated.
     *
     * @param ezcWorkflowExecution $execution
     * @param ezcWorkflowNode      $node
     */
    public function afterNodeActivated( ezcWorkflowExecution $execution, ezcWorkflowNode $node )
    {
        $this->visualize( $execution );
    }

    /**
     * Called after a node has been executed.
     *
     * @param ezcWorkflowExecution $execution
     * @param ezcWorkflowNode      $node
     */
    public function afterNodeExecuted( ezcWorkflowExecution $execution, ezcWorkflowNode $node )
    {
        $this->visualize( $execution );
    }

    /**
     * Visualizes the current state of the workflow execution.
     *
     * @param ezcWorkflowExecution $execution
     */
    protected function visualize( ezcWorkflowExecution $execution )
    {
        $activatedNodes = array();

        foreach ( $execution->getActivatedNodes() as $node )
        {
            $activatedNodes[] = $node->getId();
        }

        if ( $this->options['includeVariables'] )
        {
            $variables = $execution->getVariables();
        }
        else
        {
            $variables = array();
        }

        $visitor = new ezcWorkflowVisitorVisualization;
        $visitor->options['highlightedNodes']  = $activatedNodes;
        $visitor->options['workflowVariables'] = $variables;

        $execution->workflow->accept( $visitor );

        file_put_contents(
          sprintf(
            '%s%s%s_%03d_%03d.dot',

            $this->options['directory'],
            DIRECTORY_SEPARATOR,
            $execution->workflow->name,
            $execution->getId(),
            ++$this->fileCounter
          ),
          $visitor
        );
    }
}
?>
