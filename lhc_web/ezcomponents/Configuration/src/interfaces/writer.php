<?php
/**
 * File containing the ezcConfigurationWriter class
 *
 * @package Configuration
 * @version 1.3.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This class provides the interface for writers of configuration objects of type
 * ezcConfiguration.
 *
 * The writer will serialize the data to a given format e.g. to an INI file or a
 * given XML format which can later be read by a ezcConfigurationReader.
 *
 * The writer is meant to be initialized with setLocation() and setConfig() and
 * then a call to save(). It is also possible to initialize everything with the
 * init() function.
 *
 * <code>
 * $writer = new ezcConfigurationIniWriter();
 * $writer->setConfig( $configrationObject );
 * $writer->setLocation( 'site', 'settings' );
 * $writer->save();
 * <code>
 *
 * Classes that implements this interface are adviced to create a constructor with
 * all the initialization as parameter to make it easier to use the class. For
 * instance this could transform the above example into:
 *
 * <code>
 * $writer = new ezcConfigurationIniWriter( $conf, 'site', 'settings' );
 * $writer->save();
 * </code>
 *
 * @package Configuration
 * @version 1.3.5
 */
abstract class ezcConfigurationWriter
{
    /**
     * Returns the suffix used in the storage filename.
     *
     * @return string
     */
    abstract protected function getSuffix();

    /**
     * Initializes the writer with a $location and a $name.
     *
     * These values determine where the configuration will be
     * serialized.
     *
     * The location string can be used to determine the directory
     * location for an INI file.
     *
     * The name parameter can be the basename for the INI file, so
     * a value of 'site' would create a file with name 'site.ini'.
     *
     * @param string $location The main placement for the configuration. It is
     *                         up to the specific writer to interpret this
     *                         value.
     * @param string $name The name for the configuration. It is up to the
     *                     specific writer to interpret this value. For a file writer
     *                     it could be the basename for the INI file, so a value of
     *                     'site' would create a file with name 'site.ini'.
     * @param ezcConfiguration $config The current configuration object which
     *                                 should be serialized by the current
     *                                 writer.
     * @param array $options An associative array of options for the writer.
     *                       Which options to use is determined by the specific
     *                       writer class.
     * @return void
     */
    abstract public function init( $location, $name, ezcConfiguration $config, $options = array() );

    /**
     * Saves the current config object.
     *
     * The configuration retrieved later with a ezcConfigurationReader.
     *
     * @throws ezcConfigurationNoConfigException if there is no config
     *         object set to be written to the location.
     * @throws ezcConfigurationInvalidSuffixException if the current
     *         location values cannot be used for writing.
     * @throws ezcConfigurationReadFailedException if the configuration
     *         could not be written to the given location.
     * @return void
     */
    abstract public function save();

    /**
     * Sets the configuration object that will be used for the next call to save().
     *
     * Pass false if you wish to remove the current configuration object.
     *
     * @param ezcConfiguration $config
     * @return void
     */
    abstract public function setConfig( ezcConfiguration $config );

    /**
     * Returns the current location string.
     *
     * @return string
     */
    abstract public function getLocation();

    /**
     * Returns the current name for the configuration to be written.
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Returns the current options for the writer.
     *
     * @return array
     */
    abstract public function getOptions();

    /**
     * Sets the options for the writer.
     *
     * The options will be used the next time the save() method is called. The
     * $options array is an associative array with the options for the writer.
     * It depends on the specific writer which options are allowed here.
     *
     * @param array $options
     */
    abstract public function setOptions( $options );
}
?>
