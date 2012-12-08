<?php
/**
 * File containing the ezcDocumentRstLinkNode struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The base link AST node
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
abstract class ezcDocumentRstLinkNode extends ezcDocumentRstNode
{
    /**
     * The target the link points too.
     *
     * @var string
     */
    public $target = false;
}

?>
