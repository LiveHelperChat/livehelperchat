<?php
/**
 * File containing the ezcDocumentOdtFormattingPropertyCollection class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class to carry and manage {@link ezcDocumentOdtFormattingProperties}.
 *
 * An instance of this class is used in an {@link ezcDocumentOdtStyle} to carry 
 * various formatting properties of class {@link 
 * ezcDocumentOdtFormattingProperties}.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentOdtFormattingPropertyCollection
{
    /**
     * Formatting properties. 
     * 
     * @var array(const=>ezcDocumentOdtFormattingProperties)
     */
    private $properties = array();

    /**
     * Sets the given $properties.
     *
     * If properties of the same type are already set, an exception is thrown.  
     * If you don't care if properties are overwriten, use {@link 
     * replaceProperties()}. You can check if properties of a certain type are 
     * already set using {@link hasProperties()} and retrieve them using {@link 
     * getProperties()}.
     * 
     * @param ezcDocumentOdtFormattingProperties $properties 
     *
     * @throws ezcDocumentOdtFormattingPropertiesAlreadyExistException
     */
    public function setProperties( ezcDocumentOdtFormattingProperties $properties )
    {
        if ( isset( $this->properties[$properties->type] ) )
        {
            throw new ezcDocumentOdtFormattingPropertiesExistException(
                $properties
            );
        }
        $this->replaceProperties( $properties );
    }

    /**
     * Sets the given $properties, even if properties of the same type are 
     * already set.
     *
     * Similar to {@link setProperties()} but silently overwrites properties 
     * of the same type, if they exist.
     * 
     * @param ezcDocumentOdtFormattingProperties $properties 
     */
    public function replaceProperties( ezcDocumentOdtFormattingProperties $properties )
    {
        $this->properties[$properties->type] = $properties;
    }

    /**
     * Returns if properties of $type are set.
     *
     * Returns true, if properties of $type are set in this collection, 
     * otherwise false. $type must be one of the {@link 
     * ezcDocumentOdtFormattingProperties} PROPERTIES_* constants.
     * 
     * @param const $type 
     * @return bool
     */
    public function hasProperties( $type )
    {
        return isset( $this->properties[$type] );
    }

    /**
     * Returns properties of the given $type.
     *
     * If properties of the given $type are set, the corresponding object is 
     * returned. Otherwise null is returned. You can check if properties of a 
     * given $type are set using {@link hasProperties()}. $type must be one 
     * of the {@link ezcDocumentOdtFormattingProperties} FAMILY_ 
     * constants.
     * 
     * @param const $type 
     * @return ezcDocumentOdtFormattingProperties|null
     */
    public function getProperties( $type )
    {
        if ( $this->hasProperties( $type ) )
        {
            return $this->properties[$type];
        }
        return null;
    }
}

?>
