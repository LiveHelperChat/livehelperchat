<?php
/**
 * File containing the ezcGraphDatabaseMissingColumnException class
 *
 * @package GraphDatabaseTiein
 * @version 1.0.1
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown if a requetsted column could not be found in result set
 *
 * @package GraphDatabaseTiein
 * @version 1.0.1
 */
class ezcGraphDatabaseMissingColumnException extends ezcGraphDatabaseException
{
    /**
     * Constructor
     * 
     * @param string $column
     * @return void
     * @ignore
     */
    public function __construct( $column )
    {
        parent::__construct( "Missing column '{$column}' in result set." );
    }
}

?>
