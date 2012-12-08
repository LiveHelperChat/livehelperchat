<?php
/**
 * File containing the ezcWorkflowEventLogListener class.
 *
 * @category Workflow
 * @package WorkflowEventLogTiein
 * @version 1.1
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Wrapper for ezcLog that logs workflow events.
 *
 * @package WorkflowEventLogTiein
 * @version 1.1
 */
class ezcWorkflowEventLogListener implements ezcWorkflowExecutionListener
{
    /** 
     * ezcLog instance to be used.
     *
     * @var ezcLog 
     */
    protected $log;

    /**
     * Construct a new event log listener.
     *
     * This constructor is a tie-in.
     *
     * @param ezcLog $log
     */
    public function __construct( ezcLog $log )
    {
        $this->log = $log;
    }

    /**
     * Called to inform about events.
     *
     * @param string  $message
     * @param int $type
     */
    public function notify( $message, $type = ezcWorkflowEventLogListener::INFO )
    {
        $this->log->log( $message, $type );
    }
}
?>
