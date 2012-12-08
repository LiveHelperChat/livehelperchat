<?php
/**
 * Autoloader definition for the SystemInformation component.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.0.8
 * @filesource
 * @package SystemInformation
 */

return array(
    'ezcSystemInfoException'                 => 'SystemInformation/exceptions/exception.php',
    'ezcSystemInfoReaderCantScanOSException' => 'SystemInformation/exceptions/reader_cant_scan_os.php',
    'ezcSystemInfoReader'                    => 'SystemInformation/interfaces/reader.php',
    'ezcSystemInfo'                          => 'SystemInformation/info.php',
    'ezcSystemInfoAccelerator'               => 'SystemInformation/structs/accelerator.php',
    'ezcSystemInfoFreeBsdReader'             => 'SystemInformation/readers/freebsd.php',
    'ezcSystemInfoLinuxReader'               => 'SystemInformation/readers/linux.php',
    'ezcSystemInfoMacReader'                 => 'SystemInformation/readers/mac.php',
    'ezcSystemInfoWindowsReader'             => 'SystemInformation/readers/windows.php',
);
?>
