<?php
/**
 * File containing the ezcDocumentRstExternalReferenceNode struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The external reference AST node
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentRstExternalReferenceNode extends ezcDocumentRstLinkNode
{
    /**
     * Construct RST document node
     *
     * @param ezcDocumentRstToken $token
     * @return void
     */
    public function __construct( ezcDocumentRstToken $token )
    {
        parent::__construct( $token, self::LINK_REFERENCE );
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
        $node = new ezcDocumentRstExternalReferenceNode(
            $properties['token']
        );

        $node->nodes  = $properties['nodes'];
        $node->target = $properties['target'];
        return $node;
    }
}

?>
