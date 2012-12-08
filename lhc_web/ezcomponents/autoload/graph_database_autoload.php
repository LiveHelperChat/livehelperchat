<?php
/**
 * Autoloader definition for the GraphDatabaseTiein component.
 *
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.0.1
 * @filesource
 * @package GraphDatabaseTiein
 */

return array(
    'ezcGraphDatabaseException'                     => 'GraphDatabaseTiein/exceptions/exception.php',
    'ezcGraphDatabaseMissingColumnException'        => 'GraphDatabaseTiein/exceptions/missing_column.php',
    'ezcGraphDatabaseStatementNotExecutedException' => 'GraphDatabaseTiein/exceptions/statement_not_executed.php',
    'ezcGraphDatabaseTooManyColumnsException'       => 'GraphDatabaseTiein/exceptions/too_many_columns.php',
    'ezcGraphDatabaseDataSet'                       => 'GraphDatabaseTiein/dataset.php',
);
?>
