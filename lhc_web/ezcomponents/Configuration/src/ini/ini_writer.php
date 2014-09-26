<?php
/**
 * File containing the ezcConfigurationIniWriter class
 *
 * @package Configuration
 * @version 1.3.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This class provides functionality for writing ezcConfiguration objects into
 * INI files.
 *
 * A typical usage is to create the writer object and pass the filepath in the
 * constructor:
 * <code>
 * // $conf is an ezcConfiguration object
 * $writer = new ezcConfigurationIniWriter( "settings/site.ini", $conf );
 * $writer->save();
 * </code>
 * That makes the class figure out the location and name values automatically.
 *
 * Or generally use the init() function:
 * <code>
 * // $conf is an ezcConfiguration object
 * $writer = new ezcConfigurationIniWriter();
 * $writer->init( "settings", "site", $conf );
 * $writer->save();
 * </code>
 *
 * For more information on file based configurations see {@link
 * ezcConfigurationFileWriter}.
 *
 * This class uses exceptions and will throw them when the conditions for the
 * operation fails somehow.
 *
 * @package Configuration
 * @version 1.3.5
 * @mainclass
 */
class ezcConfigurationIniWriter extends ezcConfigurationFileWriter
{
    /**
     * Returns 'ini'. The suffix used in the storage filename.
     *
     * @return string
     */
    protected function getSuffix()
    {
        return 'ini';
    }

    /**
     * Writes the settings and comments to disk
     *
     * This method loops over all groups and settings (defined with the
     * $settings array) and writes those to disk.  For the settings itself it
     * will call writeSetting() which also detects arrays and handles those
     * recursively.  See {@link ezcConfiguration::$settings} and {@link
     * ezcConfiguration::$comments} for the formats of those arrays.
     *
     * @param resource $fp The filepointer of the file to write.
     * @param array $settings The structure containing settings.
     * @param array $comments The structure containing the comments for the
     *                        settings.
     */
    protected function writeSettings( $fp, array $settings, array $comments = array() )
    {
        /* Check if there are any settings */
        if ( !count( $settings ) )
        {
            return;
        }
        foreach ( $settings as $groupName => $groupSettings )
        {
            /* Write group comment */
            if ( count( $comments ) && isset( $comments[$groupName]['#'] ) )
            {
                fwrite( $fp, '#' . implode( "\n#", explode( "\n", $comments[$groupName]['#'] ) ) . "\n" );
            }

            /* Write group header */
            fwrite( $fp, "[$groupName]\n" );

            /* Write settings */
            foreach ( $groupSettings as $settingName => $settingValue )
            {
                $commentValue = false;
                if ( isset( $comments[$groupName][$settingName] ) )
                {
                    $commentValue = $comments[$groupName][$settingName];
                }
                self::writeSetting( $fp, $settingName, $settingValue, $commentValue );
            }

            /* Add extra whitespace */
            fwrite( $fp, "\n" );
        }
    }

    /**
     * Writes the setting $settingName to $fp with the value $settingValue and comment
     * $commentValue.
     *
     * This method detects a setting value's type and writes the setting in a
     * format according to this type. It also handles arrays recursively.
     *
     * @param resource(filepointer) $fp
     * @param string $settingName
     * @param mixed $settingValue
     * @param mixed $commentValue
     */
    private static function writeSetting( $fp, $settingName, $settingValue, $commentValue )
    {
        $type = gettype( $settingValue );

        /* Write setting comment */
        if ( $type != 'array' )
        {
            if ( $commentValue )
            {
                fwrite( $fp, '#' . implode( "\n#", explode( "\n", $commentValue ) ) . "\n" );
            }
        }

        /* Write setting value */
        switch ( $type )
        {
            case 'string':
                if ( strpbrk( $settingValue, "'\"\0\\" ) === false &&
                    !in_array( $settingValue, array( 'true', 'false' ) ) &&
                    !preg_match( '@^-?(([1-9][0-9]*)|(0))$@', $settingValue ) &&
                    !preg_match( '@^0x([0-9a-f]+)$@i', $settingValue ) &&
                    !preg_match( '@^0([0-7]+)$@i', $settingValue ) &&
                    !preg_match( '@^(([0-9]*\.[0-9]+)|([0-9]+(\.[0-9]*)?))(e[+-]?[0-9]+)?$@i', $settingValue ) &&
                    $settingValue[0] !== '"' &&
                    $settingValue[strlen( $settingValue ) - 1] !== '"' )
                {
                    fwrite( $fp, "{$settingName} = $settingValue\n" );
                }
                else
                {
                    fwrite( $fp, "{$settingName} = \"" . addslashes( $settingValue ) . "\"\n" );
                }
                break;
            case 'boolean':
                fwrite( $fp, "{$settingName} = " . ( $settingValue ? 'true' : 'false' ) . "\n" );
                break;
            case 'integer':
                fwrite( $fp, "{$settingName} = {$settingValue}\n" );
                break;
            case 'double':
                fwrite( $fp, "{$settingName} = " . sprintf( "%.8f", $settingValue ) . "\n" );
                break;
            case 'array':
                foreach ( $settingValue as $settingKey => $settingElement )
                {
                    $commentSettingValue = false;
                    if ( isset( $commentValue[$settingKey] ) )
                    {
                        $commentSettingValue = $commentValue[$settingKey];
                    }
                    self::writeSetting( $fp, "{$settingName}[{$settingKey}]", $settingElement, $commentSettingValue );
                }
                break;
        }
    }
}
?>
