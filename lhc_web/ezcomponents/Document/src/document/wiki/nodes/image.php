<?php
/**
 * File containing the ezcDocumentWikiImageNode struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Struct for Wiki document inline image syntax tree nodes
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentWikiImageNode extends ezcDocumentWikiInlineNode
{
    /**
     * Image resource description
     *
     * @var array
     */
    public $resource = array();

    /**
     * Image title
     *
     * @var array
     */
    public $title = array();

    /**
     * Image alignement
     *
     * @var string
     */
    public $alignement;

    /**
     * Image width
     *
     * @var int
     */
    public $width;

    /**
     * Image height
     *
     * @var int
     */
    public $height;

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
        $node->nodes      = $properties['nodes'];
        $node->resource   = $properties['resource'];
        $node->title      = $properties['title'];
        $node->alignement = $properties['alignement'];
        $node->width      = $properties['width'];
        $node->height     = $properties['height'];
        return $node;
    }
}

?>
