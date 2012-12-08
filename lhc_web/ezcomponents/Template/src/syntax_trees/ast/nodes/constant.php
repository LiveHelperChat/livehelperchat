<?php
/**
 * File containing the ezcTemplateConstantAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents PHP constants.
 *
 * Creating the type is done by simply passing the constant name to the
 * constructor which will take care of storing it and exporting it to PHP
 * code later on.
 *
 * <code>
 * $c = new ezcTemplateConstantAstNode( 'E_NOTICE' );
 * </code>
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateConstantAstNode extends ezcTemplateAstNode
{
    /**
     * The value for the constant.
     */
    public $value;

    /**
     * Constructs a new ezcTemplateConstantAstNode
     *
     * @param mixed $value The value of constant.
     */
    public function __construct( $value )
    {
        parent::__construct();
        $this->value = $value;
    }

}
?>
