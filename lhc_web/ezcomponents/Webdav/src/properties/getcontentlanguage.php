<?php
/**
 * File containing the ezcWebdavGetContentLanguageProperty class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * An object of this class represents the Webdav property <getcontentlanguage>.
 *
 * @property array(string) $languages
 *           The languages.
 *
 * @version 1.1.4
 * @package Webdav
 */
class ezcWebdavGetContentLanguageProperty extends ezcWebdavLiveProperty
{
    /**
     * Creates a new ezcWebdavGetContentLanguageProperty.
     *
     * The given array must contain strings that represent language shortcuts.
     * 
     * @param array(string) $languages
     * @return void
     */
    public function __construct( array $languages = array() )
    {
        parent::__construct( 'getcontentlanguage' );

        $this->languages = $languages;
    }

    /**
     * Sets a property.
     *
     * This method is called when an property is to be set.
     * 
     * @param string $propertyName The name of the property to set.
     * @param mixed $propertyValue The property value.
     * @return void
     * @ignore
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the given property does not exist.
     * @throws ezcBaseValueException
     *         if the value to be assigned to a property is invalid.
     * @throws ezcBasePropertyPermissionException
     *         if the property to be set is a read-only property.
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'languages':
                if ( !is_array( $propertyValue ) )
                {
                    return $this->hasError( $propertyName, $propertyValue, 'string' );
                }

                $this->properties[$propertyName] = $propertyValue;
                break;

            default:
                parent::__set( $propertyName, $propertyValue );
        }
    }

    /**
     * Returns if property has no content.
     *
     * Returns true, if the property has no content stored.
     * 
     * @return bool
     */
    public function hasNoContent()
    {
        return !count( $this->properties['languages'] );
    }

    /**
     * Removes all contents from a property.
     *
     * Clears the property, so that it will be recognized as empty later.
     * 
     * @return void
     */
    public function clear()
    {
        parent::clear();

        $this->properties['languages'] = array();
    }
}

?>
