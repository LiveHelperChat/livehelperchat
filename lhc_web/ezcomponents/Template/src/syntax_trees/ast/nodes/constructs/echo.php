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
 * Represents an echo construct.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateEchoAstNode extends ezcTemplateStatementAstNode
{
    /**
     * List of code elements which are going to be evaluated and output.
     * @var array(ezcTemplateAstNode)
     */
    public $outputList;

    /**
     * Constructs a new ezcTemplateEchoAstNode
     *
     * @param array(ezcTemplateAstNode) $outputList
     */
    public function __construct( Array $outputList = null )
    {
        parent::__construct();
        $this->outputList = array();

        if ( $outputList !== null )
        {
            foreach ( $outputList as $output )
            {
                $this->appendOutput( $output );
            }
        }
    }

    /**
     * Append a new output element to the current list.
     *
     * @param ezcTemplateAstNode $output
     * @return void
     */
    public function appendOutput( ezcTemplateAstNode $output )
    {
        $this->outputList[] = $output;
    }

    /**
     * Returns the current list of output elements.
     * @return array(ezcTemplateAstNode)
     */
    public function getOutputList()
    {
        return $this->outputList;
    }

    /**
     * Validates the output parameters against their constraints.
     *
     * @throws ezcTemplateInternalException if the constraints are not met.
     * @return void
     */
    public function validate()
    {
        if ( count( $this->outputList ) == 0 )
        {
            throw new ezcTemplateInternalException( "Too few output parameters for class <" . get_class( $this ) . ">, needs at least 1 but got 0." );
        }
    }
}
?>
