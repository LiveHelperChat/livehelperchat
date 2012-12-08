<?php
/**
 * File containing the ezcTemplateLoopTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Misc flow control blocks: break/continue/skip.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateLoopTstNode extends ezcTemplateBlockTstNode
{
    public $name;

    /**
     * Constructor.
     *
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end, $name = null )
    {
        parent::__construct( $source, $start, $end );
        $this->name = $name;
        $this->isNestingBlock = false;
    }

    public function getTreeProperties()
    {
        return array( 'name' => $this->name );
    }

}
?>
