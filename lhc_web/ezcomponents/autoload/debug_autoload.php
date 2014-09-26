<?php
/**
 * Autoloader definition for the Debug component.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.2.1
 * @filesource
 * @package Debug
 */

return array(
    'ezcDebugException'                      => 'Debug/exceptions/exception.php',
    'ezcDebugOperationNotPermittedException' => 'Debug/exceptions/operation_not_permitted.php',
    'ezcDebugOutputFormatter'                => 'Debug/interfaces/formatter.php',
    'ezcDebugStacktraceIterator'             => 'Debug/interfaces/stacktrace_iterator.php',
    'ezcDebug'                               => 'Debug/debug.php',
    'ezcDebugHtmlFormatter'                  => 'Debug/formatters/html_formatter.php',
    'ezcDebugMemoryWriter'                   => 'Debug/writers/memory_writer.php',
    'ezcDebugOptions'                        => 'Debug/options.php',
    'ezcDebugPhpStacktraceIterator'          => 'Debug/stacktrace/php_iterator.php',
    'ezcDebugStructure'                      => 'Debug/structs/debug_structure.php',
    'ezcDebugSwitchTimerStruct'              => 'Debug/structs/switch_timer.php',
    'ezcDebugTimer'                          => 'Debug/debug_timer.php',
    'ezcDebugTimerStruct'                    => 'Debug/structs/timer.php',
    'ezcDebugVariableDumpTool'               => 'Debug/tools/dump.php',
    'ezcDebugXdebugStacktraceIterator'       => 'Debug/stacktrace/xdebug_iterator.php',
);
?>
