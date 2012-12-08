<?php
/**
 * File containing ezcDocumentWikiOptions class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing the basic options for the ezcDocumentWiki.
 *
 * @property ezcDocumentWikiTokenizer $tokenizer
 *           Tokenizer used to tokenize the inpout string before passign it to
 *           the parser.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentWikiOptions extends ezcDocumentOptions
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
        $this->properties['tokenizer'] = new ezcDocumentWikiCreoleTokenizer();

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
            case 'tokenizer':
                if ( !is_object( $value ) ||
                     !( $value instanceof ezcDocumentWikiTokenizer ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'instanceof ezcDocumentWikiTokenizer' );
                }

                $this->properties[$name] = $value;
                break;

            default:
                parent::__set( $name, $value );
        }
    }
}

?>
