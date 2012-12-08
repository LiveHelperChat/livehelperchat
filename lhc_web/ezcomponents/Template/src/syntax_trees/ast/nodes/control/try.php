<?php
/**
 * File containing the ezcTemplateTryAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents a try control structure.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateTryAstNode extends ezcTemplateStatementAstNode
{
    /**
     * The body element.
     * @var ezcTemplateBodyAstNode
     */
    public $body;

    /**
     * Array of catche statements which are placed after the try body.
     * @var array(ezcTemplateCatchAstNode)
     */
    public $catches;

    /**
     * Initialize with function name code and optional arguments
     *
     * @param ezcTemplateBodyAstNode $body
     * @param array(ezcTemplateCatchAstNode) $catches
     */
    public function __construct( ezcTemplateBodyAstNode $body = null, Array $catches = null )
    {
        parent::__construct();
        $this->body = $body;
        $this->catches = array();

        if ( $catches !== null )
        {
            foreach ( $catches as $id => $catch )
            {
                if ( !$catch instanceof ezcTemplateCatchAstNode )
                {
                     throw new ezcBaseValueException( "catches[$id]", $catch, 'ezcTemplateCatchAstNode' );
                }
                $this->catches[] = $catch;
            }
        }
    }
}
?>
