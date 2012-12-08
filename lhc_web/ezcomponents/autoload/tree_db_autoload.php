<?php
/**
 * Autoloader definition for the TreeDatabaseTiein component.
 *
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.1
 * @filesource
 * @package TreeDatabaseTiein
 */

return array(
    'ezcTreeDbInvalidSchemaException' => 'TreeDatabaseTiein/exceptions/invalid_schema.php',
    'ezcTreeDb'                       => 'TreeDatabaseTiein/backends/db.php',
    'ezcTreeDbDataStore'              => 'TreeDatabaseTiein/stores/db.php',
    'ezcTreeDbParentChild'            => 'TreeDatabaseTiein/backends/db_parent_child.php',
    'ezcTreeDbExternalTableDataStore' => 'TreeDatabaseTiein/stores/db_external.php',
    'ezcTreeDbMaterializedPath'       => 'TreeDatabaseTiein/backends/db_materialized_path.php',
    'ezcTreeDbNestedSet'              => 'TreeDatabaseTiein/backends/db_nested_set.php',
);
?>
