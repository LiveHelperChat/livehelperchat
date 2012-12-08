<?php
/**
 * Autoloader definition for the Configuration component.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.3.5
 * @filesource
 * @package Configuration
 */

return array(
    'ezcConfigurationException'                      => 'Configuration/exceptions/exception.php',
    'ezcConfigurationGroupExistsAlreadyException'    => 'Configuration/exceptions/group_exists_already.php',
    'ezcConfigurationInvalidReaderClassException'    => 'Configuration/exceptions/invalid_reader_class.php',
    'ezcConfigurationInvalidSuffixException'         => 'Configuration/exceptions/invalid_suffix.php',
    'ezcConfigurationManagerNotInitializedException' => 'Configuration/exceptions/manager_no_init.php',
    'ezcConfigurationNoConfigException'              => 'Configuration/exceptions/no_config.php',
    'ezcConfigurationNoConfigObjectException'        => 'Configuration/exceptions/no_config_object.php',
    'ezcConfigurationParseErrorException'            => 'Configuration/exceptions/parse_error.php',
    'ezcConfigurationReadFailedException'            => 'Configuration/exceptions/read_failed.php',
    'ezcConfigurationSettingWrongTypeException'      => 'Configuration/exceptions/setting_wrong_type.php',
    'ezcConfigurationSettingnameNotStringException'  => 'Configuration/exceptions/settingname_not_string.php',
    'ezcConfigurationUnknownConfigException'         => 'Configuration/exceptions/unknown_config.php',
    'ezcConfigurationUnknownGroupException'          => 'Configuration/exceptions/unknown_group.php',
    'ezcConfigurationUnknownSettingException'        => 'Configuration/exceptions/unknown_setting.php',
    'ezcConfigurationWriteFailedException'           => 'Configuration/exceptions/write_failed.php',
    'ezcConfigurationReader'                         => 'Configuration/interfaces/reader.php',
    'ezcConfigurationWriter'                         => 'Configuration/interfaces/writer.php',
    'ezcConfigurationFileReader'                     => 'Configuration/file_reader.php',
    'ezcConfigurationFileWriter'                     => 'Configuration/file_writer.php',
    'ezcConfiguration'                               => 'Configuration/configuration.php',
    'ezcConfigurationArrayReader'                    => 'Configuration/array/array_reader.php',
    'ezcConfigurationArrayWriter'                    => 'Configuration/array/array_writer.php',
    'ezcConfigurationIniItem'                        => 'Configuration/structs/ini_item.php',
    'ezcConfigurationIniParser'                      => 'Configuration/ini/ini_parser.php',
    'ezcConfigurationIniReader'                      => 'Configuration/ini/ini_reader.php',
    'ezcConfigurationIniWriter'                      => 'Configuration/ini/ini_writer.php',
    'ezcConfigurationManager'                        => 'Configuration/configuration_manager.php',
    'ezcConfigurationValidationItem'                 => 'Configuration/structs/validation_item.php',
    'ezcConfigurationValidationResult'               => 'Configuration/validation_result.php',
);
?>
