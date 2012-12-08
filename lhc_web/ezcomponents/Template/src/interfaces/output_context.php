<?php
/**
 * File containing the ezcTemplateOutputContext class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Controls output handling in the template engine.
 *
 * The template engine will use the various methods in an output context object
 * to control how the end result is.
 *
 * The compiler will use the transformOutput() method when generating PHP
 * structures for output.
 *
 * @package Template
 * @version 1.4.2
 */

interface ezcTemplateOutputContext
{
    /**
     * Transforms an expressions so it can be displayed in the current output context
     * correctly.
     *
     * @param ezcTemplateAstNode $node
     * @return ezcTemplateAstNode The new AST node which should replace $node.
     */
    public function transformOutput( ezcTemplateAstNode $node );

    /**
     * Returns the unique identifier for the context handler. This is used to
     * uniquely identify the handler, e.g. it is included in the path of
     * compiled files.
     *
     * @return string
     */
    public function identifier();

}
?>
