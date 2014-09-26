<?php
/**
 * File containing the ezcPersistentSessionHandler abstract base class
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Base class for handler classes of ezcPersistentSession.
 *
 * This base class should be used to realized handler classes for {@link
 * ezcPersistentSession}, which are used to structure the methods provided by
 * {@link ezcPersistentSession}.
 * 
 * @package PersistentObject
 * @version 1.7.1
 * @access private
 */
abstract class ezcPersistentSessionHandler
{
    /**
     * Session object this instance belongs to.
     * 
     * @var ezcPersistentSession
     */
    protected $session;

    /**
     * Database connection from {@link $session}. 
     *
     * Kept to avoid a call to {@link ezcPersistentSession->__get()} whenever
     * the database connection is used.
     * 
     * @var ezcDbHandler
     */
    protected $database;

    /**
     * Definition manager from {@link $session}. 
     * 
     * Kept to avoid a call to {@link ezcPersistentSession->__get()} whenever
     * the definition manager is used.
     *
     * @var ezcPersistentDefinitionManager
     */
    protected $definitionManager;

    /**
     * Creates a new load handler.
     * 
     * @param ezcPersistentSession $session 
     */
    public function __construct( ezcPersistentSession $session )
    {
        $this->session           = $session;
        $this->database          = $session->database;
        $this->definitionManager = $session->definitionManager;
    }
}

?>
