<?php
/**
 * File containing the ezcWorkflow class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class representing a workflow.
 *
 * @property ezcWorkflowDefinitonStorage $definitionStorage
 *           The definition handler used to fetch sub workflows on demand.
 *           This property is set automatically if you load a workflow using
 *           a workflow definition storage.
 * @property int                         $id
 *           Unique ID set automatically by the definition handler when the
 *           workflow is stored.
 * @property string                      $name
 *           A unique name (accross the system) for this workflow.
 * @property int                         $version
 *           The version of the workflow. This must be incremented manually
 *           whenever you want a new version.
 * @property-read ezcWorkflowNodeStart   $startNode The unique start node of the workflow.
 * @property-read ezcWorkflowNodeEnd     $endNode The default end node of the workflow.
 * @property-read ezcWorkflowNodeFinally $finallyNode The start of a node
 *                                       sequence that is executed when a
 *                                       workflow execution is cancelled.
 * @property-read array(ezcWorkflowNode) $nodes All the nodes of this workflow.
 *
 * @package Workflow
 * @version 1.4.1
 * @mainclass
 */
class ezcWorkflow implements Countable, ezcWorkflowVisitable
{
    /**
     * Container to hold the properties
     *
     * @var array(string=>mixed)
     */
    protected $properties = array(
      'definitionStorage' => null,
      'id'                => false,
      'name'              => '',
      'startNode'         => null,
      'endNode'           => null,
      'finallyNode'       => null,
      'version'           => 1
    );

    /**
     * The variable handlers of this workflow.
     *
     * @var array
     */
    protected $variableHandlers = array();

    /**
     * Constructs a new workflow object with the name $name.
     *
     * Use $startNode and $endNode parameters if you don't want to use the
     * default start and end nodes.
     *
     * $name must uniquely identify the workflow within the system.
     *
     * @param string                 $name        The name of the workflow.
     * @param ezcWorkflowNodeStart   $startNode   The start node of the workflow.
     * @param ezcWorkflowNodeEnd     $endNode     The default end node of the workflow.
     * @param ezcWorkflowNodeFinally $finallyNode The start of a node sequence
     *                                            that is executed when a workflow
     *                                            execution is cancelled.
     */
    public function __construct( $name, ezcWorkflowNodeStart $startNode = null, ezcWorkflowNodeEnd $endNode = null, ezcWorkflowNodeFinally $finallyNode = null )
    {
        $this->name = $name;

        // Create a new ezcWorkflowNodeStart object, if necessary.
        if ( $startNode === null )
        {
            $this->properties['startNode'] = new ezcWorkflowNodeStart;
        }
        else
        {
            $this->properties['startNode'] = $startNode;
        }

        // Create a new ezcWorkflowNodeEnd object, if necessary.
        if ( $endNode === null )
        {
            $this->properties['endNode'] = new ezcWorkflowNodeEnd;
        }
        else
        {
            $this->properties['endNode'] = $endNode;
        }

        // Create a new ezcWorkflowNodeFinally object, if necessary.
        if ( $finallyNode === null )
        {
            $this->properties['finallyNode'] = new ezcWorkflowNodeFinally;
        }
        else
        {
            $this->properties['finallyNode'] = $finallyNode;
        }
    }

    /**
     * Property read access.
     *
     * @throws ezcBasePropertyNotFoundException 
     *         If the the desired property is not found.
     * 
     * @param string $propertyName Name of the property.
     * @return mixed Value of the property or null.
     * @ignore
     */
    public function __get( $propertyName )
    {
        switch ( $propertyName ) 
        {
            case 'definitionStorage':
            case 'id':
            case 'name':
            case 'startNode':
            case 'endNode':
            case 'finallyNode':
            case 'version':
                return $this->properties[$propertyName];

            case 'nodes':
                $visitor = new ezcWorkflowVisitorNodeCollector( $this );

                return $visitor->getNodes();
        }

        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

    /**
     * Property write access.
     * 
     * @param string $propertyName Name of the property.
     * @param mixed $val  The value for the property.
     *
     * @throws ezcBaseValueException 
     *         If the value for the property definitionStorage is not an
     *         instance of ezcWorkflowDefinitionStorage.
     * @throws ezcBaseValueException 
     *         If the value for the property id is not an integer.
     * @throws ezcBaseValueException 
     *         If the value for the property name is not a string.
     * @throws ezcBasePropertyPermissionException 
     *         If there is a write access to startNode.
     * @throws ezcBasePropertyPermissionException 
     *         If there is a write access to endNode.
     * @throws ezcBasePropertyPermissionException 
     *         If there is a write access to finallyNode.
     * @throws ezcBasePropertyPermissionException 
     *         If there is a write access to nodes.
     * @throws ezcBaseValueException 
     *         If the value for the property version is not an integer.
     * @ignore
     */
    public function __set( $propertyName, $val )
    {
        switch ( $propertyName ) 
        {
            case 'definitionStorage':
                if ( !( $val instanceof ezcWorkflowDefinitionStorage ) )
                {
                    throw new ezcBaseValueException( $propertyName, $val, 'ezcWorkflowDefinitionStorage' );
                }

                $this->properties['definitionStorage'] = $val;

                return;

            case 'id':
                if ( !( is_int( $val ) ) )
                {
                    throw new ezcBaseValueException( $propertyName, $val, 'integer' );
                }

                $this->properties['id'] = $val;

                return;

            case 'name':
                if ( !( is_string( $val ) ) )
                {
                    throw new ezcBaseValueException( $propertyName, $val, 'string' );
                }

                $this->properties['name'] = $val;

                return;

            case 'startNode':
            case 'endNode':
            case 'finallyNode':
            case 'nodes':
                throw new ezcBasePropertyPermissionException(
                  $propertyName, ezcBasePropertyPermissionException::READ
                );

            case 'version':
                if ( !( is_int( $val ) ) )
                {
                    throw new ezcBaseValueException( $propertyName, $val, 'integer' );
                }

                $this->properties['version'] = $val;

                return;
        }

        throw new ezcBasePropertyNotFoundException( $propertyName );
    }
 
    /**
     * Property isset access.
     * 
     * @param string $propertyName Name of the property.
     * @return bool True is the property is set, otherwise false.
     * @ignore
     */
    public function __isset( $propertyName )
    {
        switch ( $propertyName )
        {
            case 'definitionStorage':
            case 'id':
            case 'name':
            case 'startNode':
            case 'endNode':
            case 'finallyNode':
            case 'nodes':
            case 'version':
                return true;
        }

        return false;
    }

    /**
     * Returns the number of nodes of this workflow.
     *
     * @return integer
     */
    public function count()
    {
        $visitor = new ezcWorkflowVisitor;
        $this->accept( $visitor );

        return count( $visitor );
    }

    /**
     * Returns true when the workflow requires user interaction
     * (ie. when it contains ezcWorkflowNodeInput nodes)
     * and false otherwise.
     *
     * @return boolean true when the workflow is interactive, false otherwise.
     */
    public function isInteractive()
    {
        foreach ( $this->nodes as $node )
        {
            if ( $node instanceof ezcWorkflowNodeInput )
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true when the workflow has sub workflows
     * (ie. when it contains ezcWorkflowNodeSubWorkflow nodes)
     * and false otherwise.
     *
     * @return boolean true when the workflow has sub workflows, false otherwise.
     */
    public function hasSubWorkflows()
    {
        foreach ( $this->nodes as $node )
        {
            if ( $node instanceof ezcWorkflowNodeSubWorkflow )
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Resets the nodes of this workflow.
     *
     * See the documentation of ezcWorkflowVisitorReset for
     * details.
     */
    public function reset()
    {
        $this->accept( new ezcWorkflowVisitorReset );
    }

    /**
     * Verifies the specification of this workflow.
     *
     * See the documentation of ezcWorkflowVisitorVerification for
     * details.
     *
     * @throws ezcWorkflowInvalidWorkflowException if the specification of this workflow is not correct.
     */
    public function verify()
    {
        $this->accept( new ezcWorkflowVisitorVerification );
    }

    /**
     * Overridden implementation of accept() calls
     * accept on the start node.
     *
     * @param ezcWorkflowVisitor $visitor
     */
    public function accept( ezcWorkflowVisitor $visitor )
    {
        $visitor->visit( $this );
        $this->properties['startNode']->accept( $visitor );
    }

    /**
     * Sets the class $className to handle the variable named $variableName.
     *
     * $className must be the name of a class implementing the
     * ezcWorkflowVariableHandler interface.
     *
     * @param string $variableName
     * @param string $className
     * @throws ezcWorkflowInvalidWorkflowException if $className does not contain the name of a valid class implementing ezcWorkflowVariableHandler
     */
    public function addVariableHandler( $variableName, $className )
    {
        if ( class_exists( $className ) )
        {
            $class = new ReflectionClass( $className );

            if ( $class->implementsInterface( 'ezcWorkflowVariableHandler' ) )
            {
                $this->variableHandlers[$variableName] = $className;
            }
            else
            {
                throw new ezcWorkflowInvalidWorkflowException(
                  sprintf( 'Class "%s" does not implement the ezcWorkflowVariableHandler interface.', $className )
                );
            }
        }
        else
        {
            throw new ezcWorkflowInvalidWorkflowException(
              sprintf( 'Class "%s" not found.', $className )
            );
        }
    }

    /**
     * Removes the handler for $variableName and returns true
     * on success.
     *
     * Returns false if no handler was set for $variableName.
     *
     * @param string $variableName
     * @return boolean
     */
    public function removeVariableHandler( $variableName )
    {
        if ( isset( $this->variableHandlers[$variableName] ) )
        {
            unset( $this->variableHandlers[$variableName] );
            return true;
        }

        return false;
    }

    /**
     * Sets handlers for multiple variables.
     *
     * The format of $variableHandlers is
     * array( 'variableName' => ezcWorkflowVariableHandler )
     *
     * @throws ezcWorkflowInvalidWorkflowException if $className does not contain the name of a valid class implementing ezcWorkflowVariableHandler
     * @param array $variableHandlers
     */
    public function setVariableHandlers( array $variableHandlers )
    {
        $this->variableHandlers = array();

        foreach ( $variableHandlers as $variableName => $className )
        {
            $this->addVariableHandler( $variableName, $className );
        }
    }

    /**
     * Returns the variable handlers.
     *
     * The format of the returned array is
     * array( 'variableName' => ezcWorkflowVariableHandler )
     *
     * @return array
     */
    public function getVariableHandlers()
    {
        return $this->variableHandlers;
    }
}
?>
