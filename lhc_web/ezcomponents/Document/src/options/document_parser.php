<?php
/**
 * File containing the ezcDocumentParserOptions class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing the basic options for the ezcDocumentParser.
 *
 * @property int $errorReporting
 *           Error reporting level. All errors with a severity greater or equel
 *           then the defined level are converted to exceptions. All other
 *           errors are just stored in errors property of the parser class.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentParserOptions extends ezcBaseOptions
{
    /**
     * Container to hold the properties.
     *
     * @var array(string=>mixed)
     */
    protected $properties = array(
        'errorReporting' => 15, // E_PARSE | E_ERROR | E_WARNING | E_NOTICE
    );

    /**
     * Sets the option $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name is not defined
     * @throws ezcBaseValueException
     *         if $value is not correct for the property $name
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'errorReporting':
                if ( !is_int( $value ) ||
                     ( ( $value & E_PARSE ) === 0 ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'int & E_PARSE' );
                }

                $this->properties[$name] = $value;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }
}

?>
