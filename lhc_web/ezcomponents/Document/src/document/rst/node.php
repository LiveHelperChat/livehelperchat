<?php
/**
 * File containing the ezcDocumentRstNode struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Struct for RST document document abstract syntax tree nodes
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocumentRstNode extends ezcBaseStruct // implements RecursiveIterator
{
    /**
     * Document AST node
     */
    const DOCUMENT             = 1;

    /**
     * Section AST node
     */
    const SECTION              = 1;

    /**
     * Title AST node
     */
    const TITLE                = 2;

    /**
     * Paragraph AST node
     */
    const PARAGRAPH            = 3;

    /**
     * Text line AST node
     */
    const TEXT_LINE            = 4;

    /**
     * Blockquote AST node
     */
    const BLOCKQUOTE           = 5;

    /**
     * Blockquote anotation AST node
     */
    const ANNOTATION           = 6;

    /**
     * Literal block AST node
     */
    const LITERAL_BLOCK        = 7;
    /**
     * Comment AST node
     */

    const COMMENT              = 8;

    /**
     * Page transition AST node
     */
    const TRANSITION           = 9;

    /**
     * Field list AST node
     */
    const FIELD_LIST           = 10;

    /**
     * Definition list AST node
     */
    const DEFINITION_LIST      = 11;

    /**
     * Line block AST node
     */
    const LINE_BLOCK           = 12;

    /**
     * Line block line AST node
     */
    const LINE_BLOCK_LINE      = 13;

    /**
     * Definition list item AST node
     */
    const DEFINITION_LIST_LIST = 14;

    /**
     * Bullet list item AST node
     */
    const BULLET_LIST          = 20;

    /**
     * Enumerated list item AST node
     */
    const ENUMERATED_LIST      = 21;

    /**
     * Bullet list AST node
     */
    const BULLET_LIST_LIST     = 22;

    /**
     * Enumerated list AST node
     */
    const ENUMERATED_LIST_LIST = 23;

    /**
     * Emphasis markup AST node
     */
    const MARKUP_EMPHASIS      = 30;

    /**
     * Strong emphasis markup AST node
     */
    const MARKUP_STRONG        = 31;

    /**
     * Interpreted text markup AST node
     */
    const MARKUP_INTERPRETED   = 32;

    /**
     * Inline literal markup AST node
     */
    const MARKUP_LITERAL       = 33;

    /**
     * Substitution reference markup AST node
     */
    const MARKUP_SUBSTITUTION  = 34;

    /**
     * Anonymous hyperlink AST node
     */
    const LINK_ANONYMOUS       = 40;

    /**
     * External Reference AST node
     */
    const LINK_REFERENCE       = 41;

    /**
     * Internal Target AST node
     */
    const TARGET               = 42;

    /**
     * Internal Reference AST node
     */
    const REFERENCE            = 43;

    /**
     * Inline Literal AST node
     */
    const LITERAL              = 50;

    /**
     * Substitution target AST node
     */
    const SUBSTITUTION         = 51;

    /**
     * Directive AST node
     */
    const DIRECTIVE            = 52;

    /**
     * Named reference target AST node
     */
    const NAMED_REFERENCE      = 53;

    /**
     * Footnote target AST node
     */
    const FOOTNOTE             = 54;

    /**
     * Anonymous reference target AST node
     */
    const ANON_REFERENCE       = 55;

    /**
     * Table node AST node
     */
    const TABLE                = 100;

    /**
     * Table head node AST node
     */
    const TABLE_HEAD           = 101;

    /**
     * Table body node AST node
     */
    const TABLE_BODY           = 102;

    /**
     * Table row node AST node
     */
    const TABLE_ROW            = 103;

    /**
     * Table cell node AST node
     */
    const TABLE_CELL           = 104;

    /**
     * Line of node in source file.
     *
     * @var int
     */
    public $line;

    /**
     * Character position of node in source file.
     *
     * @var int
     */
    public $position;

    /**
     * Node type
     *
     * @var int
     */
    public $type;

    /**
     * Child nodes
     *
     * @var mixed
     */
    public $nodes = array();

    /**
     * Optional reference to token, not available for all nodes.
     *
     * @var ezcDocumentRstToken
     */
    public $token = null;

    /**
     * Optional paragraph identifier, to reference the paragraph using internal
     * links.
     *
     * @var string
     */
    public $identifier = null;

    /**
     * Construct RST node
     *
     * @param ezcDocumentRstToken $token
     * @param int $type
     * @return void
     */
    public function __construct( ezcDocumentRstToken $token, $type )
    {
        $this->type     = $type;
        $this->line     = $token->line;
        $this->position = $token->position;
        $this->token    = $token;
    }

    /**
     * Get node name from type
     *
     * Return a user readable name from the numeric node type.
     *
     * @param int $type
     * @return string
     */
    public static function getTokenName( $type )
    {
        $names = array(
            self::DOCUMENT             => 'Document',
            self::SECTION              => 'Section',
            self::TITLE                => 'Title',
            self::PARAGRAPH            => 'Paragraph',
            self::BULLET_LIST          => 'Bullet list item',
            self::ENUMERATED_LIST      => 'Enumerated list item',
            self::BULLET_LIST_LIST     => 'Bullet list',
            self::ENUMERATED_LIST_LIST => 'Enumerated list',
            self::TEXT_LINE            => 'Text line',
            self::BLOCKQUOTE           => 'Blockquote',
            self::ANNOTATION           => 'Blockquote anotation',
            self::COMMENT              => 'Comment',
            self::TRANSITION           => 'Page transition',
            self::FIELD_LIST           => 'Field list',
            self::DEFINITION_LIST      => 'Definition list item',
            self::DEFINITION_LIST_LIST => 'Definition list',
            self::LINE_BLOCK           => 'Line block',
            self::LINE_BLOCK_LINE      => 'Line block line',
            self::LITERAL_BLOCK        => 'Literal block',
            self::MARKUP_EMPHASIS      => 'Emphasis markup',
            self::MARKUP_STRONG        => 'Strong emphasis markup',
            self::MARKUP_INTERPRETED   => 'Interpreted text markup',
            self::MARKUP_LITERAL       => 'Inline literal markup',
            self::MARKUP_SUBSTITUTION  => 'Substitution reference markup',
            self::LINK_ANONYMOUS       => 'Anonymous hyperlink',
            self::LINK_REFERENCE       => 'External Reference',
            self::TARGET               => 'Internal Target',
            self::REFERENCE            => 'Internal Reference',
            self::LITERAL              => 'Inline Literal',
            self::SUBSTITUTION         => 'Substitution target',
            self::DIRECTIVE            => 'Directive',
            self::NAMED_REFERENCE      => 'Named reference target',
            self::FOOTNOTE             => 'Footnote target',
            self::ANON_REFERENCE       => 'Anonymous reference target',
            self::TABLE                => 'Table node',
            self::TABLE_HEAD           => 'Table head node',
            self::TABLE_BODY           => 'Table body node',
            self::TABLE_ROW            => 'Table row node',
            self::TABLE_CELL           => 'Table cell node',
        );

        if ( !isset( $names[$type] ) )
        {
            return 'Unknown';
        }

        return $names[$type];
    }

    /**
     * Return node content, if available somehow
     *
     * @return string
     */
    protected function content()
    {
        return '';
    }

    /**
     * Get dump of document
     *
     * @param int $depth
     * @return string
     */
    public function dump( $depth = 0 )
    {
        $return = sprintf( "%s%s [%s] (%d, %d)\n",
            ( $depth === 0 ? "" : str_repeat( "  ", $depth - 1 ) . "- " ),
            self::getTokenName( $this->type ),
            $this->content(),
            ( isset( $this->token ) ? $this->token->line : 0 ),
            ( isset( $this->token ) ? $this->token->position : 0 )
        );

        foreach ( $this->nodes as $nr => $node )
        {
            if ( !$node instanceof ezcDocumentRstNode )
            {
                $return .= "\n=> Broken child node: $nr.\n";
                continue;
            }

            $return .= $node->dump( $depth + 1 );
        }

        $return .= sprintf( "%s/ %s\n",
            ( $depth === 0 ? "" : str_repeat( "  ", $depth - 1 ) . "- " ),
            self::getTokenName( $this->type )
        );

        return $return;
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
        return new ezcDocumentRstNode(
            $properties['type'],
            $properties['nodes']
        );
    }
}

?>
