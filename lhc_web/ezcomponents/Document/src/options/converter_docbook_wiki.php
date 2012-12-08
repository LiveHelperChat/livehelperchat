<?php
/**
 * File containing the ezcDocumentDocbookToWikiConverterOptions class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing the basic options for the ezcDocumentEzp3Xml class
 *
 * @property int $wordWrap
 *           Maximum number of characters per line. The contents will be
 *           wrapped at the given position. Defaults to 78.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToWikiConverterOptions extends ezcDocumentConverterOptions
{
    /**
     * Constructs an object with the specified values.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if $options contains a property not defined
     * @throws ezcBaseValueException
     *         if $options contains a property with a value not allowed
     * @param array(string=>mixed) $options
     */
    public function __construct( array $options = array() )
    {
        $this->wordWrap          = 78;

        parent::__construct( $options );
    }

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
            case 'wordWrap':
                if ( !is_numeric( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'int' );
                }

                $this->properties[$name] = (int) $value;
                break;

            default:
                parent::__set( $name, $value );
        }
    }
}

?>
