<?php
/**
 * File containing the ezcTemplateTranslationTstNode
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Control structure: tr.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateTranslationTstNode extends ezcTemplateBlockTstNode
{
    /**
     * The translatable string
     *
     * @var ezcTemplateLiteralTstNode
     */
    public $string;

    /**
     * The context variable.
     *
     * @var ezcTemplateLiteralTstNode
     */
    public $context;

    /**
     * The optional comment
     *
     * @var ezcTemplateLiteralTstNode
     */
    public $comment;

    /**
     * The variables.
     *
     * @var ezcTemplateExpressionTstNode
     */
    public $variables;

    /**
     * Constructs a new ezcTemplateForeachLoopTstNode.
     *
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
        $this->string = $this->context = $this->comment = $this->variables = null;
        $this->name = 'tr';
        $this->isNestingBlock = false;
    }

    /**
     * Returns the tree properties.
     *
     * @return array(string=>mixed)
     */
    public function getTreeProperties()
    {
        return array( 'string'    => $this->string,
                      'context'   => $this->context,
                      'comment'   => $this->comment,
                      'variables' => $this->variables );
    }
}
?>
