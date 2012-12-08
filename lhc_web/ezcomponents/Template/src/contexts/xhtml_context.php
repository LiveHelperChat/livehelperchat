<?php
/**
 * File containing the ezcTemplateXhtmlContext class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The ezcTemplateXhtmlContext class escapes special HTML characters in the
 * output.
 *
 * @package Template
 * @version 1.4.2
 */

class ezcTemplateXhtmlContext implements ezcTemplateOutputContext
{
    /**
     * Escapes special HTML characters in the output.
     *
     * @param ezcTemplateAstNode $node
     * @return ezcTemplateAstNode The new AST node which should replace $node.
     */
    public function transformOutput( ezcTemplateAstNode $node )
    {
        return new ezcTemplateFunctionCallAstNode( "htmlspecialchars", array( $node ) );
    }

    /**
     * Returns the unique identifier for the context handler.
     *
     * @return string
     */
    public function identifier()
    {
        return 'xhtml';
    }
}
?>
