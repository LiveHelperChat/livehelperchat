<?php
/**
 * File containing the ezcTemplateCodeTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Element interface representing code found in the template source code.
 *
 * Code is abstract and used by both text and block nodes, like the EBNF:
 * <code>
 * Code ::= ( Text | Block )*
 * </code>
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
abstract class ezcTemplateCodeTstNode extends ezcTemplateTstNode
{
    /**
     * Constructs a new ezcTemplateCodeTstNode
     * 
     * @param ezcTemplateSourceCode $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
    }




}
?>
