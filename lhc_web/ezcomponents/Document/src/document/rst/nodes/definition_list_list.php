<?php
/**
 * File containing the ezcDocumentRstDefinitionListListNode struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * An aggregating AST node for definition list items nodes
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentRstDefinitionListListNode extends ezcDocumentRstNode
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
        parent::__construct( $token, self::DEFINITION_LIST_LIST );
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
        $node = new ezcDocumentRstDefinitionListListNode(
            $properties['token']
        );

        $node->nodes       = $properties['nodes'];
        return $node;
    }
}

?>
