<?php
/**
 * File containing the ezcWorkflowDatabaseExecution class.
 *
 * @package WorkflowDatabaseTiein
 * @version 1.4
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Workflow executer that suspends and resumes workflow
 * execution states to and from a database.
 *
 * @package WorkflowDatabaseTiein
 * @version 1.4
 */
class ezcWorkflowDatabaseExecution extends ezcWorkflowExecution
{
    /**
     * ezcDbHandler instance to be used.
     *
     * @var ezcDbHandler
     */
    protected $db;

    /**
     * Flag that indicates whether the execution has been loaded.
     *
     * @var boolean
     */
    protected $loaded = false;

    /**
     * Container to hold the properties
     *
     * @var array(string=>mixed)
     */
    protected $properties = array(
      'definitionStorage' => null,
      'workflow' => null,
      'options' => null
    );

    /**
     * Construct a new database execution.
     *
     * This constructor is a tie-in.
     *
     * @param  ezcDbHandler $db
     * @param  int          $executionId
     * @throws ezcWorkflowExecutionException
     */
    public function __construct ( ezcDbHandler $db, $executionId = null )
    {
        if ( $executionId !== null && !is_int( $executionId ) )
        {
            throw new ezcWorkflowExecutionException( '$executionId must be an integer.' );
        }

        $this->db = $db;
        $this->properties['definitionStorage'] = new ezcWorkflowDatabaseDefinitionStorage( $db );
        $this->properties['options'] = new ezcWorkflowDatabaseOptions;

        if ( is_int( $executionId ) )
        {
            $this->loadExecution( $executionId );
        }
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
            case 'definitionStorage':
            case 'workflow':
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
            case 'definitionStorage':
            case 'workflow':
                return parent::__set( $propertyName, $propertyValue );
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
            case 'definitionStorage':
            case 'workflow':
            case 'options':
                return true;
        }

        return false;
    }

    /**
     * Start workflow execution.
     *
     * @param  int $parentId
     * @throws ezcDbException
     */
    protected function doStart( $parentId )
    {
        $this->db->beginTransaction();

        $query = $this->db->createInsertQuery();

        $query->insertInto( $this->db->quoteIdentifier( $this->options['prefix'] . 'execution' ) )
              ->set( $this->db->quoteIdentifier( 'workflow_id' ), $query->bindValue( (int)$this->workflow->id ) )
              ->set( $this->db->quoteIdentifier( 'execution_parent' ), $query->bindValue( (int)$parentId ) )
              ->set( $this->db->quoteIdentifier( 'execution_started' ), $query->bindValue( time() ) )
              ->set( $this->db->quoteIdentifier( 'execution_variables' ), $query->bindValue( ezcWorkflowDatabaseUtil::serialize( $this->variables ) ) )
              ->set( $this->db->quoteIdentifier( 'execution_waiting_for' ), $query->bindValue( ezcWorkflowDatabaseUtil::serialize( $this->waitingFor ) ) )
              ->set( $this->db->quoteIdentifier( 'execution_threads' ), $query->bindValue( ezcWorkflowDatabaseUtil::serialize( $this->threads ) ) )
              ->set( $this->db->quoteIdentifier( 'execution_next_thread_id' ), $query->bindValue( (int)$this->nextThreadId ) );

        $statement = $query->prepare();
        $statement->execute();

        $this->id = (int)$this->db->lastInsertId( 'execution_execution_id_seq' );
    }

    /**
     * Suspend workflow execution.
     *
     * @throws ezcDbException
     */
    protected function doSuspend()
    {
        $this->cleanupTable( 'execution_state' );

        $query = $this->db->createUpdateQuery();

        $query->update( $this->db->quoteIdentifier( $this->options['prefix'] . 'execution' ) )
              ->where( $query->expr->eq( $this->db->quoteIdentifier( 'execution_id' ), $query->bindValue( (int)$this->id ) ) )
              ->set( $this->db->quoteIdentifier( 'execution_suspended' ), $query->bindValue( time() ) )
              ->set( $this->db->quoteIdentifier( 'execution_variables' ), $query->bindValue( ezcWorkflowDatabaseUtil::serialize( $this->variables ) ) )
              ->set( $this->db->quoteIdentifier( 'execution_waiting_for' ), $query->bindValue( ezcWorkflowDatabaseUtil::serialize( $this->waitingFor ) ) )
              ->set( $this->db->quoteIdentifier( 'execution_threads' ), $query->bindValue( ezcWorkflowDatabaseUtil::serialize( $this->threads ) ) )
              ->set( $this->db->quoteIdentifier( 'execution_next_thread_id' ), $query->bindValue( (int)$this->nextThreadId ) );

        $statement = $query->prepare();
        $statement->execute();

        foreach ( $this->activatedNodes as $node )
        {
            $query = $this->db->createInsertQuery();

            $query->insertInto( $this->db->quoteIdentifier( $this->options['prefix'] . 'execution_state' ) )
                  ->set( $this->db->quoteIdentifier( 'execution_id' ), $query->bindValue( (int)$this->id ) )
                  ->set( $this->db->quoteIdentifier( 'node_id' ), $query->bindValue( (int)$node->getId() ) )
                  ->set( $this->db->quoteIdentifier( 'node_state' ), $query->bindValue( ezcWorkflowDatabaseUtil::serialize( $node->getState() ) ) )
                  ->set( $this->db->quoteIdentifier( 'node_activated_from' ), $query->bindValue( ezcWorkflowDatabaseUtil::serialize( $node->getActivatedFrom() ) ) )
                  ->set( $this->db->quoteIdentifier( 'node_thread_id' ), $query->bindValue( (int)$node->getThreadId() ) );

            $statement = $query->prepare();
            $statement->execute();
        }

        $this->db->commit();
    }

    /**
     * Resume workflow execution.
     *
     * @throws ezcDbException
     */
    protected function doResume()
    {
        $this->db->beginTransaction();
    }

    /**
     * End workflow execution.
     *
     * @throws ezcDbException
     */
    protected function doEnd()
    {
        $this->cleanupTable( 'execution' );
        $this->cleanupTable( 'execution_state' );

        if ( !$this->isCancelled() )
        {
            $this->db->commit();
        }
    }

    /**
     * Returns a new execution object for a sub workflow.
     *
     * @param  int $id
     * @return ezcWorkflowExecution
     */
    protected function doGetSubExecution( $id = null )
    {
        return new ezcWorkflowDatabaseExecution( $this->db, $id );
    }

    /**
     * Cleanup execution / execution_state tables.
     *
     * @param  string $tableName
     * @throws ezcDbException
     */
    protected function cleanupTable( $tableName )
    {
        $query = $this->db->createDeleteQuery();
        $query->deleteFrom( $this->db->quoteIdentifier( $this->options['prefix'] . $tableName ) );

        $id = $query->expr->eq( $this->db->quoteIdentifier( 'execution_id' ), $query->bindValue( (int)$this->id ) );

        if ( $tableName == 'execution' )
        {
            $parent = $query->expr->eq( $this->db->quoteIdentifier( 'execution_parent' ), $query->bindValue( (int)$this->id ) );
            $query->where( $query->expr->lOr( $id, $parent ) );
        }
        else
        {
            $query->where( $id );
        }

        $statement = $query->prepare();
        $statement->execute();
    }

    /**
     * Load execution state.
     *
     * @param int $executionId  ID of the execution to load.
     * @throws ezcWorkflowExecutionException
     */
    protected function loadExecution( $executionId )
    {
        $query = $this->db->createSelectQuery();

        $query->select( $this->db->quoteIdentifier( 'workflow_id' ) )
              ->select( $this->db->quoteIdentifier( 'execution_variables' ) )
              ->select( $this->db->quoteIdentifier( 'execution_threads' ) )
              ->select( $this->db->quoteIdentifier( 'execution_next_thread_id' ) )
              ->select( $this->db->quoteIdentifier( 'execution_waiting_for' ) )
              ->from( $this->db->quoteIdentifier( $this->options['prefix'] . 'execution' ) )
              ->where( $query->expr->eq( $this->db->quoteIdentifier( 'execution_id' ),
                                         $query->bindValue( (int)$executionId ) ) );

        $stmt = $query->prepare();
        $stmt->execute();

        $result = $stmt->fetchAll( PDO::FETCH_ASSOC );

        if ( $result === false || empty( $result ) )
        {
            throw new ezcWorkflowExecutionException(
              'Could not load execution state.'
            );
        }

        $this->id = $executionId;
        $this->nextThreadId = $result[0]['execution_next_thread_id'];

        $this->threads = ezcWorkflowDatabaseUtil::unserialize( $result[0]['execution_threads'] );
        $this->variables = ezcWorkflowDatabaseUtil::unserialize( $result[0]['execution_variables'] );
        $this->waitingFor = ezcWorkflowDatabaseUtil::unserialize( $result[0]['execution_waiting_for'] );

        $workflowId     = $result[0]['workflow_id'];
        $this->workflow = $this->properties['definitionStorage']->loadById( $workflowId );

        $query = $this->db->createSelectQuery();

        $query->select( $this->db->quoteIdentifier( 'node_id' ) )
              ->select( $this->db->quoteIdentifier( 'node_state' ) )
              ->select( $this->db->quoteIdentifier( 'node_activated_from' ) )
              ->select( $this->db->quoteIdentifier( 'node_thread_id' ) )
              ->from( $this->db->quoteIdentifier( $this->options['prefix'] . 'execution_state' ) )
              ->where( $query->expr->eq( $this->db->quoteIdentifier( 'execution_id' ),
                                         $query->bindValue( (int)$executionId ) ) );

        $stmt = $query->prepare();
        $stmt->execute();

        $result = $stmt->fetchAll( PDO::FETCH_ASSOC );
        $active = array();

        foreach ( $result as $row )
        {
            $active[$row['node_id']] = array(
              'activated_from' => ezcWorkflowDatabaseUtil::unserialize(
                $row['node_activated_from']
              ),
              'state' => ezcWorkflowDatabaseUtil::unserialize(
                $row['node_state'], null
              ),
              'thread_id' => $row['node_thread_id']
            );
        }

        foreach ( $this->workflow->nodes as $node )
        {
            $nodeId = $node->getId();

            if ( isset( $active[$nodeId] ) )
            {
                $node->setActivationState( ezcWorkflowNode::WAITING_FOR_EXECUTION );
                $node->setThreadId( $active[$nodeId]['thread_id'] );
                $node->setState( $active[$nodeId]['state'], null );
                $node->setActivatedFrom( $active[$nodeId]['activated_from'] );

                $this->activate( $node, false );
            }
        }

        $this->cancelled = false;
        $this->ended     = false;
        $this->loaded    = true;
        $this->resumed   = false;
        $this->suspended = true;
    }
}
?>
