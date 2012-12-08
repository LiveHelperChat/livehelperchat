<?php
/**
 * File containing the ezcTemplateArrayFetchOperatorTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Fetching of array value in an expression.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateArrayFetchOperatorTstNode extends ezcTemplateOperatorTstNode
{
    /**
     * The source operand element which the fetch is executed on.
     *
     * @var ezcTemplateTstNode
     */
    public $sourceOperand;

    /**
     * List of array keys to lookup expressed as parser elements.
     *
     * @var array(ezcTemplateTstNode)
     */
    public $arrayKeys;

    /**
     * Constructs a new ezcTemplateArrayFetchOperatorTstNode
     *
     * @param ezcTemplateSourceCode $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end,
                             11, 1, self::RIGHT_ASSOCIATIVE,
                             '[...]' );
        $this->sourceOperand = null;
        $this->arrayKeys = array();
    }

    /**
     * Returns the tree properties.
     *
     * @return array(string=>mixed) 
     */
    public function getTreeProperties()
    {
        return array( 'symbol'         => $this->symbol,
                      'sourceOperand'  => $this->sourceOperand,
                      'arrayKeys'      => $this->arrayKeys );
    }

    /**
     * Appends a parameter to this node.
     *
     * @param ezcTemplateTstNode $element
     * @return void
     */
    public function appendParameter( $element )
    {
        if ( $this->sourceOperand === null )
            $this->sourceOperand = $element;
        else
            $this->arrayKeys[] = $element;
        $this->parameters = array_merge( array( $this->sourceOperand ),
                                         $this->arrayKeys );
    }
}
?>
