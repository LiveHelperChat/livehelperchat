<?php
/**
 * File containing the ezcTemplateDynamicStringAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents a dynamic PHP string.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateDynamicStringAstNode extends ezcTemplateParameterizedAstNode
{
    /**
     * Initialize dynamic string with parameter constraints.
     */
    public function __construct()
    {
        parent::__construct( 0, false );
    }
}
?>
