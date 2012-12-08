<?php
/**
 * File containing the ezcTemplateVariableAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents PHP variables.
 *
 * Variables consists of a string which defines the name of the variable
 * to access.
 *
 * Normal lookup of variable named $some_var.
 * <code>
 * $var = new ezcTemplateVariableAstNode( 'some_var' );
 * </code>
 * The corresponding PHP code will be:
 * <code>
 * $some_var
 * </code>
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateVariableAstNode extends ezcTemplateAstNode
{
    /**
     * The name of the variable.
     *
     * @var string
     */
    public $name;

    /**
     * Constructs a new variable.
     *
     * @param string $name The name of the variable.
     */
    public function __construct( $name )
    {
        parent::__construct();
        if ( !is_string( $name ) )
        {
            throw new ezcBaseValueException( "name", $name, 'string' );
        }
        $this->name = $name;
        $this->typeHint = self::TYPE_VALUE;
    }
}
?>
