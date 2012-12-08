<?php
/**
 * File containing the ezcTemplateCacheBlockTstNode  class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * The dynamic block node contains the possible the dynamic block.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateCacheBlockTstNode extends ezcTemplateBlockTstNode
{
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
        return array( 'keys'      => $this->keys,
                      'ttl'       => $this->ttl,
                      'children'  => $this->children );
    }
}
?>
