<?php
/**
 * File containing the ezcDocumentRstBulletListNode struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The bullet list item AST node
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentRstBulletListNode extends ezcDocumentRstBlockNode
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
        parent::__construct( $token, self::BULLET_LIST );
    }

    /**
     * Return node content, if available somehow
     *
     * @return string
     */
    protected function content()
    {
        return trim( $this->token->content ) . ', ' . $this->indentation;
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
        $node = new ezcDocumentRstBulletListNode(
            $properties['token']
        );

        $node->nodes       = $properties['nodes'];
        $node->indentation = isset( $properties['indentation'] ) ? $properties['indentation'] : 0;
        return $node;
    }
}

?>
