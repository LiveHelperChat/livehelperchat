<?php
/**
 * File containing the ezcDocumentDocbookToRstConverterOptions class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing the basic options for the ezcDocumentEzp3Xml class
 *
 * @property array $headerTypes
 *           Array of special characters to use fore headings in RST output. If
 *           two chracters are given, the heading will be rendered with an over
 *           and underline.
 * @property int $wordWrap
 *           Maximum number of characters per line. The contents will be
 *           wrapped at the given position. Defaults to 78.
 * @property int $itemListCharacter
 *           Character used for item lists. Defaults to -, valid are also:
 *           *, +, •, ‣, ⁃
 *           wrapped at the given position. Defaults to 78.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToRstConverterOptions extends ezcDocumentConverterOptions
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
        $this->headerTypes = array(
            '==',
            '--',
            '=',
            '-',
            '^',
            '~',
            '`',
            '*',
            ':',
            '+',
            '/',
            '.',
        );
        $this->wordWrap          = 78;
        $this->itemListCharacter = '-';

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
            case 'headerTypes':
                if ( !is_array( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'array' );
                }

                $this->properties[$name] = $value;
                break;

            case 'wordWrap':
                if ( !is_numeric( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'int' );
                }

                $this->properties[$name] = (int) $value;
                break;

            case 'itemListCharacter':
                if ( !in_array( $value, $listCharacters = array(
                        '*', '-', '+',
                        "\xe2\x80\xa2", "\xe2\x80\xa3", "\xe2\x81\x83"
                    ), true ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'Item list characters: ' . implode( ', ', $listCharacters ) );
                }

                $this->properties[$name] = $value;
                break;

            default:
                parent::__set( $name, $value );
        }
    }
}

?>
