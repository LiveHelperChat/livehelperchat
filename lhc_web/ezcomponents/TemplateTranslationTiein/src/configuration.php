<?php
/**
 * File containing the ezcTemplateTranslationConfiguration class
 *
 * @package TemplateTranslationTiein
 * @version 1.1.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcTemplateTranslationConfiguration provides an environment for translations in templates.
 *
 * @package TemplateTranslationTiein
 * @mainclass
 * @version 1.1.1
 */
class ezcTemplateTranslationConfiguration
{
    /**
     * @param ezcTemplateTranslationConfiguration Instance
     */
    static private $instance = null;

    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    private $properties = array();

    /**
     * Private constructor to prevent non-singleton use
     */
    private function __construct()
    {
        $this->properties = array( 'locale' => null, 'manager' => null );
    }

    /**
     * Returns an instance of the class ezcTemplateTranslationConfiguration
     *
     * @return ezcTemplateTranslationConfiguration Instance of ezcTemplateTranslationConfiguration
     */
    public static function getInstance()
    {
        if ( is_null( self::$instance ) )
        {
            self::$instance = new ezcTemplateTranslationConfiguration();
        }
        return self::$instance;
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @throws ezcBaseValueException if a the value for a property is out of
     *         range.
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'locale':
                if ( !is_string( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'string' );
                }
                break;

            case 'manager':
                if ( ( !$value instanceof ezcTranslationManager) && $value !== null )
                {
                    throw new ezcBaseValueException( $name, $value, 'instance of ezcTranslationManager or null' );
                }
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
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
        switch ( $name )
        {
            case 'locale':
                return $this->properties[$name];

            case 'manager':
                if ( $this->properties[$name] === null )
                {
                    ezcBaseInit::fetchConfig( 'ezcInitTemplateTranslationManager', $this );
                }
                if ( $this->properties[$name] === null )
                {
                    throw new ezcTemplateTranslationManagerNotConfiguredException();
                }
                return $this->properties[$name];
        }
        throw new ezcBasePropertyNotFoundException( $name );
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
        switch ( $name )
        {
            case 'locale':
            case 'manager':
                return isset( $this->properties[$name] );

            default:
                return false;
        }
        // if there is no default case before:
        return parent::__isset( $name );
    }
}
?>
