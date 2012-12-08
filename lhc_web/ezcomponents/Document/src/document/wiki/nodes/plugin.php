<?php
/**
 * File containing the ezcDocumentWikiPluginNode struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Struct for Wiki document plugin abstract syntax tree nodes
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentWikiPluginNode extends ezcDocumentWikiBlockLevelNode
{
    /**
     * Plugin type / name.
     *
     * @var string
     */
    public $type;

    /**
     * Plugin parameters
     *
     * @var array
     */
    public $parameters;

    /**
     * Plugin content
     *
     * @var string
     */
    public $text;

    /**
     * Construct Wiki node
     *
     * @param ezcDocumentWikiToken $token
     * @param int $type
     * @return void
     */
    public function __construct( ezcDocumentWikiToken $token )
    {
        parent::__construct( $token );
        $this->type       = $token->type;
        $this->parameters = $token->parameters;
        $this->text       = $token->text;
    }

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
        $node->nodes = $properties['nodes'];
        return $node;
    }
}

?>
