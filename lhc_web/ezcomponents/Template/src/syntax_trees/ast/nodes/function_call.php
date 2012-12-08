<?php
/**
 * File containing the ezcTemplateFunctionCallAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents a function call.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateFunctionCallAstNode extends ezcTemplateParameterizedAstNode
{
    /**
     * The name of the function to call.
     * @var string
     */
    public $name;

    /**
     * Checks and sets the type hint.
     *
     * @return void
     */
    public function checkAndSetTypeHint()
    {
        $this->typeHint = self::TYPE_ARRAY | self::TYPE_VALUE; 
    }

    /**
     * Initialize with function name code and optional arguments
     *
     * @param string $name
     * @param array(ezcTemplateAstNode) $functionArguments
     */
    public function __construct( $name, Array $functionArguments = null )
    {
        parent::__construct( 1, false );
        $this->name = $name;
        $this->typeHint = self::TYPE_ARRAY | self::TYPE_VALUE;

        if ( $functionArguments !== null )
        {
            foreach ( $functionArguments as $argument )
            {
                $this->appendParameter( $argument );
            }
        }
    }
}
?>
