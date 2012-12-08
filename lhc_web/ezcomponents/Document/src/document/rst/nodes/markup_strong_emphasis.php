<?php
/**
 * File containing the ezcDocumentRstMarkupStrongEmphasisNode struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The inline markup AST node for strong emphasis markup
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentRstMarkupStrongEmphasisNode extends ezcDocumentRstMarkupNode
{
    /**
     * Construct RST document node
     *
     * @param ezcDocumentRstToken $token
     * @param bool $open
     * @return void
     */
    public function __construct( ezcDocumentRstToken $token, $open )
    {
        parent::__construct( $token, self::MARKUP_STRONG );
        $this->openTag = (bool) $open;
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
        $node = new ezcDocumentRstMarkupStrongEmphasisNode(
            $properties['token'],
            $properties['openTag']
        );

        $node->nodes = $properties['nodes'];
        return $node;
    }
}

?>
