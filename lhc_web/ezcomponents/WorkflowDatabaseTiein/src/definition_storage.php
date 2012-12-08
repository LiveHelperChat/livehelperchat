<?php
/**
 * File containing the ezcWorkflowDatabaseDefinitionStorage class.
 *
 * @package WorkflowDatabaseTiein
 * @version 1.4
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Workflow definition storage handler that saves and loads workflow
 * definitions to and from a database.
 *
 * @package WorkflowDatabaseTiein
 * @version 1.4
 */
class ezcWorkflowDatabaseDefinitionStorage implements ezcWorkflowDefinitionStorage
{
    /**
     * ezcDbHandler instance to be used.
     *
     * @var ezcDbHandler
     */
    protected $db;

    /**
     * Container to hold the properties
     *
     * @var array(string=>mixed)
     */
    protected $properties = array(
      'options' => null
    );

    /**
     * Construct a new database definition handler.
     *
     * This constructor is a tie-in.
     *
     * @param ezcDbHandler $db
     */
    public function __construct( ezcDbHandler $db )
    {
        $this->db = $db;
        $this->properties['options'] = new ezcWorkflowDatabaseOptions;
    }

    /**
     * Property get access.
     *
     * @param string $propertyName
     * @return mixed
     * @throws ezcBasePropertyNotFoundException
     *         If the given property could not be found.
     * @ignore
     */
    public function __get( $propertyName )
    {
        switch ( $propertyName )
        {
            case 'options':
                return $this->properties[$propertyName];
        }

        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

    /**
     * Property set access.
     *
     * @param string $propertyName
     * @param string $propertyValue
     * @throws ezcBasePropertyNotFoundException
     *         If the given property could not be found.
     * @throws ezcBaseValueException
     *         If the value for the property options is not an ezcWorkflowDatabaseOptions object.
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'options':
                if ( !( $propertyValue instanceof ezcWorkflowDatabaseOptions ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'ezcWorkflowDatabaseOptions'
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
        switch ( $propertyName )
        {
            case 'options':
                return true;
        }

        return false;
    }

    /**
     * Load a workflow definition by ID.
     *
     * Providing the name of the workflow that is to be loaded as the
     * optional second parameter saves a database query.
     *
     * @param  int $workflowId
     * @param  string  $workflowName
     * @param  int $workflowVersion
     * @return ezcWorkflow
     * @throws ezcWorkflowDefinitionStorageException
     * @throws ezcDbException
     */
    public function loadById( $workflowId, $workflowName = '', $workflowVersion = 0 )
    {
        // Query the database for the name and version of the workflow.
        if ( empty( $workflowName ) || $workflowVersion == 0 )
        {
            $query = $this->db->createSelectQuery();

            $query->select( $this->db->quoteIdentifier( 'workflow_name' ) )
                  ->select( $this->db->quoteIdentifier( 'workflow_version' ) )
                  ->from( $this->db->quoteIdentifier( $this->options['prefix'] . 'workflow' ) )
                  ->where( $query->expr->eq( $this->db->quoteIdentifier( 'workflow_id' ),
                                             $query->bindValue( (int)$workflowId ) ) );

            $stmt = $query->prepare();
            $stmt->execute();

            $result = $stmt->fetchAll( PDO::FETCH_ASSOC );

            if ( $result !== false && isset( $result[0] ) )
            {
                $workflowName    = $result[0]['workflow_name'];
                $workflowVersion = $result[0]['workflow_version'];
            }
            else
            {
                throw new ezcWorkflowDefinitionStorageException(
                  'Could not load workflow definition.'
                );
            }
        }

        // Query the database for the nodes of the workflow to be loaded.
        $query = $this->db->createSelectQuery();

        $query->select( $this->db->quoteIdentifier( 'node_id' ) )
              ->select( $this->db->quoteIdentifier( 'node_class' ) )
              ->select( $this->db->quoteIdentifier( 'node_configuration' ) )
              ->from( $this->db->quoteIdentifier( $this->options['prefix'] . 'node' ) )
              ->where( $query->expr->eq( $this->db->quoteIdentifier( 'workflow_id' ),
                                          $query->bindValue( (int)$workflowId ) ) );

        $stmt = $query->prepare();
        $stmt->execute();

        $result = $stmt->fetchAll( PDO::FETCH_ASSOC );
        $nodes  = array();

        // Create node objects.
        foreach ( $result as $node )
        {
            $configuration = ezcWorkflowDatabaseUtil::unserialize(
              $node['node_configuration'], null
            );

            if ( is_null( $configuration ) )
            {
                $configuration = ezcWorkflowUtil::getDefaultConfiguration( $node['node_class'] );
            }

            $nodes[$node['node_id']] = new $node['node_class'](
              $configuration
            );

            if ($nodes[$node['node_id']] instanceof ezcWorkflowNodeFinally &&
                !isset( $finallyNode ) )
            {
                $finallyNode = $nodes[$node['node_id']];
            }

            else if ($nodes[$node['node_id']] instanceof ezcWorkflowNodeEnd &&
                     !isset( $defaultEndNode ) )
            {
                $defaultEndNode = $nodes[$node['node_id']];
            }

            else if ($nodes[$node['node_id']] instanceof ezcWorkflowNodeStart &&
                     !isset( $startNode ) )
            {
               $startNode = $nodes[$node['node_id']];
            }
        }

        if ( !isset( $startNode ) || !isset( $defaultEndNode ) )
        {
            throw new ezcWorkflowDefinitionStorageException(
              'Could not load workflow definition.'
            );
        }

        // Connect node objects.
        $query = $this->db->createSelectQuery();

        $query->select( $query->alias( 'node_connection.incoming_node_id',
                                       $this->db->quoteIdentifier( 'incoming_node_id' ) ) )
              ->select( $query->alias( 'node_connection.outgoing_node_id',
                                       $this->db->quoteIdentifier( 'outgoing_node_id' ) ) )
              ->from( $query->innerJoin( $this->db->quoteIdentifier( $this->options['prefix'] . 'node_connection' ),
                                         $this->db->quoteIdentifier( $this->options['prefix'] . 'node' ),
                                         'node_connection.incoming_node_id',
                                         'node.node_id' ) )
              ->where( $query->expr->eq( 'node.workflow_id',
                                         $query->bindValue( (int)$workflowId ) ) )
              ->orderBy( $this->db->quoteIdentifier( 'node_connection_id' ) );

        $stmt = $query->prepare();
        $stmt->execute();

        $connections = $stmt->fetchAll( PDO::FETCH_ASSOC );

        foreach ( $connections as $connection )
        {
            $nodes[$connection['incoming_node_id']]->addOutNode(
              $nodes[$connection['outgoing_node_id']]
            );
        }

        if ( !isset( $finallyNode ) ||
             count( $finallyNode->getInNodes() ) > 0 )
        {
            $finallyNode = null;
        }

        // Create workflow object and add the node objects to it.
        $workflow = new ezcWorkflow( $workflowName, $startNode, $defaultEndNode, $finallyNode );
        $workflow->definitionStorage = $this;
        $workflow->id = (int)$workflowId;
        $workflow->version = (int)$workflowVersion;

        // Query the database for the variable handlers.
        $query = $this->db->createSelectQuery();

        $query->select( $this->db->quoteIdentifier( 'variable' ) )
              ->select( $this->db->quoteIdentifier( 'class' ) )
              ->from( $this->db->quoteIdentifier( $this->options['prefix'] . 'variable_handler' ) )
              ->where( $query->expr->eq( $this->db->quoteIdentifier( 'workflow_id' ),
                                         $query->bindValue( (int)$workflowId ) ) );

        $stmt = $query->prepare();
        $stmt->execute();

        $result = $stmt->fetchAll( PDO::FETCH_ASSOC );
        $nodes  = array();

        if ( $result !== false )
        {
            foreach ( $result as $variableHandler )
            {
                $workflow->addVariableHandler(
                  $variableHandler['variable'],
                  $variableHandler['class']
                );
            }
        }

        // Verify the loaded workflow.
        $workflow->verify();

        return $workflow;
    }

    /**
     * Load a workflow definition by name.
     *
     * @param  string  $workflowName
     * @param  int $workflowVersion
     * @return ezcWorkflow
     * @throws ezcWorkflowDefinitionStorageException
     * @throws ezcDbException
     */
    public function loadByName( $workflowName, $workflowVersion = 0 )
    {
        // Query the database for the workflow ID.
        $query = $this->db->createSelectQuery();

        // Load the current version of the workflow.
        if ( $workflowVersion == 0 )
        {
            $workflowVersion = $this->getCurrentVersionNumber( $workflowName );
        }

        // Query for the workflow_id.
        $query->select( $this->db->quoteIdentifier( 'workflow_id' ) )
              ->from( $this->db->quoteIdentifier( $this->options['prefix'] . 'workflow' ) )
              ->where( $query->expr->eq( $this->db->quoteIdentifier( 'workflow_name' ),
                                         $query->bindValue( $workflowName ) ) )
              ->where( $query->expr->eq( $this->db->quoteIdentifier( 'workflow_version' ),
                                         $query->bindValue( (int)$workflowVersion ) ) );

        $stmt = $query->prepare();
        $stmt->execute();

        $result = $stmt->fetchAll( PDO::FETCH_ASSOC );

        if ( $result !== false && isset( $result[0] ) )
        {
            return $this->loadById(
              $result[0]['workflow_id'],
              $workflowName,
              $workflowVersion
            );
        }
        else
        {
            throw new ezcWorkflowDefinitionStorageException(
              'Could not load workflow definition.'
            );
        }
    }

    /**
     * Save a workflow definition to the database.
     *
     * @param  ezcWorkflow $workflow
     * @throws ezcWorkflowDefinitionStorageException
     * @throws ezcDbException
     */
    public function save( ezcWorkflow $workflow )
    {
        // Verify the workflow.
        $workflow->verify();

        $this->db->beginTransaction();

        // Calculate new version number.
        $workflowVersion = $this->getCurrentVersionNumber( $workflow->name ) + 1;

        // Write workflow table row.
        $query = $this->db->createInsertQuery();

        $query->insertInto( $this->db->quoteIdentifier( $this->options['prefix'] . 'workflow' ) )
              ->set( $this->db->quoteIdentifier( 'workflow_name' ), $query->bindValue( $workflow->name ) )
              ->set( $this->db->quoteIdentifier( 'workflow_version' ), $query->bindValue( (int)$workflowVersion ) )
              ->set( $this->db->quoteIdentifier( 'workflow_created' ), $query->bindValue( time() ) );

        $statement = $query->prepare();
        $statement->execute();

        $workflow->definitionStorage = $this;
        $workflow->id = (int)$this->db->lastInsertId( 'workflow_workflow_id_seq' );
        $workflow->version = (int)$workflowVersion;

        // Write node table rows.
        $nodeMap = array();

        foreach ( $workflow->nodes as $node )
        {
            $query = $this->db->createInsertQuery();

            $query->insertInto( $this->db->quoteIdentifier( $this->options['prefix'] . 'node' ) )
                  ->set( $this->db->quoteIdentifier( 'workflow_id' ), $query->bindValue( (int)$workflow->id ) )
                  ->set( $this->db->quoteIdentifier( 'node_class' ), $query->bindValue( get_class( $node ) ) )
                  ->set( $this->db->quoteIdentifier( 'node_configuration' ), $query->bindValue(
                    ezcWorkflowDatabaseUtil::serialize( $node->getConfiguration() ) )
                  );

            $statement = $query->prepare();
            $statement->execute();

            $nodeMap[$this->db->lastInsertId( $this->db->quoteIdentifier( 'node_node_id_seq' ) )] = $node;
        }

        // Connect node table rows.
        foreach ( $workflow->nodes as $node )
        {
            foreach ( $node->getOutNodes() as $outNode )
            {
                $incomingNodeId = null;
                $outgoingNodeId = null;

                foreach ( $nodeMap as $_id => $_node )
                {
                    if ( $_node === $node )
                    {
                        $incomingNodeId = $_id;
                    }

                    else if ( $_node === $outNode )
                    {
                        $outgoingNodeId = $_id;
                    }

                    if ( $incomingNodeId !== NULL && $outgoingNodeId !== NULL )
                    {
                        break;
                    }
                }

                $query = $this->db->createInsertQuery();

                $query->insertInto( $this->db->quoteIdentifier( $this->options['prefix'] . 'node_connection' ) )
                      ->set( $this->db->quoteIdentifier( 'incoming_node_id' ), $query->bindValue( $incomingNodeId ) )
                      ->set( $this->db->quoteIdentifier( 'outgoing_node_id' ), $query->bindValue( $outgoingNodeId ) );

                $statement = $query->prepare();
                $statement->execute();
            }
        }

        unset( $nodeMap );

        // Write variable handler rows.
        foreach ( $workflow->getVariableHandlers() as $variable => $class )
        {
            $query = $this->db->createInsertQuery();

            $query->insertInto( $this->db->quoteIdentifier( $this->options['prefix'] . 'variable_handler' ) )
                  ->set( $this->db->quoteIdentifier( 'workflow_id' ), $query->bindValue( (int)$workflow->id ) )
                  ->set( $this->db->quoteIdentifier( 'variable' ), $query->bindValue( $variable ) )
                  ->set( $this->db->quoteIdentifier( 'class' ), $query->bindValue( $class ) );

            $statement = $query->prepare();
            $statement->execute();
        }

        $this->db->commit();
    }

    /**
     * Returns the current version number for a given workflow name.
     *
     * @param  string $workflowName
     * @return int
     * @throws ezcDbException
     */
    protected function getCurrentVersionNumber( $workflowName )
    {
        $query = $this->db->createSelectQuery();

        $query->select( $query->alias( $query->expr->max( $this->db->quoteIdentifier( 'workflow_version' ) ),
                                       'version' ) )
              ->from( $this->db->quoteIdentifier( $this->options['prefix'] . 'workflow' ) )
              ->where( $query->expr->eq( $this->db->quoteIdentifier( 'workflow_name' ),
                                         $query->bindValue( $workflowName ) ) );

        $stmt = $query->prepare();
        $stmt->execute();

        $result = $stmt->fetchAll( PDO::FETCH_ASSOC );

        if ( $result !== false && isset( $result[0]['version'] ) && $result[0]['version'] !== null )
        {
            return $result[0]['version'];
        }
        else
        {
            return 0;
        }
    }
}
?>
