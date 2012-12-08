<?php
/**
 * File containing the ezcGraphDatabaseStatementNotExecutedException class
 *
 * @package GraphDatabaseTiein
 * @version 1.0.1
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown if a given statement has not been executed.
 *
 * @package GraphDatabaseTiein
 * @version 1.0.1
 */
class ezcGraphDatabaseStatementNotExecutedException extends ezcGraphDatabaseException
{
    /**
     * Constructor
     * 
     * @param PDOStatement $statement
     * @return void
     * @ignore
     */
    public function __construct( $statement )
    {
        parent::__construct( "Empty result set. Execute the statement before using with ezcGraphDatabaseTiein." );
    }
}

?>
