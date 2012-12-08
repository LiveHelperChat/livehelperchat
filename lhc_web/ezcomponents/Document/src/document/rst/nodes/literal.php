<?php
/**
 * File containing the ezcDocumentRstLiteralNode struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The inline literal AST node
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentRstLiteralNode extends ezcDocumentRstNode
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
        parent::__construct( $token, self::LITERAL );
    }

    /**
     * Return node content, if available somehow
     *
     * @return string
     */
    protected function content()
    {
        return trim( $this->token->content );
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
        return new ezcDocumentRstLiteralNode(
            $properties['token']
        );
    }
}

?>
