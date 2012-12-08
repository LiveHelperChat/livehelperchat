<?php
/**
 * File containing the ezcTemplateTypeCastAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * This node represents a type cast.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateTypeCastAstNode extends ezcTemplateAstNode
{
    /**
     * The constant type.
     *
     * @var string
     */
    public $type;

    /**
     * The original value that needs to be cast.
     *
     * @var ezcTemplateAstNode
     */
    public $value;

    /**
     * Construct a new type cast.
     *
     * @param string $castToType
     * @param ezcTemplateAstNode $value
     */
    public function __construct( $castToType, $value )
    {
        parent::__construct();

        // TODO, check for int, string, array, etc.

        $this->type  = $castToType;
        $this->value = $value;
    }
}
?>
