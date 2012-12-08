<?php
/**
 * File containing the ezcTemplateCustomBlockTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Custom block elements in parser trees.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateCustomBlockTstNode extends ezcTemplateBlockTstNode
{
    /**
     * All parameters of the custom block as an associative array.
     * The key is the parameter name and the value is another element object.
     *
     * @var array
     */
    public $customParameters;

    /**
     * The definition block.
     * 
     * @var ezcTemplateCustomBlockDefinition
     */
    public $definition;

    /**
     * The named parameters.
     *
     * @var array
     */
    public $namedParameters = array();

    /**
     * Constructs a new custom block
     *
     * @param ezcTemplateSourceCode $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
        $this->customParameters = array();
    }
    
    /**
     * Returns the tree properties.
     *
     * @return array(string=>mixed)
     */
    public function getTreeProperties()
    {
        return array( 'name'             => $this->name,
                      'isClosingBlock'   => $this->isClosingBlock,
                      'isNestingBlock'   => $this->isNestingBlock,
                      'customParameters' => $this->customParameters,
                      'children'         => $this->children );
    }

    /**
     * Adds the element $parameter as a parameter of this custom block element.
     *
     * @param string $parameterName The name of the parameter.
     * @param ezcTemplateTstNode $parameter The element object to use as parameter
     */
    public function appendParameter( $parameterName, ezcTemplateTstNode $nameElement, ezcTemplateTstNode $parameter )
    {
        $this->customParameters[$parameterName] = array( $nameElement,
                                                         $parameter );
    }

    /**
     * Checks if the parameter named $parameterName is set in the block and the result.
     *
     * @param string $parameterName The name of the parameter.
     * @return bool
     */
    public function hasParameter( $parameterName )
    {
        return isset( $this->customParameters[$parameterName] );
    }
}
?>
