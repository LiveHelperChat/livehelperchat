<?php
/**
 * File containing the ezcTemplateParenthesisTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Block element containing an parenthesis expression.
 *
 * e.g. ( 5 + 2 )
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateParenthesisTstNode extends ezcTemplateExpressionTstNode
{
    /**
     * The bracket start character.
     * @var string
     */
    public $startBracket;

    /**
     * The bracket end character.
     * @var string
     */
    public $endBracket;

    /**
     * The root of the parsed parenthesis expression.
     */
    public $expressionRoot;

    /**
     *
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
        $this->startBracket   = '(';
        $this->endBracket     = ')';
        $this->expressionRoot = null;
    }

    public function getTreeProperties()
    {
        return array( 'startBracket'   => $this->startBracket,
                      'endBracket'     => $this->endBracket,
                      'expressionRoot' => $this->expressionRoot );
    }
}
?>
