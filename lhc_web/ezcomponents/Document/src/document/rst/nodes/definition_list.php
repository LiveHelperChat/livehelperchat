<?php
/**
 * File containing the ezcDocumentRstDefinitionListNode struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The definition list item AST node
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentRstDefinitionListNode extends ezcDocumentRstBlockNode
{
    /**
     * DefinitionList target name
     *
     * @var array
     */
    public $name;

    /**
     * Construct RST document node
     *
     * @param ezcDocumentRstToken $token
     * @param array $name
     * @return void
     */
    public function __construct( ezcDocumentRstToken $token, array $name )
    {
        // Perhaps check, that only node of type section and metadata are
        // added.
        parent::__construct( $token, self::DEFINITION_LIST );
        $this->name = $name;
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
        $node = new ezcDocumentRstDefinitionListNode(
            $properties['token'],
            $properties['name']
        );

        $node->nodes       = $properties['nodes'];
        $node->indentation = isset( $properties['indentation'] ) ? $properties['indentation'] : 0;
        return $node;
    }
}

?>
