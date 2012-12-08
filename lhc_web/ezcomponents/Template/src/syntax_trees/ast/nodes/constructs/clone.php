<?php
/**
 * File containing the ezcTemplateCloneAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Represents an clone construct.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateCloneAstNode extends ezcTemplateStatementAstNode
{
    /**
     * Object to clone.
     *
     * @var ezcTemplateAstNode
     */
    public $object;

    /**
     * Constructs a new ezcTemplateCloneAstNode
     *
     * @param ezcTemplateAstNode $object
     */
    public function __construct( $object = null )
    {
        parent::__construct();
        $this->object = $object;
    }

    /**
     * Validates the output parameters against their constraints.
     *
     * @return void
     */
    public function validate()
    {
    }
}
?>
