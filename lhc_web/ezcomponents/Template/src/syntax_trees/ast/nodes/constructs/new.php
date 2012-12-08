<?php
/**
 * File containing the ezcTemplateEchoAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents a new construct.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateNewAstNode extends ezcTemplateParameterizedAstNode
{
    /**
     * The class name to create.
     *
     * @var string
     */
    public $class;

    /**
     * Constructs a 'new' class element.
     *
     * @param string $class 
     * @param array(ezcTemplateAstNode) $functionArguments
     */
    public function __construct( $class = null, array $functionArguments = null )
    {
        parent::__construct();
        $this->class = $class;

        if ( $functionArguments !== null )
        {
            foreach ( $functionArguments as $argument )
            {
                $this->appendParameter( $argument );
            }
        }
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
