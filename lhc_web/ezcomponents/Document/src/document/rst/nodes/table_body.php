<?php
/**
 * File containing the ezcDocumentRstTableBodyNode struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The table body AST node
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentRstTableBodyNode extends ezcDocumentRstNode
{
    /**
     * Construct RST document node
     *
     * @param ezcDocumentRstToken $token
     * @return void
     */
    public function __construct( ezcDocumentRstToken $token )
    {
        // Perhaps check, that only node of type section and metadata are
        // added.
        parent::__construct( $token, self::TABLE_BODY );
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
        $node = new ezcDocumentRstTableBodyNode(
            $properties['token']
        );

        $node->nodes = $properties['nodes'];

        return $node;
    }
}

?>
