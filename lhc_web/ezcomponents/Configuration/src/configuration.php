<?php
/**
 * File containing the ezcConfiguration class
 *
 * @package Configuration
 * @version 1.3.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This class provides persistent platform-independent and format independent
 * application settings.
 *
 * A typical usage for retrieving the settings in an application:
 * <code>
 * $conf = new ezcConfiguration();
 * if ( $conf->hasSettings( 'Colors', array( 'Background', 'Foreground' ) ) )
 * {
 *     $colorBg = $conf->getSetting( 'Colors', 'Background' );
 *     $colorFg = $conf->getIntSetting( 'Colors', 'Foreground' );
 * }
 * </code>
 *
 * A typical usage for storing the settings of an application:
 * <code>
 * // $conf contains an ezcConfiguration object
 * $conf->setSetting( 'Colors', 'Background', 'blue' );
 * $conf->setSetting( 'Fonts', 'PointSize', 12 );
 *
 * $conf->setSettings( 'DB',
 *                     array( 'Host', 'User', 'Password' ),
 *                     array( 'localhost', 'dr', 'eXaMpLe' ) );
 * </code>
 *
 * The current groups and their settings can be examined with:
 * <code>
 * // $conf contains an ezcConfiguration object
 * $groups = $conf->getGroupNames();
 * foreach ( $groups as $group )
 * {
 *    $settings = $conf->getSettingNames( $group );
 *    foreach ( $settings as $setting )
 *    {
 *        $value = $conf->getSetting( $group, $setting );
 *        print "$group:$setting=$value\n";
 *    }
 * }
 * </code>
 *
 * Alternatively all settings and their values can be returned in one go:
 * <code>
 * // $conf contains an ezcConfiguration object
 * $settings = $conf->getSettingsInGroup( 'Colors' );
 * foreach ( $settings as $setting => $value )
 * {
 *    print "$setting=$value\n";
 * }
 * </code>
 *
 * Or quering the entire configuration settings with getAllSettings():
 * <code>
 * // $conf contains an ezcConfiguration object
 * $allSettings = $conf->getAllSettings( 'Colors' );
 * foreach ( $allSettings as $group => $settings )
 * {
 *    foreach ( $settings as $setting => $value )
 *    {
 *        print "$group:$setting=$value\n";
 *    }
 * }
 * </code>
 *
 * Fetching specific settings is done using getSetting() or if you want to ensure
 * that it is a specific type use getBoolSetting(), getIntSetting(), getFloatSetting(),
 * getNumberSetting(), getStringSetting() or getArraySetting(). Fetching multiple values
 * is possible with getSettings().
 *
 * Removing entries is possible with removeSetting(), removeSettings(),
 * removeGroup() and removeAllSettings().
 *
 * In addition all entries can queried for existance with hasSetting(),
 * hasSettings() and hasGroup().
 *
 * Reading and writing is done by the various implemenations of
 * ezcConfigurationReader and ezcConfigurationWriter respectively. They provide
 * access to different configuration formats and storage types, for instance
 * INI files and database storage.
 *
 * If the application does not need to have such finegrained control over the
 * settings the ezcConfigurationManager class might be of interest.
 *
 * @package Configuration
 * @version 1.3.5
 * @mainclass
 */
class ezcConfiguration
{
    /**
     * Contains all the setting groups in the configuration.
     *
     * The name of the group is the key and the value is the settings array. An
     * example of such an array is:
     *
     * <pre>
     * array(
     *     '3D' => array(
     *         'Decimal' => array( 42, 0 ),
     *         'Array' =>  array(
     *             'Decimal' => array( 'a' => 42, 'b' => 0 ),
     *             'Mixed' => array( 'b' => false, 2 => "Derick \"Tiger\" Rethans" ),
     *         ),
     *     ),
     * );
     * </pre>
     *
     * In this array there is one setting '3D' which value is an array with two
     * elements (with the keys 'Decimal' and 'Array'). Each of those elements'
     * values is another array. For example for the 3D:Decimal setting the
     * value is an array with the elements 42 and 0.
     *
     * @var array(string=>mixed)
     */
    private $settings = array();

    /**
     * Contains all the comments that exist for the configuration.
     *
     * The structure of the comments array is the same as the $settings array
     * but will only contain entries for settings which have comments. For
     * comments for groups a special syntax is used. An example of such an
     * array:
     *
     * <pre>
     * array(
     *     'TheOnlyGroup' => array(
     *         '#' => "Just one group",
     *         'Setting1' => " This setting sucks",
     *         'MultiRow' => " Multi\n row\n comment",
     *     )
     * );
     * </pre>
     *
     * For each settings group there is an array with the comments. Each
     * setting in that group that has a comment has an entry in this array. In
     * the example above the group 'TheOnlyGroup' has two settings that have a
     * comment ('Setting1' and 'MultiRow'). The comment for the group itself
     * can be found in an element with the name '#'.
     *
     * @var array(string=>array)
     */
    private $comments = array();

    /**
     * Whether the original data passed to the constructor has been modified.
     *
     * This variable is set to true when a method is called that modifies the
     * settings array.
     *
     * @var bool
     */
    private $isModified = false;

    /**
     * Constructs the configuration object.
     *
     * Initializes the configuration object with the groups and the comments.
     * The $settings array contains all the setting groups. The $comments array
     * has the same format. See {@link ezcConfiguration::$settings} and {@link
     * ezcConfiguration::$comments} for an example of the layout.
     *
     * @param array $settings
     * @param array $comments
     */
    public function __construct( $settings = array(), $comments = array() )
    {
        $this->settings = $settings;
        $this->comments = $comments;
    }

    /**
     * Returns true if setting $setting exists within the group $group.
     *
     * @param string $group
     * @param string $setting
     * @return bool
     */
    public function hasSetting( $group, $setting )
    {
        if ( !is_string( $setting ) )
        {
            throw new ezcConfigurationSettingnameNotStringException( $setting );
        }
        if ( isset( $this->settings[$group] ) && isset( $this->settings[$group][$setting] ) )
        {
            return true;
        }
        return false;
    }

    /**
     * Checks whether a specific $group and $setting exist.
     *
     * @param string $group
     * @param string $setting
     *
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @throws ezcConfigurationUnknownSettingException if the setting does not
     *         exist.
     * @return mixed
     */
    private function assertGroupSetting( $group, $setting )
    {
        if ( !$this->hasGroup( $group ) )
        {
            throw new ezcConfigurationUnknownGroupException( $group );
        }
        if ( !$this->hasSetting( $group, $setting ) )
        {
            throw new ezcConfigurationUnknownSettingException( $group, $setting );
        }
    }

    /**
     * Returns the value of setting $setting located in group $group.
     *
     * @param string $group
     * @param string $setting
     *
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @throws ezcConfigurationUnknownSettingException if the setting does not
     *         exist.
     * @return mixed
     */
    public function getSetting( $group, $setting )
    {
        $this->assertGroupSetting( $group, $setting );
        return $this->settings[$group][$setting];
    }

    /**
     * Returns the comment belonging to setting $setting located in group $group.
     *
     * This method returns the comment belonging to the setting that is passed.
     * If there is no comment for this specific setting it returns false.
     *
     * @param string $group
     * @param string $setting
     *
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @throws ezcConfigurationUnknownSettingException if the setting does not
     *         exist.
     * @return string
     */
    public function getComment( $group, $setting )
    {
        $this->assertGroupSetting( $group, $setting );
        if ( isset( $this->comments[$group][$setting] ) )
        {
            return $this->comments[$group][$setting];
        }
        return false;
    }

    /**
     * Returns the value of the setting $setting in group $group.
     *
     * Uses the getSetting() method to fetch the value, this method can throw
     * exceptions. This method also validates whether the value is actually a
     * boolean value.
     *
     * @see setting
     *
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @throws ezcConfigurationUnknownSettingException if the setting does not
     *         exist.
     * @throws ezcConfigurationSettingWrongTypeException if the setting value
     *         is not a boolean.
     * @param string $group
     * @param string $setting
     * @return bool
     */
    public function getBoolSetting( $group, $setting )
    {
        $value = $this->getSetting( $group, $setting );
        $type = gettype( $value );
        if ( $type != 'boolean' )
        {
            throw new ezcConfigurationSettingWrongTypeException( $group, $setting, 'boolean', $type );
        }
        return $value;
    }

    /**
     * Returns the value of the setting $setting in group $group.
     *
     * Uses the getSetting() method to fetch the value, this method can throw
     * exceptions. This method also validates whether the value is actually an
     * integer or float value.
     *
     * @see setting
     *
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @throws ezcConfigurationUnknownSettingException if the setting does not
     *         exist.
     * @throws ezcConfigurationSettingWrongTypeException if the setting value
     *         is not an integer or a float.
     * @param string $group
     * @param string $setting
     * @return mixed
     */
    public function getNumberSetting( $group, $setting )
    {
        $value = $this->getSetting( $group, $setting );
        $type = gettype( $value );
        if ( $type != 'double' && $type != 'integer' )
        {
            throw new ezcConfigurationSettingWrongTypeException( $group, $setting, 'double or integer', $type );
        }
        return $value;
    }

    /**
     * Returns the value of the setting $setting in group $group.
     *
     * Uses the getSetting() method to fetch the value, this method can throw
     * exceptions. This method also validates whether the value is actually a
     * string value.
     *
     * @see setting
     *
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @throws ezcConfigurationUnknownSettingException if the setting does not
     *         exist.
     * @throws ezcConfigurationSettingWrongTypeException if the setting value
     *         is not a string.
     * @param string $group
     * @param string $setting
     * @return string
     */
    public function getStringSetting( $group, $setting )
    {
        $value = $this->getSetting( $group, $setting );
        $type = gettype( $value );
        if ( !is_string( $value ) )
        {
            throw new ezcConfigurationSettingWrongTypeException( $group, $setting, 'string', $type );
        }
        return $value;
    }

    /**
     * Returns the value of the setting $setting in group $group.
     *
     * Uses the getSetting() method to fetch the value, this method can throw
     * exceptions. This method also validates whether the value is actually an
     * array value.
     *
     * @see setting
     *
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @throws ezcConfigurationUnknownSettingException if the setting does not
     *         exist.
     * @throws ezcConfigurationSettingWrongTypeException if the setting value
     *         is not an array.
     * @param string $group
     * @param string $setting
     * @return array
     */
    public function getArraySetting( $group, $setting )
    {
        $value = $this->getSetting( $group, $setting );
        $type = gettype( $value );
        if ( $type != 'array' )
        {
            throw new ezcConfigurationSettingWrongTypeException( $group, $setting, 'array', $type );
        }
        return $value;
    }

    /**
     * Sets the setting $setting in group $group to $value.
     *
     * If the setting does not already exists it will be created.
     *
     * @param string $group
     * @param string $setting
     * @param mixed $value The value of the setting, can be any PHP type except
     *                     a resource or an object.
     * @param string $comment The comment belonging to the setting
     * @return void
     */
    public function setSetting( $group, $setting, $value, $comment = null )
    {
        $this->settings[$group][$setting] = $value;
        if ( $comment !== null )
        {
            $this->comments[$group][$setting] = $comment;
        }
        $this->isModified = true;
    }

    /**
     * Removes the setting $setting from the group $group.
     *
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @throws ezcConfigurationUnknownSettingException if the setting does not
     *         exist.
     *
     * @param string $group
     * @param string $setting
     * @return void
     */
    public function removeSetting( $group, $setting )
    {
        $this->assertGroupSetting( $group, $setting );
        unset( $this->settings[$group][$setting] );
        $this->isModified = true;
    }

    /**
     * Returns true if all the specified settings $settings exists within $group.
     *
     * @param string $group
     * @param array(string) $settings
     * @return bool
     */
    public function hasSettings( $group, array $settings )
    {
        if ( !$this->hasGroup( $group ) )
        {
            return false;
        }

        foreach ( $settings as $settingName )
        {
            if ( !$this->hasSetting( $group, $settingName ) )
            {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns the values of the settings $settings in group $group as an array.
     *
     * For each of the setting names passed in the $settings array it will
     * return the setting in the returned array with the name of the setting as
     * key.
     *
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @throws ezcConfigurationUnknownSettingException if one or more of the
     *         settings do not exist.
     * @param string $group
     * @param array $settings
     * @return array
     */
    public function getSettings( $group, array $settings )
    {
        $return = array();

        if ( !$this->hasGroup( $group ) )
        {
            throw new ezcConfigurationUnknownGroupException( $group );
        }

        foreach ( $settings as $settingName )
        {
            if ( !$this->hasSetting( $group, $settingName ) )
            {
                throw new ezcConfigurationUnknownSettingException( $group, $settingName );
            }
            $return[$settingName] = $this->getSetting( $group, $settingName );
        }
        return $return;
    }

    /**
     * Returns the comments belonging to the specified settings $settings as an array.
     *
     * This method returns the comments belonging to the settings that are
     * passed.  If there is no comment for each specific setting the returning
     * array element will have a value of false.
     *
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @throws ezcConfigurationUnknownSettingException if one or more of the
     *         settings do not exist.
     * @param string $group The name of the group the settings will be located
     *                      in.
     * @param array $settings
     * @return array
     */
    public function getComments( $group, array $settings )
    {
        $return = array();

        if ( !$this->hasGroup( $group ) )
        {
            throw new ezcConfigurationUnknownGroupException( $group );
        }

        foreach ( $settings as $settingName )
        {
            if ( !$this->hasSetting( $group, $settingName ) )
            {
                throw new ezcConfigurationUnknownSettingException( $group, $settingName );
            }
            $value = false;
            if ( isset( $this->comments[$group][$settingName] ) )
            {
                $value = $this->comments[$group][$settingName];
            }
            $return[$settingName] = $value;
        }
        return $return;
    }

    /**
     * Sets the settings $setting in group $group to $values.
     *
     * If the settings do not already exists it will be created.
     *
     * @param string $group
     * @param array(string) $settings
     * @param array(mixed)  $values
     * @param array(string) $comments The comment belonging to the setting
     * @return void
     */
    public function setSettings( $group, $settings, $values, $comments = null )
    {
        foreach ( $settings as $settingKey => $settingName )
        {
            $this->settings[$group][$settingName] = $values[$settingKey];
            if ( $comments !== null && isset( $comments[$settingKey] ) && $comments[$settingKey] != null )
            {
                $this->comments[$group][$settingName] = $comments[$settingKey];
            }
        }
        $this->isModified = true;
    }

    /**
     * Removes the settings $settings from the group $group.
     *
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @throws ezcConfigurationUnknownSettingException if one or more of the
     *         settings do not exist.
     * @param string $group
     * @param array(string) $settings
     * @return void
     */
    public function removeSettings( $group, array $settings )
    {
        if ( !$this->hasGroup( $group ) )
        {
            throw new ezcConfigurationUnknownGroupException( $group );
        }

        foreach ( $settings as $settingName )
        {
            if ( !$this->hasSetting( $group, $settingName ) )
            {
                throw new ezcConfigurationUnknownSettingException( $group, $settingName );
            }

            unset( $this->settings[$group][$settingName] );
            if ( isset( $this->comments[$group][$settingName] ) )
            {
                unset( $this->comments[$group][$settingName] );
            }
        }
        $this->isModified = true;
    }

    /**
     * Returns the names of all the groups as an array.
     *
     * @return array
     */
    public function getGroupNames()
    {
        $groups = array();

        foreach ( $this->settings as $groupName => $dummy )
        {
            $groups[] = $groupName;
        }
        return $groups;
    }

    /**
     * Returns true if the group $group exists.
     *
     * @param string $group
     * @return bool
     */
    public function hasGroup( $group )
    {
        if ( !isset( $this->settings[$group] ) || !is_array( $this->settings[$group] ) )
        {
            return false;
        }
        return true;
    }

    /**
     * Returns the names of all settings in the group $group.
     *
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @param string $group
     * @return array(string)
     */
    public function getSettingNames( $group )
    {
        $return = array();
        if ( !$this->hasGroup( $group ) )
        {
            throw new ezcConfigurationUnknownGroupException( $group );
        }
        foreach ( $this->settings[$group] as $settingName => $dummy ) {
            $return[] = $settingName;
        }
        return $return;
    }

    /**
     * Returns all settings in the group $group.
     *
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @param string $group
     * @return array(string=>mixed)
     */
    public function getSettingsInGroup( $group )
    {
        if ( !$this->hasGroup( $group ) )
        {
            throw new ezcConfigurationUnknownGroupException( $group );
        }
        return $this->settings[$group];
    }

    /**
     * Adds a the group $group with the comment $comment the settings.
     *
     * @throws ezcConfigurationGroupExistsAlreadyException if the group that
     *         you are trying to add already exists.
     * @param string $group
     * @param string $comment
     * @return void
     */
    public function addGroup( $group, $comment = null )
    {
        if ( $this->hasGroup( $group ) )
        {
            throw new ezcConfigurationGroupExistsAlreadyException( $group );
        }
        $this->settings[$group] = array();
        if ( $comment !== null )
        {
            $this->comments[$group]['#'] = $comment;
        }
        $this->isModified = true;
    }

    /**
     * Removes the group $group from the settings.
     *
     * @param string $group
     * @throws ezcConfigurationUnknownGroupException if the group does not
     *         exist.
     * @return void
     */
    public function removeGroup( $group )
    {
        if ( !$this->hasGroup( $group ) )
        {
            throw new ezcConfigurationUnknownGroupException( $group );
        }
        unset( $this->settings[$group] );
        if ( isset( $this->comments[$group] ) )
        {
            unset( $this->comments[$group] );
        }
        $this->isModified = true;
    }

    /**
     * Returns all the groups and their settings and values.
     *
     * The returned array looks like:
     * <code>
     * array( 'group1' => array( 'setting1' => 'value1',
     *                           'setting2' => 'value2' ),
     *        'group2' => array( 'setting3' => 'value3' ) );
     * </code>
     *
     * @return array(array)
     */
    public function getAllSettings()
    {
        return $this->settings;
    }

    /**
     * Returns all the groups and their settings comments as an array.
     *
     * The returned array looks like:
     * <code>
     * array( 'group1' => array( '#' => 'groupcomment',
     *                           'setting1' => 'comment1',
     *                           'setting2' => 'comment2' ),
     *        'group2' => array( 'setting3' => 'comment3' ) );
     * </code>
     *
     * @return array(array)
     */
    public function getAllComments()
    {
        return $this->comments;
    }

    /**
     * Removes all groups, settings, values and comments.
     *
     * @return void
     */
    public function removeAllSettings()
    {
        $this->settings = array();
        $this->comments = array();
        $this->isModified = true;
    }

    /**
     * Returns true if the configuration has been modified since it was initialized
     * with the constructor.
     *
     * @return bool
     */
    public function isModified()
    {
        return $this->isModified;
    }
}
?>
