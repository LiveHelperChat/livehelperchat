<?php
/**
 * File containing the ezcTemplateTranslationStringExtracter class
 *
 * @package TemplateTranslationTiein
 * @version 1.1.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * A visiter that can be used to extract translatable strings from a template.
 *
 * Implements the ezcTemplateTstNodeVisiter interface for visiting the nodes
 * and extracting translatable strings from them. It can be used like:
 *
 * <code>
 * <?php
 * $file = dirname( __FILE__ ) . '/test_files/test.ezt';
 * $source = new ezcTemplateSourceCode( $file, $file );
 * $source->load();
 * 
 * $parser = new ezcTemplateParser( $source, new ezcTemplate() );
 * $tst = $parser->parseIntoNodeTree();
 * 
 * $et = new ezcTemplateTranslationStringExtracter( $parser );
 * $eted = $tst->accept( $et );
 * 
 * $tr = $et->getTranslation();
 * ?>
 * </code>
 *
 * @package TemplateTranslationTiein
 * @version 1.1.1
 */
class ezcTemplateTranslationStringExtracter extends ezcTemplateTstWalker
{
    /**
     * Contains the active default translation context
     *
     * @var string
     */
    public $translationContext;

    /**
     * Contans an array of arrays, where the key is the context, and the value an array of ezcTranslationData elements.
     *
     * @var array(string=>array(ezcTranslationData)
     */
    protected $strings;

    /**
     * Initialize the transformer, after this send this object to the accept() method on a node.
     *
     * @param ezcTemplateParser $parser The main parser object.
     */
    public function __construct( ezcTemplateParser $parser )
    {
        $this->parser = $parser;
        $this->strings = array();
    }

    /**
     * Transform the literal from a TST node to a string.
     * The text will transformed by processing the escape sequences
     * according to the type which is either
     * {@link ezcTemplateLiteralTstNode::SINGLE_QUOTE single quote} or
     * {@link ezcTemplateLiteralTstNode::DOUBLE_QUOTE double quite}.
     *
     * @param ezcTemplateLiteralTstNode $type
     * @return string
     *
     * @see ezcTemplateStringTool::processSingleQuotedEscapes()
     * @see ezcTemplateStringTool::processDoubleQuotedEscapes()
     */
    public function visitLiteralTstNode( ezcTemplateLiteralTstNode $type )
    {
        // TODO: The handling of escape characters should be done in the
        //       parser and not here. Like the text/literal blocks.
        if ( $type->quoteType == ezcTemplateLiteralTstNode::SINGLE_QUOTE )
        {
            $text = ezcTemplateStringTool::processSingleQuotedEscapes( $type->value );
        }
        elseif( $type->quoteType == ezcTemplateLiteralTstNode::DOUBLE_QUOTE )
        {
            $text = ezcTemplateStringTool::processDoubleQuotedEscapes( $type->value );
        }
        else
        {
            // Numbers
            $text = $type->value;
        }

        return $text;
    }

    /**
     * visitTranslationTstNode
     *
     * @param ezcTemplateTranslationTstNode $node
     */
    public function visitTranslationTstNode( ezcTemplateTranslationTstNode $node )
    {
        $string = $node->string->accept( $this );
        $comment = $node->comment ? $node->comment->accept( $this )->value : null;
        $file = realpath( $node->source->stream );
        $line = $node->string->startCursor->line;
        $column = $node->string->startCursor->column + 1;
        // check for the translation context. If we have one, we use it. If we
        // don't have one, we check whether there is one set through a
        // tr_context block. If not, we throw an exception.
        if ( $node->context !== null )
        {
            $context = $node->context->accept( $this );
        }
        else
        {
            if ( $this->translationContext !== null )
            {
                $context = $this->translationContext;
            }
            else
            {
                throw new ezcTemplateParserException( $node->source, $node->startCursor, $node->endCursor, ezcTemplateSourceToTstErrorMessages::MSG_NO_TRANSLATION_CONTEXT );
            }
        }

        $this->strings[$context][] = new ezcTranslationData( $string, $string, $comment, ezcTranslationData::UNFINISHED, $file, $line, $column );
    }

    /**
     * visitTranslationContextTstNode
     *
     * @param ezcTemplateTranslationContextTstNode $node
     */
    public function visitTranslationContextTstNode( ezcTemplateTranslationContextTstNode $node )
    {
        $this->translationContext = $node->context->value;
    }

    /**
     * Returns an array of translation datamaps indexed by context
     *
     * @return array(string=>ezcTranslation)
     */
    function getTranslation()
    {
        $ret = array();
        foreach ( $this->strings as $context => $data )
        {
            $ret[$context] = new ezcTranslation( $data );
        }
        return $ret;
    }

    /**
     * Returns an array of translation objects indexed by context
     *
     * @return array(string=>array(ezcTranslationData))
     */
    function getStrings()
    {
        $ret = array();
        foreach ( $this->strings as $context => $data )
        {
            $ret[$context] = array();
            foreach ( $data as $translation )
            {
                $ret[$context][$translation->original] = $translation;
            }
        }
        return $ret;
    }
}
?>
