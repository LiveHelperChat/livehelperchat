<?php
/**
 * File containing the ezcDocumentOdtListLevelStyleBullet class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Bullet list-level style.
 *
 * @property-read int $level
 *                The list level, starting with 1.
 * @property ezcDocumentOdtStyle|null $textStyle
 *           Text style for list bullet / number formatting.
 * @property string $bulletChar
 *           Character to use for bullets (only 1 char allowed).
 * @property string $numPrefix
 *           Prefix for list bullets.
 * @property string $numSuffix
 *           Suffix for list bullets.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentOdtListLevelStyleBullet extends ezcDocumentOdtListLevelStyle
{
    /**
     * Properties
     * 
     * @var array(string=>mixed)
     */
    private $properties = array(
        'bulletChar' => '',
        'numPrefix'  => '',
        'numSuffix'  => '',
    );

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'bulletChar':
                if ( !is_string( $value ) || iconv_strlen( $value, 'UTF-8' ) !== 1 )
                {
                    throw new ezcBaseValueException( $name, $value, 'string, length = 1' );
                }
                break;
            case 'numPrefix':
            case 'numSuffix':
                if ( !is_string( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'string' );
                }
                break;
            default:
                return parent::__set( $name, $value );
        }
        $this->properties[$name] = $value;
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @ignore
     */
    public function __get( $name )
    {
        if ( array_key_exists( $name, $this->properties ) )
        {
            return $this->properties[$name];
        }
        return parent::__get( $name );
    }

    /**
     * Returns true if the property $name is set, otherwise false.
     *
     * @param string $name     
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        return array_key_exists( $name, $this->properties ) || parent::__isset( $name );
    }
}

?>
