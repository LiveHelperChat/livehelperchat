<?php
/**
 * Autoloader definition for the Execution component.
 *
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.1
 * @filesource
 * @package Execution
 */

return array(
    'ezcExecutionException'                   => 'Execution/exceptions/exception.php',
    'ezcExecutionAlreadyInitializedException' => 'Execution/exceptions/already_initialized.php',
    'ezcExecutionInvalidCallbackException'    => 'Execution/exceptions/invalid_callback.php',
    'ezcExecutionNotInitializedException'     => 'Execution/exceptions/not_initialized.php',
    'ezcExecutionWrongClassException'         => 'Execution/exceptions/wrong_class.php',
    'ezcExecutionErrorHandler'                => 'Execution/interfaces/execution_handler.php',
    'ezcExecution'                            => 'Execution/execution.php',
    'ezcExecutionBasicErrorHandler'           => 'Execution/handlers/basic_handler.php',
);
?>
