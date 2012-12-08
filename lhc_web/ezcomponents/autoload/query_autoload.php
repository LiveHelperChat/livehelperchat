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
    'ezcQueryException'              => 'Database/exceptions/query_exception.php',
    'ezcQueryInvalidException'       => 'Database/exceptions/query/invalid.php',
    'ezcQueryInvalidParameterException' => 'Database/exceptions/query/invalid_parameter.php',
    'ezcQueryVariableParameterException' => 'Database/exceptions/query/variable_parameter.php',
    'ezcQuery'                       => 'Database/sqlabstraction/query.php',
    'ezcQueryExpression'             => 'Database/sqlabstraction/expression.php',
    'ezcQuerySelect'                 => 'Database/sqlabstraction/query_select.php',
    'ezcQueryDelete'                 => 'Database/sqlabstraction/query_delete.php',
    'ezcQueryExpressionMssql'        => 'Database/sqlabstraction/implementations/expression_mssql.php',
    'ezcQueryExpressionOracle'       => 'Database/sqlabstraction/implementations/expression_oracle.php',
    'ezcQueryExpressionPgsql'        => 'Database/sqlabstraction/implementations/expression_pgsql.php',
    'ezcQueryExpressionSqlite'       => 'Database/sqlabstraction/implementations/expression_sqlite.php',
    'ezcQueryInsert'                 => 'Database/sqlabstraction/query_insert.php',
    'ezcQuerySelectMssql'            => 'Database/sqlabstraction/implementations/query_select_mssql.php',
    'ezcQuerySelectOracle'           => 'Database/sqlabstraction/implementations/query_select_oracle.php',
    'ezcQuerySelectSqlite'           => 'Database/sqlabstraction/implementations/query_select_sqlite.php',
    'ezcQuerySqliteFunctions'        => 'Database/sqlabstraction/implementations/query_sqlite_function_implementations.php',
    'ezcQuerySubSelect'              => 'Database/sqlabstraction/query_subselect.php',
    'ezcQueryUpdate'                 => 'Database/sqlabstraction/query_update.php',
);
?>
