<?php
/**
 * File containing the ezcDocumentRstAnonymousLinkNode struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The anonymous link AST node
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentRstAnonymousLinkNode extends ezcDocumentRstLinkNode
{
    /**
     * Construct RST document node
     *
     * @param ezcDocumentRstToken $token
     * @return void
     */
    public function __construct( ezcDocumentRstToken $token )
    {
        parent::__construct( $token, self::LINK_ANONYMOUS );
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
        $node = new ezcDocumentRstAnonymousLinkNode(
            $properties['token']
        );

        $node->nodes  = $properties['nodes'];
        $node->target = $properties['target'];
        return $node;
    }
}

?>
