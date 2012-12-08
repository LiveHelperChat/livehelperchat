<?php
/**
 * File containing the ezcWorkflowVisitorVisualization class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * An implementation of the ezcWorkflowVisitor interface that
 * generates GraphViz/dot markup for a workflow definition.
 *
 * <code>
 * <?php
 * $visitor = new ezcWorkflowVisitorVisualization;
 * $workflow->accept( $visitor );
 * print $visitor;
 * ?>
 * </code>
 *
 * @property ezcWorkflowVisitorVisualizationOptions $options
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowVisitorVisualization extends ezcWorkflowVisitor
{
    /**
     * Holds the displayed strings for each of the nodes.
     *
     * @var array(string => string)
     */
    protected $nodes = array();

    /**
     * Holds all the edges of the graph.
     *
     * @var array( id => array( ezcWorkflowNode ) )
     */
    protected $edges = array();

    /**
     * Holds the name of the workflow.
     *
     * @var string
     */
    protected $workflowName = 'Workflow';

    /**
     * Properties.
     *
     * @var array(string=>mixed)
     */
    protected $properties = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->options = new ezcWorkflowVisitorVisualizationOptions;
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
                if ( !( $propertyValue instanceof ezcWorkflowVisitorVisualizationOptions ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'ezcWorkflowVisitorVisualizationOptions'
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
     * Perform the visit.
     *
     * @param ezcWorkflowVisitable $visitable
     */
    protected function doVisit( ezcWorkflowVisitable $visitable )
    {
        if ( $visitable instanceof ezcWorkflow )
        {
            $this->workflowName = $visitable->name;

            // The following line of code is not a no-op. It triggers the
            // ezcWorkflow::__get() method, thus initializing the respective
            // ezcWorkflowVisitorNodeCollector object.
            $visitable->nodes;
        }

        if ( $visitable instanceof ezcWorkflowNode )
        {
            $id = $visitable->getId();

            if ( in_array( $id, $this->options['highlightedNodes'] ) )
            {
                $color = $this->options['colorHighlighted'];
            }
            else
            {
                $color = $this->options['colorNormal'];
            }

            if ( !isset( $this->nodes[$id] ) )
            {
                $this->nodes[$id] = array(
                  'label' => (string)$visitable,
                  'color' => $color
                );
            }

            $outNodes = array();

            foreach ( $visitable->getOutNodes() as $outNode )
            {
                $label = '';

                if ( $visitable instanceof ezcWorkflowNodeConditionalBranch )
                {
                    $condition = $visitable->getCondition( $outNode );

                    if ( $condition !== false )
                    {
                        $label = ' [label="' . $condition . '"]';
                    }
                }

                $outNodes[] = array( $outNode->getId(), $label );
            }

            $this->edges[$id] = $outNodes;
        }
    }

    /**
     * Returns a the contents of a graphviz .dot file.
     *
     * @return boolean
     * @ignore
     */
    public function __toString()
    {
        $dot = 'digraph ' . $this->workflowName . " {\n";

        foreach ( $this->nodes as $key => $data )
        {
            $dot .= sprintf(
              "node%s [label=\"%s\", color=\"%s\"]\n",
              $key,
              $data['label'],
              $data['color']
            );
        }

        $dot .= "\n";

        foreach ( $this->edges as $fromNode => $toNodes )
        {
            foreach ( $toNodes as $toNode )
            {
                $dot .= sprintf(
                  "node%s -> node%s%s\n",

                  $fromNode,
                  $toNode[0],
                  $toNode[1]
                );
            }
        }

        if ( !empty( $this->options['workflowVariables'] ) )
        {
            $dot .= 'variables [shape=none, label=<<table>';

            foreach ( $this->options['workflowVariables'] as $name => $value )
            {
                $dot .= sprintf(
                  '<tr><td>%s</td><td>%s</td></tr>',

                  $name,
                  htmlspecialchars( ezcWorkflowUtil::variableToString( $value ) )
                );
            }

            $dot .= "</table>>]\n";
        }

        return $dot . "}\n";
    }
}
?>
