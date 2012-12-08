<?php
/**
 * File containing the ezcConfigurationFileReader class.
 *
 * @package Configuration
 * @version 1.3.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * ezcConfigurationFileReader class provides the functionality for reading
 * file based configuration formats.
 *
 * This class implements most of the interface of ezcConfigurationReader and
 * makes it easier to work on file based configuration. All methods except
 * load() and validate() are implemented by this class, so a subclass only
 * needs to handle the actual serialization.
 *
 * @package Configuration
 * @version 1.3.5
 */
abstract class ezcConfigurationFileReader extends ezcConfigurationReader
{
    /**
     * The path to the file which will contain the serialized configuration data.
     *
     * @var string
     */
    protected $path = '';

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
     * Current options for the reader.
     * See the specific reader to see which options it supports.
     *
     * @var array
     */
    protected $options = array();

    /**
     * Contains the configuration object that was read from the file with
     * load().
     *
     * @var ezcConfiguration
     */
    protected $config = false;

    /**
     * Controls whether comments are read from the INI file or not.
     *
     * @var bool
     */
    private $useComments;

    /**
     * Constructs the reader object.
     *
     * $path must contain the relative or absolute path to the configuration file.
     * You can use PHP streams, e.g compress.gz://site.ini.gz.
     *
     * After construction call load() to parse the INI file from disk and return a
     * configuration object.
     *
     * @param string $path
     */
    public function __construct( $path = null )
    {
        if ( $path !== null )
        {
            $this->parseLocationPath( $path, $this->getSuffix() );
        }
    }

    /**
     * Initializes the reader with a location and a name. These values determine
     * where the configuration will be serialized.
     *
     * @param string $location The main placement for the configuration. It is
     *               up to the specific reader to interpret this value. This
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
    public function init( $location, $name, array $options = array() )
    {
        $this->path = $location . DIRECTORY_SEPARATOR . $name . '.' . $this->getSuffix();
        $this->location = $location;
        $this->name = $name;
        $this->setOptions( $options );
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
                default:
                    throw new ezcBaseSettingNotFoundException( $name );
            }
        }
    }

    /**
     * Returns the current options for the reader.
     *
     * @return array
     */
    public function getOptions()
    {
        return array( 'useComments' => $this->useComments );
    }
    /**
     * Returns the current configuration object.
     *
     * Returns false if there no current configuration.
     *
     * @return ezcConfiguration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Returns the current location string.
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Returns the current name for the configuration to be read.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Parses a the path $path and sets the location and name
     * properties on this object.
     *
     * ezcConfigurationFileWriter::parseLocationPath() has the same
     * code. It is duplicated to prevent complex OO hacks.
     *
     * @throws ezcConfigurationException if the configuration file has the wrong suffix.
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
        if ( !preg_match( '@' . preg_quote( $suffix ) . '$@', $path ) )
        {
            throw new ezcConfigurationInvalidSuffixException( $path, $suffix );
        }
        $this->name = basename( $base, $suffix );
    }

    /**
     * Returns the last modified timestamp.
     *
     * Returns false if there is not last current timestamp.
     *
     * @return int
     */
    public function getTimestamp()
    {
        if ( file_exists( $this->path ) )
        {
            return filemtime( $this->path );
        }
        return false;
    }

    /**
     * Returns true if the configuration exists.
     *
     * @return bool
     */
    public function configExists()
    {
        return file_exists( $this->path );
    }
}
?>
