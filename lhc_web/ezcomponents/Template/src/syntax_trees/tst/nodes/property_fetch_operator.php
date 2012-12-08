<?php
/**
 * File containing the ezcTemplatePropertyFetchOperatorTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Fetching of property value in an expression.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplatePropertyFetchOperatorTstNode extends ezcTemplateOperatorTstNode
{
    /**
     * The source operand element which the fetch is executed on.
     *
     * @var ezcTemplateTstNode
     */
    public $sourceOperand;

    /**
     * The element which contains the name of the property.
     *
     * @var ezcTemplateTstNode
     */
    public $property;

    /**
     *
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end,
                             11, 2, self::LEFT_ASSOCIATIVE,
                             '->' );
        $this->sourceOperand = null;
        $this->property = null;
    }

    public function getTreeProperties()
    {
        return array( 'symbol'         => $this->symbol,
                      'sourceOperand'  => $this->sourceOperand,
                      'property'       => $this->property );
    }

    public function appendParameter( $element )
    {
        if ( $this->sourceOperand === null )
            $this->sourceOperand = $element;
        else
            $this->property = $element;
        $this->parameters[] = $element;
    }
}
?>
