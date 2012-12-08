<?php
/**
 * Autoloader definition for the MvcMailTiein component.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.0.1
 * @filesource
 * @package MvcMailTiein
 */

return array(
    'ezcMvcMailTieinException'        => 'MvcMailTiein/exceptions/exception.php',
    'ezcMvcMailNoDataException'       => 'MvcMailTiein/exceptions/no_data.php',
    'ezcMvcMailBugzillaRequestFilter' => 'MvcMailTiein/request_filters/bugzilla.php',
    'ezcMvcMailRawRequest'            => 'MvcMailTiein/structs/request_raw_mail.php',
    'ezcMvcMailRequestParser'         => 'MvcMailTiein/request_parsers/mail.php',
);
?>
