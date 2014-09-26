<?php
/**
 * File containing the ezcGraphDatabaseTooManyColumnsException class
 *
 * @package GraphDatabaseTiein
 * @version 1.0.1
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown if a data set has too many columns for a key value 
 * association.
 *
 * @package GraphDatabaseTiein
 * @version 1.0.1
 */
class ezcGraphDatabaseTooManyColumnsException extends ezcGraphDatabaseException
{
    /**
     * Constructor
     * 
     * @param array $row
     * @return void
     * @ignore
     */
    public function __construct( $row )
    {
        $columnCount = count( $row );
        parent::__construct( "'{$columnCount}' columns are too many in a result." );
    }
}

?>
