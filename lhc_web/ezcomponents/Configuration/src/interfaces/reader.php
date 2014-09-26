<?php
/**
 * File containing the ezcConfigurationReader class
 *
 * @package Configuration
 * @version 1.3.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This class provides the interface for readers of configuration objects of
 * type ezcConfiguration.
 *
 * The reader will unserialize the data from a given format e.g. from an INI
 * file or a given XML format which can later be written by a
 * ezcConfigurationWriter.
 *
 * The reader is meant to be initialized with setLocation() and setConfig() and
 * then a call to save(). It is also possible to initialize everything with the
 * init() function.
 *
 * <code>
 * $reader = new ezcConfigurationIniReader();
 * $reader->init( 'site', 'settings' );
 * $conf = $reader->load();
 * </code>
 *
 * Most readers allows even quicker initialization with the constructor. For
 * instance the INI reader allows you to specify the full path to the INI file.
 *
 * <code>
 * $reader = new ezcConfigurationIniReader( 'settings/site.ini' );
 * $conf = $reader->load();
 * </code>
 *
 * @package Configuration
 * @version 1.3.5
 */
abstract class ezcConfigurationReader
{
    /**
     * Returns the suffix used in the storage filename.
     *
     * @return string
     */
    abstract protected function getSuffix();

    /**
     * Initializes the reader with a $location and a $name.
     *
     * These values determine where the configuration will be serialized.
     *
     * @param string $location The main placement for the configuration. It is
     *               up to the specific reader to interpret this value.  This
     *               can for instance be used to determine the directory
     *               location for an INI file.
     * @param string $name The name for the configuration. It is up to the
     *               specific reader to interpret this value.  This can for
     *               instance be the basename for the INI file, so a value of
     *               'site' would create a file with name 'site.ini'.
     * @param array $options An associative array of options for the reader.
     *              Which options to use is determined by the specific reader
     *              class.
     * @return void
     */
    abstract public function init( $location, $name, array $options = array() );

    /**
     * Loads the current config object.
     *
     * The configuration can stored later with a ezcConfigurationWriter.
     *
     * @see config()
     *
     * @throws ezcConfigurationNoConfigException if there is no config
     *         object to be read from the location.
     * @throws ezcConfigurationInvalidSuffixException if the current
     *         location values cannot be used for reading.
     * @throws ezcConfigurationReadFailedException if the configuration
     *         could not be read from the given location.
     * @return ezcConfiguration
     */
    abstract public function load();

    /**
     * Returns the current configuration object.
     *
     * Returns the current configuration object if one is set, false otherwise.
     * The object will be set each time load() is called.
     *
     * @return ezcConfiguration
     */
    abstract public function getConfig();

    /**
     * Checks if the configuration exists.
     *
     * Returns true if a configuration exists at the location specified in the
     * constructor.
     *
     * @return bool
     */
    abstract public function configExists();

    /**
     * Returns the last modified timestamp.
     *
     * Returns false if the configuration does not exist.
     *
     * @return mixed
     */
    abstract public function getTimestamp();

    /**
     * Returns the current location string.
     *
     * @return string
     */
    abstract public function getLocation();

    /**
     * Returns the name of the configuration to be read.
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Returns the options for the reader.
     *
     * @return array
     */
    abstract public function getOptions();

    /**
     * Sets the options $options for the reader.
     *
     * The options will be used the next time the save() method is called.
     *
     * @param array $options An associative array of options for the reader.
     *              Which options to use is determined by the specific reader
     *              class.
     * @return void
     */
    abstract public function setOptions( $options );

    /**
     * Validates the configuration.
     *
     * Validates the configuration at the given location and returns the
     * validation result.
     *
     * If $strict is set it will not validate the file if it contains any
     * errors or warnings. If false it will allow warnings but not errors.
     *
     * @param bool $strict
     * @return ezcConfigurationValidationResult
     */
    abstract public function validate( $strict = false );
}
?>
