<?php
/**
 * File containing the ezcDocumentPropertyContainerDomElement class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Wrapper class around DOMElement to store additional information
 * associated with DOMElement nodes.
 *
 * The storage of additional information is realized using a static object 
 * attribute, since dynamic attributes do not seem to work in DOMElement 
 * derived classes.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentPropertyContainerDomElement extends DOMElement
{
    /**
     * Static property container shared with all nodes.
     *
     * Contains the properties registered for a node indexed by the numeric
     * ID of the respective node, which is assigned on the first write
     * access to each node.
     *
     * @var array
     */
    protected static $properties = array();

    /**
     * Autoincrement unique ID for DOMElement nodes in XML documents.
     *
     * @var int
     */
    protected static $id = 1;

    /**
     * Namespace URI for the custom ID setting, for the association with
     * the node data.
     */
    const NS_URI = 'http://ezcomponents.org/Document';

    /**
     * Get property associated with node
     *
     * Get the value of a property associated with the node, or false, if
     * the property does not (yet) exist.
     *
     * @param string $name
     * @return mixed
     */
    public function getProperty( $name )
    {
        if ( ( !$this->hasAttributeNs( self::NS_URI, 'id' ) ) ||
             ( !isset( self::$properties[$id = (int) $this->getAttributeNs( self::NS_URI, 'id' )] ) ) ||
             ( !isset( self::$properties[$id][$name] ) ) )
        {
            return false;
        }

        return self::$properties[$id][$name];
    }

    /**
     * Set property on current node
     *
     * Set a custom property on the current node, containing a mixed value
     * identified by a string identifier.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setProperty( $name, $value )
    {
        if ( !$this->hasAttributeNs( self::NS_URI, 'id' ) )
        {
            $id = self::$id++;
            $this->setAttributeNs( self::NS_URI, 'ez:id', $id );
            self::$properties[$id] = array();
        }
        else
        {
             $id = (int) $this->getAttributeNs( self::NS_URI, 'id' );
        }

        self::$properties[$id][$name] = $value;
    }
}

?>
