<?php
/**
 * File containing the ezcTemplateDeclarationTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Declares a new variable.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateDeclarationTstNode extends ezcTemplateBlockTstNode
{
    /**
     * The type of the variable.
     *
     * @var int
     */
    public $type;

    /**
     * The variable itself.
     *
     * @var ezcTemplateVariableTstNode
     */
    public $variable;

    /**
     * The expression assigned to the variable.
     *
     * @var ezcTemplateTstNode
     */
    public $expression;

    /**
     * Constructs a new ezcTemplateDeclarationTstNode.
     *
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
        $this->type = "normal";
        $this->variable = null;
        $this->expression = null;
    }

    /**
     * Returns the tree properties.
     *
     * @return array(string=>mixed)
     */
    public function getTreeProperties()
    {
        return array( 'type'       => $this->type,
                      'variable'   => $this->variable,
                      'expression' => $this->expression );
    }
}
?>
