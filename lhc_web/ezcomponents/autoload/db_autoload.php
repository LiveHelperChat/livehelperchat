<?php
/**
 * Autoloader definition for the Database component.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.4.7
 * @filesource
 * @package Database
 */

return array(
    'ezcDbException'                 => 'Database/exceptions/exception.php',
    'ezcDbHandlerNotFoundException'  => 'Database/exceptions/handler_not_found.php',
    'ezcDbMissingParameterException' => 'Database/exceptions/missing_parameter.php',
    'ezcDbTransactionException'      => 'Database/exceptions/transaction.php',
    'ezcDbHandler'                   => 'Database/handler.php',
    'ezcDbUtilities'                 => 'Database/sqlabstraction/utilities.php',
    'ezcDbFactory'                   => 'Database/factory.php',
    'ezcDbHandlerMssql'              => 'Database/handlers/mssql.php',
    'ezcDbHandlerMysql'              => 'Database/handlers/mysql.php',
    'ezcDbHandlerOracle'             => 'Database/handlers/oracle.php',
    'ezcDbHandlerPgsql'              => 'Database/handlers/pgsql.php',
    'ezcDbHandlerSqlite'             => 'Database/handlers/sqlite.php',
    'ezcDbInstance'                  => 'Database/instance.php',
    'ezcDbMssqlOptions'              => 'Database/options/identifiers.php',
    'ezcDbUtilitiesMysql'            => 'Database/sqlabstraction/implementations/utilities_mysql.php',
    'ezcDbUtilitiesOracle'           => 'Database/sqlabstraction/implementations/utilities_oracle.php',
    'ezcDbUtilitiesPgsql'            => 'Database/sqlabstraction/implementations/utilities_pgsql.php',
    'ezcDbUtilitiesSqlite'           => 'Database/sqlabstraction/implementations/utilities_sqlite.php',
);
?>
