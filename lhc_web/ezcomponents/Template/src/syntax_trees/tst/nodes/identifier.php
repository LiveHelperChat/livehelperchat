<?php
/**
 * File containing the ezcTemplateLiteralTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Builtin identifier type value in an expression.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateIdentifierTstNode extends ezcTemplateExpressionTstNode
{

    /**
     * The value of the identifier type.
     *
     * Note: This value contains null if it is not set yet, this means null is
     *       considered a literal type.
     * @var mixed
     */
    public $value;

    /**
     *
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
        $this->value = null;
    }

    public function getTreeProperties()
    {
        return array( 'value' => $this->value );
    }
}
?>
