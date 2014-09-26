<?php
/**
 * File containing the ezcConfigurationManager class
 *
 * @package Configuration
 * @version 1.3.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcConfigurationManager provides easy access to application settings.
 *
 * Using this class removes the need to work with specific readers and writers
 * and also handles caching to speed up the process. This can be useful for
 * smaller applications which don't have too many settings and does not have
 * high memory or speed requirements.
 *
 * Many of the methods of this class that fetch settings accept one to three
 * parameters with the following names: $name - the configuration's name. For
 * the ini file reader, this is the name of the configuration file without the
 * path or the extension (.ini); $group - the name of the group in which the
 * setting is located; and $setting - the name of the setting itself.
 *
 * Before the manager can be used it must be configured so it knows where to
 * fetch the settings, this is usually at the start of the program.
 * <code>
 * $man = ezcConfigurationManager::getInstance();
 * $man->init( 'ezcConfigurationIniReader', 'settings', $options );
 * </code>
 *
 * After it is configured the rest of the code can simply access the global
 * instance and fetch the settings using getSetting().
 * <code>
 * $color = ezcConfigurationManager::getInstance()->getSetting( 'site', 'Colors', 'Background' );
 * </code>
 *
 * @see ezcConfiguration
 * @see ezcConfigurationReader
 * @see ezcConfigurationWriter
 *
 * @package Configuration
 * @mainclass
 * @version 1.3.5
 */
class ezcConfigurationManager
{
    /**
     * The name of the class to create readers from. This class must implement
     * the ezcConfigurationReader interface, if not the
     * ezcConfigurationInvalidReaderClassException exception is thrown when the
     * class with the class name in this property is created.
     *
     * @var ezcConfigurationReader
     */
    private $readerClass = null;

    /**
     * The main location of the configurations which is passed to each reader, this
     * is either the path on the filesystem or a PHP stream prefix.
     *
     * @var mixed
     */
    private $location = null;

    /**
     * Options for the readers, this is passed on when the reader is created
     * for the first time.
     *
     * @var array
     */
    private $options = array();

    /**
     * Maps the name of the configuration to the ezcConfiguration object.
     *
     * @var array
     */
    private $nameMap = array();

    /**
     * ezcConfigurationManager Singleton instance
     *
     * @var ezcConfigurationManager
     */
    static private $instance = null;

    /**
     * Constructs an empty manager.
     *
     * The constructor is private to prevent non-singleton.
     */
    private function __construct()
    {
    }

    /**
     * Returns the instance of the class ezcConfigurationManager.
     *
     * @return ezcConfigurationManager
     */
    public static function getInstance()
    {
        if ( is_null( self::$instance ) )
        {
            self::$instance = new ezcConfigurationManager();
            ezcBaseInit::fetchConfig( 'ezcInitConfigurationManager', self::$instance );
        }
        return self::$instance;
    }

    /**
     * Initializes the manager.
     *
     * Initializes the manager with the values which will be used by the configuration
     * reader. It sets the default location and reader options and which reader to
     * use by specifying the class name.
     *
     * @throws ezcConfigurationInvalidReaderClassException if the $readerClass
     *         does not exist or does not implement the ezcConfigurationReader
     *         interface.
     *
     * @param string $readerClass The name of the class to use as a
     *               configuration reader. This class must implement the
     *               ezcConfigurationReader interface.
     * @param string $location The main placement for the configuration. It is
     *               up to the specific reader to interpret this value.  This
     *               can for instance be used to determine the directory
     *               location for an INI file.
     * @param array  $options Options for the configuration reader, this is
     *               passed on the reader specified in $readerClass when it is
     *               created. Check the documentation for the specific reader
     *               to see which options it supports.
     * @return void
     */
    public function init( $readerClass, $location, array $options = array() )
    {
        // Check if the passed classname actually exists
        if ( !ezcBaseFeatures::classExists( $readerClass, true ) )
        {
            throw new ezcConfigurationInvalidReaderClassException( $readerClass );
        }

        // Check if the passed classname actually implements the interface.
        if ( !in_array( 'ezcConfigurationReader', class_parents( $readerClass ) ) )
        {
            throw new ezcConfigurationInvalidReaderClassException( $readerClass );
        }

        $this->readerClass = $readerClass;
        $this->location = $location;
        $this->options = $options;
    }

    /**
     * Resets the manager to the uninitialized state.
     *
     * @return void
     */
    public function reset()
    {
        $this->readerClass = null;
        $this->location = null;
        $this->options = array();
        $this->nameMap = array();
    }

    /**
     * Fetches a reader for the configuration $name.
     *
     * This method checks whether the configuration name was previously
     * requested. If it is not requested before, the method will construct a
     * new configuration reader based on the settings that were passed to this
     * class with the init() method.
     *
     * @throws ezcConfigurationUnknownConfigException
     *         if the configuration $name does not exist.
     * @throws ezcConfigurationManagerNotInitializedException
     *         if the manager has not been initialized with the init() method.
     *
     * @param string $name
     * @return ezcConfigurationReader The constructed reader
     */
    private function fetchReader( $name )
    {
        if ( $this->readerClass === null || $this->location === null )
        {
            throw new ezcConfigurationManagerNotInitializedException();
        }
        $key = "{$this->readerClass}-{$this->location}-{$name}";
        if ( !isset( $this->nameMap[$key] ) )
        {
            $className = $this->readerClass;
            $class = new $className();
            $class->init( $this->location, $name, $this->options );
            if ( $class->configExists() )
            {
                $class->load();
            }
            $this->nameMap[$key] = $class;
        }
        return $this->nameMap[$key];
    }

    /**
     * Returns whether the setting $setting exists in group $group in the
     * configuration named $name.
     *
     * @throws ezcConfigurationUnknownConfigException if the configuration does not
     *         exist.
     * @throws ezcConfigurationManagerNotInitializedException
     *         if the manager has not been initialized with the init() method.
     *
     * @param string $name
     * @param string $group
     * @param string $setting
     * @return bool
     */
    public function hasSetting( $name, $group, $setting )
    {
        $reader = $this->fetchReader( $name );
        $config = $reader->getConfig();
        if ( $config )
        {
            return $reader->getConfig()->hasSetting( $group, $setting );
        }
        else
        {
            throw new ezcConfigurationUnknownConfigException( $name );
        }
    }

    /**
     * Returns if the requested configuration file exists.
     *
     * Returns a boolean value indicating whether the configuration file exists
     * or not.
     *
     * @throws ezcConfigurationManagerNotInitializedException
     *         if the manager has not been initialized with the init() method.
     *
     * @param string $name
     * @return bool
     */
    public function hasConfigFile( $name )
    {
        $reader = $this->fetchReader( $name );
        $config = $reader->getConfig();
        
        return $config instanceof ezcConfiguration;
    }

    /**
     * Returns the configuration object for the configuration named $name.
     *
     * @throws ezcConfigurationUnknownConfigException if the configuration
     *         $name does not exist.
     * @throws ezcConfigurationManagerNotInitializedException
     *         if the manager has not been initialized with the init() method.
     *
     * @param string $name
     * @return ezcConfiguration
     */
    private function fetchConfig( $name )
    {
        $reader = $this->fetchReader( $name );
        if ( $reader->configExists() )
        {
            return $reader->getConfig();
        }
        else
        {
            throw new ezcConfigurationUnknownConfigException( $name );
        }
    }

    /**
     * Returns configuration setting.
     *
     * This method fetches a setting depending on the $name, $group and
     * $setting parameters. The $functionType parameter determines what type of
     * setting (mixed, boolean, number, string or array) should be retrieve. The
     * name that you have to pass is one 'setting', 'boolSetting',
     * 'numberSetting', 'stringSetting' or 'arraySetting'. This is not checked
     * as this is a private function.
     *
     * @throws ezcConfigurationUnknownConfigException if the configuration does
     *         not exist.
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @throws ezcConfigurationUnknownSettingException if the setting does not
     *         exist.
     *
     * @param string $functionType
     * @param string $name
     * @param string $group
     * @param string $setting
     * @return mixed
     */
    private function fetchSetting( $functionType, $name, $group, $setting )
    {
        return $this->fetchConfig( $name )->$functionType( $group, $setting );
    }

    /**
     * Returns the value of the setting $setting in group $group in the configuration
     * named $name.
     *
     * Uses the fetchSetting() method to fetch the value, this method can throw
     * exceptions.
     *
     * @throws ezcConfigurationUnknownConfigException if the configuration does
     *         not exist.
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @throws ezcConfigurationUnknownSettingException if the setting does not
     *         exist.
     *
     * @param string $name
     * @param string $group
     * @param string $setting
     * @return mixed
     */
    public function getSetting( $name, $group, $setting )
    {
        return $this->fetchSetting( 'getSetting', $name, $group, $setting );
    }

    /**
     * Returns whether the setting $group group exists in the
     * configuration named $name.
     * 
     * @param string $name 
     * @param string $group 
     * @return bool True if the group exist.
     */
    public function hasGroup( $name, $group )
    {
        $config = $this->fetchConfig( $name );
        return $config->hasGroup( $group );
    }

    /**
     * Returns all settings in group $group in the configuration named $name.
     * 
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @param string $name
     * @param string $group
     * @return array
     */
    public function getSettingsInGroup( $name, $group )
    {
        $config = $this->fetchConfig( $name );
        return $config->getSettingsInGroup( $group );
    }

    /**
     * Returns the value of the setting $setting in group $group in the configuration
     * named $name.
     *
     * Uses the fetchSetting() method to fetch the value, this method can throw
     * exceptions. This method also validates whether the value is actually a
     * boolean value.
     *
     * @see fetchSetting
     *
     * @throws ezcConfigurationUnknownConfigException if the configuration does
     *         not exist.
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @throws ezcConfigurationUnknownSettingException if the setting does not
     *         exist.
     * @throws ezcConfigurationSettingWrongTypeException if the setting value
     *         is not a boolean.
     * @param string $name
     * @param string $group
     * @param string $setting
     * @return bool
     */
    public function getBoolSetting( $name, $group, $setting )
    {
        return $this->fetchSetting( 'getBoolSetting', $name, $group, $setting );
    }

    /**
     * Returns the value of the setting $setting in group $group in the configuration
     * named $name.
     *
     * Uses the fetchSetting() method to fetch the value, this method can throw
     * exceptions. This method also validates whether the value is actually an
     * integer value.
     *
     * @see fetchSetting
     *
     * @throws ezcConfigurationUnknownConfigException if the configuration does
     *         not exist.
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @throws ezcConfigurationUnknownSettingException if the setting does not
     *         exist.
     * @throws ezcConfigurationSettingWrongTypeException if the setting value
     *         is not a number.
     * @param string $name
     * @param string $group
     * @param string $setting
     * @return int
     */
    public function getNumberSetting( $name, $group, $setting )
    {
        return $this->fetchSetting( 'getNumberSetting', $name, $group, $setting );
    }

    /**
     * Returns the value of the setting $setting in group $group in the configuration
     * named $name.
     *
     * Uses the fetchSetting() method to fetch the value, this method can throw
     * exceptions. This method also validates whether the value is actually a
     * string value.
     *
     * @see fetchSetting
     *
     * @throws ezcConfigurationUnknownConfigException if the configuration does
     *         not exist.
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @throws ezcConfigurationUnknownSettingException if the setting does not
     *         exist.
     * @throws ezcConfigurationSettingWrongTypeException if the setting value
     *         is not a string.
     * @param string $name
     * @param string $group
     * @param string $setting
     * @return string
     */
    public function getStringSetting( $name, $group, $setting )
    {
        return $this->fetchSetting( 'getStringSetting', $name, $group, $setting );
    }

    /**
     * Returns the value of the setting $setting in group $group in the configuration
     * named $name.
     *
     * Uses the fetchSetting() method to fetch the value, this method can throw
     * exceptions. This method also validates whether the value is actually an
     * array value.
     *
     * @see fetchSetting
     *
     * @throws ezcConfigurationUnknownConfigException if the configuration does
     *         not exist.
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @throws ezcConfigurationUnknownSettingException if the setting does not
     *         exist.
     * @throws ezcConfigurationSettingWrongTypeException if the setting value
     *         is not an array.
     * @param string $name
     * @param string $group
     * @param string $setting
     * @return array
     */
    public function getArraySetting( $name, $group, $setting )
    {
        return $this->fetchSetting( 'getArraySetting', $name, $group, $setting );
    }

    /**
     * Returns the values of the settings $settings in group $group in the configuration
     * named $name.
     *
     * For each of the setting names passed in the $settings array it will
     * return the setting in the returned array with the name of the setting as
     * key.
     *
     * @see getSettingsAsList
     *
     * @throws ezcConfigurationUnknownConfigException if the configuration does
     *         not exist.
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @throws ezcConfigurationUnknownSettingException if one or more of the
     *         settings do not exist.
     * @param string $name
     * @param string $group
     * @param array $settings
     * @return array
     */
    public function getSettings( $name, $group, array $settings )
    {
        $config = $this->fetchConfig( $name );
        return $config->getSettings( $group, $settings );
    }

    /**
     * Returns the values of the settings $settings in group $group as an array.
     *
     * For each of the setting names passed in the $settings array it will only
     * return the values of the settings in the returned array, and not include
     * the name of the setting as the array's key.
     *
     * @see getSettings
     *
     * @throws ezcConfigurationUnknownConfigException if the configuration does
     *         not exist.
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @throws ezcConfigurationUnknownSettingException if one or more of the
     *         settings do not exist.
     * @param string $name
     * @param string $group
     * @param array $settings
     * @return array
     */
    public function getSettingsAsList( $name, $group, array $settings )
    {
        $return = array();

        $settings = $this->getSettings( $name, $group, $settings );

        foreach ( $settings as $setting )
        {
            $return[] = $setting;
        }
        return $return;
    }

    /**
     * Returns true if the configuration named $name exists.
     *
     * @throws ezcConfigurationManagerNotInitializedException
     *         if the manager has not been initialized with the init() method.
     *
     * @param string $name
     * @return bool
     */
    public function exists( $name )
    {
        $reader = $this->fetchReader( $name );
        return $reader->configExists();
    }
}
?>
