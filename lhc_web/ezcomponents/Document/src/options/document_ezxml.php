<?php
/**
 * File containing the ezcDocumentEzXmlOptions class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing the basic options for the ezcDocumentEzXml class
 *
 * @property string $relaxNgSchema
 *           Relax NG schema which is used to validate the eZ Publish 3
 *           documents.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentEzXmlOptions extends ezcDocumentXmlOptions
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
        $this->relaxNgSchema = dirname( __FILE__ ) . '/../document/xml/ezxml/schema/ezxml_schema.rng';

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
            case 'relaxNgSchema':
                if ( !is_file( $value ) || !is_readable( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'RelaxNG schema file' );
                }

                $this->properties[$name] = (string) $value;
                break;

            default:
                parent::__set( $name, $value );
        }
    }
}

?>
