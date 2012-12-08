<?php
/**
 * File containing the ezcWebdavNamespaceRegistry class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Class to map XML namespaces to their shortcuts.
 *
 * An instance of this class is used in {@link ezcWebdavXmlTool} to keep track
 * of used namespace shortcuts and the creation of new ones, if necessary.
 * 
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavNamespaceRegistry implements ArrayAccess
{
    /**
     * Counter to create new shortcuts.
     * 
     * @var int
     */
    protected $shortcutCounter = 0;

    /**
     * Base string to be used for new shortcuts.
     * 
     * @var string
     */
    protected $shortcutBase = 'ezc';

    /**
     * Maps namespace URIs to shortcuts.
     * 
     * <code>
     * array(
     *      'uri' => '<shortcut>',
     *      // ...
     * )
     * </code>
     *
     * @var array(string=>string)
     */
    protected $namespaceMap = array();

    /**
     * Stores shortcuts that are already in use.
     *
     * <code>
     * array(
     *      '<shortcut>' => true,
     *      // ...
     * )
     * </code>
     * 
     * @var array(string=>bool)
     */
    protected $usedShortcuts = array();

    /**
     * Create a new namespace registry.
     *
     * Registers the standard namespace 'DAV:' with the shortcut 'D', which is
     * common in the RFC document.
     * 
     * @return void
     */
    public function __construct()
    {
        $this['DAV:']                             = 'D';
        $this[ezcWebdavLockPlugin::XML_NAMESPACE] = 'ezclock';
    }

    /**
     * ArrayAccess set access.
     *
     * Required by the ArrayAccess interface.
     * 
     * @param string $offset 
     * @param string $value 
     * @return void
     * @ignore
     *
     * @throws ezcBaseValueException
     *         if the given namespace is already registered.
     */
    public function offsetSet( $offset, $value )
    {
        if ( isset( $this->namespaceMap[$offset] ) )
        {
            throw new ezcBaseValueException(
                'offset',
                $offset,
                'non-existent'
            );
        }
        $this->namespaceMap[$offset] = $value;
        $this->usedShortcuts[$value] = true;
    }

    /**
     * ArrayAccess get access.
     *
     * Required by the ArrayAccess interface.
     * 
     * @param string $offset 
     * @return string
     * @ignore
     */
    public function offsetGet( $offset )
    {
        if ( !isset( $this->namespaceMap[$offset] ) )
        {
            $this[$offset] = $this->newShortcut();
        }
        return $this->namespaceMap[$offset];
    }

    /**
     * Array unset() access.
     *
     * Required by the ArrayAccess interface.
     *
     * @param string $offset 
     * @return void
     * @ignore
     */
    public function offsetUnset( $offset )
    {
        if ( isset( $this->namespaceMap[$offset] ) )
        {
            unset( $this->usedShortcuts[$this->namespaceMap[$offset]] );
            unset( $this->namespaceMap[$offset] );
        }
    }

    /**
     * Array isset() access.
     *
     * Required by the ArrayAccess interface.
     * 
     * @param string $offset 
     * @return bool
     * @ignore
     */
    public function offsetExists( $offset )
    {
        return isset( $this->namespaceMap[$offset] );
    }
    
    /**
     * Creates a new namespace shortcut.
     *
     * Produces a new shortcut for a namespace by using {@link
     * $this->shortcutBase} and the first 5 characters of the MD5 hash of the
     * current microtime. Only returns unused shortcuts.
     * 
     * @return string
     */
    protected function newShortcut()
    {
        do
        {
            $shortcut = sprintf( "%s%'05s", $this->shortcutBase, $this->shortcutCounter++ );
        }
        while ( isset( $this->usedShortcuts[$shortcut] ) );
        return $shortcut;
    }
}

?>
