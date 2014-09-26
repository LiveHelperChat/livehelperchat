<?php
/**
 * File containing the ezcConfigurationFileWriter class
 *
 * @package Configuration
 * @version 1.3.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcConfigurationFileWriter class provides the functionality for writing
 * file based configuration formats.
 *
 * This class implements most of the interface of ezcConfigurationWriter and
 * makes it easier to work on file based configuration. All methods except save()
 * are implemented by this class so a subclass only needs to handle the actual
 * serialization.
 *
 * @package Configuration
 * @version 1.3.5
 */
abstract class ezcConfigurationFileWriter extends ezcConfigurationWriter
{
    /**
     * The path to the file which will contain the serialized configuration data.
     *
     * @var string
     */
    protected $path = '';

    /**
     * Contains the file permissions for the file to write the INI settings to.
     *
     * @var int
     */
    protected $permissions = 0666;

    /**
     * The current location of the config, this is either the path on the filesystem
     * or a PHP stream prefix.
     *
     * @var string
     */
    protected $location = '';

    /**
     * The base name of the configuration file, the suffix will be appended to this
     * to find the real filename.
     *
     * @var string
     */
    protected $name = '';

    /**
     * Current options for the writer.
     * See the specific writer to see which options it supports.
     *
     * @var array
     */
    protected $options = array();

    /**
     * Contains the configuration object to write with the save() method.
     *
     * @var bool
     */
    protected $config = false;

    /**
     * Controls whether comments are written to the INI file or not.
     *
     * @var bool
     */
    protected $useComments = true;

    /**
     * Constructs the writer and initializes it with the file to write.
     *
     * After construction call save() to store the INI file to disk.
     *
     * @param string $path The relative or absolute path to where the
     *                     configuration should be written to. Using PHP
     *                     streams is also possible, e.g.
     *                     compress.gz://site.ini.gz
     * @param ezcConfiguration $config The configuration object which should be
     *                                 stored in an INI file.
     * @param int $permissions The file permission to use on the newly created
     *                         file, it uses the same values as chmod().
     */
    public function __construct( $path = null, ezcConfiguration $config = null, $permissions = 0666 )
    {
        if ( $path !== null )
        {
            $this->parseLocationPath( $path, $this->getSuffix() );
        }
        $this->config = $config;
        $this->permissions = $permissions;
    }

    /**
     * Sets the configuration object that will be used for the next call to save().
     *
     * Pass false if you wish to remove the current configuration object.
     *
     * @param ezcConfiguration $config
     * @return void
     */
    public function setConfig( ezcConfiguration $config )
    {
        $this->config = $config;
    }

    /**
     * Return the current location string.
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Return the current name for the configuration to be written.
     *
     * @return int
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the current options for the writer.
     *
     * @return array
     */
    public function getOptions()
    {
        return array( 'useComments' => $this->useComments, 'permissions' => $this->permissions );
    }

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
    public function init( $location, $name, ezcConfiguration $config, $options = array() )
    {
        $this->path = $location . DIRECTORY_SEPARATOR . $name . '.' . $this->getSuffix();
        $this->location = $location;
        $this->name = $name;
        $this->setConfig( $config );
        $this->setOptions( $options );
    }

    /**
     * Parses a the path $path and sets the location and name
     * properties on this object.
     *
     * The file is checked if it contains the correct $suffix.
     *
     * ezcConfigurationFileReader::parseLocationPath() has the same
     * code. It is duplicated to prevent complex OO hacks.
     *
     * @throws ezcConfigurationInvalidSuffixExceptionif the configuration file
     *         has the wrong suffix.
     * @param string $path
     * @param string $suffix
     * @return void
     */
    protected function parseLocationPath( $path, $suffix )
    {
        $this->path = $path;
        $this->location = dirname( $path );
        $base = basename( $path );
        if ( $suffix[0] != '.' )
        {
            $suffix = ".$suffix";
        }
        if ( !preg_match( '@'. preg_quote( $suffix ) . '$@', $path ) )
        {
            throw new ezcConfigurationInvalidSuffixException( $path, $suffix );
        }
        $this->name = basename( $base, $suffix );
    }

    /**
     * Saves the current config object.
     *
     * Saves the current configuration object to a place which can later be
     * retrieved with a ezcConfigurationReader.
     *
     * @throws ezcConfigurationNoConfigObjectException if there is not config
     *         object set to write.
     * @throws ezcConfigurationInvalidSuffixExceptionif the configuration file
     *         has the wrong suffix.
     * @throws ezcConfigurationWriteFailureException if the configuration could
     *         not be stored in the given location.
     * @return void
     */
    public function save()
    {
        if ( !$this->config )
        {
            throw new ezcConfigurationNoConfigObjectException();
        }

        // Open the file
        $fp = $this->openFile();

        // Retrieve settings and comments from configuration object, and write
        // them to the file
        if ( $this->useComments )
        {
            $this->writeSettings( $fp, $this->config->getAllSettings(), $this->config->getAllComments() );
        }
        else
        {
            $this->writeSettings( $fp, $this->config->getAllSettings() );
        }

        $this->closeFile( $fp );
    }

    /**
     * Opens a file for writing.
     *
     * This method opens a file for writing and checks whether it was
     * successfully opened.
     *
     * @throws ezcConfigurationWriteFailedException if it was not possible to
     *         write to the file.
     * @return resource The opened file's filehandler.
     */
    protected function openFile()
    {
        $fp = fopen( $this->path, 'wt' );
        if ( !$fp )
        {
            throw new ezcConfigurationWriteFailedException( $this->path );
        }
        return $fp;
    }

    /**
     * Closes a file pointed to by $fp and sets file permissions.
     *
     * This method closes a file with the file pointer that was passed. After
     * closing the file the permissions are set as configured with the
     * "permissions" option.
     *
     * @param resource $fp
     * @return void
     */
    protected function closeFile( $fp )
    {
        fclose( $fp );
        $oldUmask = umask( 0 );
        chmod( $this->path, $this->permissions );
        umask( $oldUmask );
    }

    /**
     * Sets the options $configurationData.
     *
     * The options are specified in a associative array in the form 'optionName' => value.
     *
     * @throws ezcBaseSettingNotFoundException if you try to set a non existent setting.
     * @throws ezcBaseSettingValueException if you specify a value out of range for a setting.
     * @param array(string=>mixed) $configurationData
     * @return void
     */
    public function setOptions( $configurationData )
    {
        foreach ( $configurationData as $name => $value )
        {
            switch ( $name )
            {
                case 'useComments':
                    if ( gettype( $value ) != 'boolean' )
                    {
                        throw new ezcBaseSettingValueException( $name, $value, 'bool' );
                    }
                    $this->useComments = $value;
                    break;
                case 'permissions':
                    if ( gettype( $value ) != 'integer' )
                    {
                        throw new ezcBaseSettingValueException( $name, $value, 'int, 0 - 0777' );
                    }
                    if ( $value < 0 || $value > 0777 )
                    {
                        throw new ezcBaseSettingValueException( $name, $value, 'int, 0 - 0777' );
                    }
                    $this->permissions = $value;
                    break;
                default:
                    throw new ezcBaseSettingNotFoundException( $name );
            }
        }
    }
}
?>
