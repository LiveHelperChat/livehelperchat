<?php
/**
 * File containing the ezcTemplateCacheTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * The cache node contains the possible caching information.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateCacheTstNode extends ezcTemplateExpressionTstNode
{
    const TYPE_CACHE_TEMPLATE = 1;
    const TYPE_CACHE_BLOCK = 2;

    public $type = 0; 

    public $templateCache = false;

    public $isClosingBlock = false;

    public $keys = array();

    public $ttl = null;

    /**
     *
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
    }

    public function getTreeProperties()
    {
        return array( 'templateCache' => $this->templateCache);
    }

    /**
     * Checks if the given node can be attached to its parent.
     *
     * @throws ezcTemplateParserException if the node cannot be attached.
     * @param ezcTemplateTstNode $parentElement
     * @return void
     */
    public function canAttachToParent( $parentElement )
    {
        // Must be TYPE_CACHE_TEMPLATE and in the root, not in a template block

        $p = $parentElement;

        if ( $this->type === self::TYPE_CACHE_TEMPLATE && !$p instanceof ezcTemplateProgramTstNode )
        {
            throw new ezcTemplateParserException( $this->source, $this->startCursor, $this->startCursor, 
                "{cache_template} cannot be declared inside a template block." );
        }
    }
}
?>
