<?php
/**
 * File containing the ezcDocumentWikiNode struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Struct for Wiki document document abstract syntax tree nodes
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocumentWikiNode extends ezcBaseStruct
{
    /**
     * Line of node in source file.
     *
     * @var int
     */
    public $line;

    /**
     * Character position of node in source file.
     *
     * @var int
     */
    public $position;

    /**
     * Child nodes
     *
     * @var mixed
     */
    public $nodes = array();

    /**
     * Optional reference to token, not available for all nodes.
     *
     * @var ezcDocumentWikiToken
     */
    public $token = null;

    /**
     * Construct Wiki node
     *
     * @param ezcDocumentWikiToken $token
     * @param int $type
     * @return void
     */
    public function __construct( ezcDocumentWikiToken $token )
    {
        $this->line     = $token->line;
        $this->position = $token->position;
        $this->token    = $token;
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
        return null;
    }
}

?>
