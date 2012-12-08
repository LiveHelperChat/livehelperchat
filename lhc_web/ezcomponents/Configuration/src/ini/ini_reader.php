<?php
/**
 * File containing the ezcConfigurationIniReader class
 *
 * @package Configuration
 * @version 1.3.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcConfigurationIniReader provides functionality for reading INI files into
 * ezcConfiguration objects.
 *
 * Typical usage is to create the reader object and pass the filepath in the
 * constructor:
 * <code>
 * $reader = new ezcConfigurationIniReader( "settings/site.ini" );
 * $reader->load();
 * </code>
 * That makes the class figure out the location and name values automatically.
 *
 * Or generally use the init() function:
 * <code>
 * $reader = new ezcConfigurationIniReader();
 * $reader->init( "settings", "site" );
 * $reader->load();
 * </code>
 *
 * Accessing the configuration object is done by the getConfig() method or by
 * using the return value of load():
 * <code>
 * $conf1 = $reader->load();
 * $conf2 = $reader->getConfig();
 * // $conf1 and $conf2 points to the same object
 * </code>
 *
 * If caching is employed the getTimestamp() method can be used to find the last
 * modification time of the file.
 * <code>
 * $time = $reader->getTimestamp();
 * if ( $time > $cachedTime )
 * {
 *    $reader->load();
 * }
 * </code>
 *
 * Options can be set with the setOptions() method. The only option that this
 * reader supports is the "useComments" option:
 * <code>
 * $reader->setOptions( array( 'useComments' => true ) );
 * </code>
 *
 * Instead of loading the INI file it can be validated with validate(), this will
 * return an ezcConfigurationValidationResult which can be inspected and
 * presented to the end user.
 * <code>
 * $result = $reader->validate();
 * if ( !$result->isValid )
 * {
 *    foreach ( $result->getResultList() as $resultItem )
 *    {
 *        print $resultItem->file . ":" . $resultItem->line . ":" .
 *            $resultItem->column. ":";
 *        print " " . $resultItem->details . "\n";
 *    }
 * }
 * </code>
 *
 * For more information on file based configurations see {@link
 * ezcConfigurationFileReader}.
 *
 * This class uses exceptions and will throw them when the conditions for the
 * operation fails somehow.
 *
 * @package Configuration
 * @version 1.3.5
 * @mainclass
 */
class ezcConfigurationIniReader extends ezcConfigurationFileReader
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
     * Loads a configuration object
     *
     * Loads the current config object from a give location which can later be stored
     * with a ezcConfigurationWriter.
     *
     * @see config()
     * @throws ezcConfigurationNoConfigException if there is no config
     *         object to be read from the location.
     * @throws ezcConfigurationInvalidSuffixException if the current
     *         location values cannot be used for reading.
     * @throws ezcConfigurationReadFailureException if the configuration
     *         could not be read from the given location.
     *
     * @return ezcConfiguration
     */
    public function load()
    {
        $parser = new ezcConfigurationIniParser( ezcConfigurationIniParser::PARSE, $this->path );
        $settings = array();
        $comments = array();

        foreach ( new NoRewindIterator( $parser ) as $element )
        {
            if ( $element instanceof ezcConfigurationIniItem )
            {
                switch ( $element->type )
                {
                    case ezcConfigurationIniItem::GROUP_HEADER:
                        $settings[$element->group] = array();
                        if ( !is_null( $element->comments ) )
                        {
                            $comments[$element->group]['#'] = $element->comments;
                        }
                        break;

                    case ezcConfigurationIniItem::SETTING:
                        eval( '$settings[$element->group][$element->setting]'. $element->dimensions. ' = $element->value;' );
                        if ( !is_null( $element->comments ) )
                        {
                            eval( '$comments[$element->group][$element->setting]'. $element->dimensions. ' = $element->comments;' );
                        }
                        break;
                }
            }
            if ( $element instanceof ezcConfigurationValidationItem )
            {
                throw new ezcConfigurationParseErrorException( $element->file, $element->line, $element->description );
            }
        }

        $this->config = new ezcConfiguration( $settings, $comments );
        return $this->config;
    }

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
    public function validate( $strict = false )
    {
        $parserType = $strict ? ezcConfigurationIniParser::VALIDATE_STRICT : ezcConfigurationIniParser::VALIDATE;
        $parser = new ezcConfigurationIniParser( $parserType, $this->path );

        $validationResult = new ezcConfigurationValidationResult( $this->location, $this->name, $this->path );

        foreach ( new NoRewindIterator( $parser ) as $element )
        {
            if ( $element instanceof ezcConfigurationIniItem )
            {
                throw new Exception( "A validating parser emitted a configuration item, which should never happen" );
            }
            if ( $element instanceof ezcConfigurationValidationItem )
            {
                $validationResult->appendItem( $element );
                if ( $element->type == ezcConfigurationValidationItem::ERROR )
                {
                    $validationResult->isValid = false;
                }
                else if ( $element->type == ezcConfigurationValidationItem::WARNING && $parserType == ezcConfigurationIniItem::VALIDATE_STRICT )
                {
                    $validationResult->isValid = false;
                }
            }
        }
        return $validationResult;
    }
}
?>
