<?php
/**
 * File containing the ezcDocumentRstDirectiveNode struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The AST node for RST document directives
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentRstDirectiveNode extends ezcDocumentRstBlockNode
{
    /**
     * Directive target identifier
     *
     * @var string
     */
    public $identifier;

    /**
     * Directive paramters
     *
     * @var string
     */
    public $parameters;

    /**
     * Directive content tokens
     *
     * @var array
     */
    public $tokens;

    /**
     * Directive options
     *
     * @var array
     */
    public $options;

    /**
     * Construct RST document node
     *
     * @param ezcDocumentRstToken $token
     * @param string $identifier
     * @return void
     */
    public function __construct( ezcDocumentRstToken $token, $identifier )
    {
        // Perhaps check, that only node of type section and metadata are
        // added.
        parent::__construct( $token, self::DIRECTIVE );
        $this->identifier = $identifier;
    }

    /**
     * Set state after var_export
     *
     * @param array $properties
     * @return void
     * @ignore
     */
    public static function __set_state( $properties )
    {
        $node = new ezcDocumentRstDirectiveNode(
            $properties['token'],
            $properties['identifier']
        );

        $node->nodes       = $properties['nodes'];
        $node->parameters  = $properties['parameters'];
        $node->options     = $properties['options'];
        $node->indentation = isset( $properties['indentation'] ) ? $properties['indentation'] : 0;

        if ( isset( $properties['tokens'] ) )
        {
            $node->tokens = $properties['tokens'];
        }

        return $node;
    }
}

?>
