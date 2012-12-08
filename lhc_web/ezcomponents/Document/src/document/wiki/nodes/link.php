<?php
/**
 * File containing the ezcDocumentWikiLinkNode struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Struct for Wiki document inline link syntax tree nodes
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentWikiLinkNode extends ezcDocumentWikiInlineNode
{
    /**
     * Link nodes
     *
     * @var array
     */
    public $link = array();

    /**
     * Link title nodes
     *
     * @var array
     */
    public $title = array();

    /**
     * Link description
     *
     * @var array
     */
    public $description = array();

    /**
     * Set state after var_export
     *
     * @param array $properties
     * @return void
     * @ignore
     */
    public static function __set_state( $properties )
    {
        $nodeClass = __CLASS__;
        $node = new $nodeClass( $properties['token'] );
        $node->nodes       = $properties['nodes'];
        $node->link        = $properties['link'];
        $node->title       = $properties['title'];
        $node->description = $properties['description'];
        return $node;
    }
}

?>
