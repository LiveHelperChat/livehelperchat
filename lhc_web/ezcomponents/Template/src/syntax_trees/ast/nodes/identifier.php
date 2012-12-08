<?php
/**
 * File containing the ezcTemplateIdentifierAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * This node represents an identifier. 
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateIdentifierAstNode extends ezcTemplateAstNode
{
    /**
     * The name of the identifier.
     *
     * @var string
     */
    public $name;

    /**
     * Constructs a new ezcTemplateIdentifierAstNode
     *
     * @param string $name The name of the variable.
     */
    public function __construct( $name )
    {
        parent::__construct();

        $this->typeHint = self::TYPE_ARRAY | self::TYPE_VALUE;
        /*
        if ( !is_string( $name ) )
        {
            throw new ezcBaseValueException( "name", $name, 'string' );
        }
        */

        $this->name = $name;
    }
}
?>
