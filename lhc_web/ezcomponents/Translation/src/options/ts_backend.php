<?php
/**
 * File containing the ezcTranslationTsBackendOptions class.
 *
 * @package Translation
 * @version 1.3.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Struct class to store the options of the ezcTranslationTsBackend class.
 *
 * This class stores the options for the {@link ezcTranslationTsBackend} class.
 *
 * @property string $location
 *           Path to the directory that contains all TS .xml files.
 * @property string $format
 *           Format for the translation file's name.  In this format
 *           string the special place holder [LOCALE] will be
 *           replaced with the requested locale's name. For example a
 *           format of "translations/[LOCALE].xml" will result in the
 *           filename for the translation file to be
 *           "translations/nl_NL.xml" if the nl_NL locale is
 *           requested.
 * @property bool $keepObsolete
 *           When this option is set to "true" the reader will not drop
 *           translation messages with the "obsolete" type set.
 * 
 * @package Translation
 * @version 1.3.2
 * @mainclass
 */
class ezcTranslationTsBackendOptions extends ezcBaseOptions
{
    /**
     * Constructs a new options class.
     *
     * It also sets the default values of the format property
     *
     * @param array(string=>mixed) $array The initial options to set.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If a the value for the property options is not an instance of
     * @throws ezcBaseValueException
     *         If a the value for a property is out of range.
     */
    public function __construct( $array = array() )
    {
        $this->properties['format'] = '[LOCALE].xml';
        $this->properties['keepObsolete'] = false;
        parent::__construct( $array );
    }

    /**
     * Property write access.
     * 
     * @throws ezcBasePropertyNotFoundException
     *         If a desired property could not be found.
     * @throws ezcBaseSettingValueException
     *         If a desired property value is out of range.
     *
     * @param string $propertyName Name of the property.
     * @param mixed $val  The value for the property.
     * @ignore
     */
    public function __set( $propertyName, $val )
    {
        switch ( $propertyName )
        {
            case 'location':
                if ( $val[strlen( $val ) - 1] != '/' )
                {
                    $val .= '/';
                }
                break;
            case 'format':
            case 'keepObsolete':
                break;
            default:
                throw new ezcBaseSettingNotFoundException( $propertyName );
        }
        $this->properties[$propertyName] = $val;
    }
}

?>
