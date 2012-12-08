<?php
/**
 * File containing the ezcDocumentWikiConfluenceTokenizer
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Tokenizer for Confluence wiki documents.
 *
 * The Confluence wiki is a quite popular wiki and part of the Atlassian
 * software stack. It is chosen, because it uses an entirely different markup
 * in some places, compared to the other wiki markup languages. The markup is
 * documented at:
 *
 * http://confluence.atlassian.com/renderer/notationhelp.action?section=all
 *
 * For the basic workings of the tokenizer see the class level documentation in
 * the ezcDocumentWikiTokenizer class.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentWikiConfluenceTokenizer extends ezcDocumentWikiTokenizer
{
    /**
     * Common whitespace characters. The vertical tab is excluded, because it
     * causes strange problems with PCRE.
     */
    const WHITESPACE_CHARS  = '[\\x20\\t]';

    /**
     * Characters ending a pure text section.
     */
    const TEXT_END_CHARS    = '/*^,#_~?+!\\\\\\[\\]{}|=\\r\\n\\t\\x20-';

    /**
     * Special characters, which do have some special meaaning and though may
     * not have been matched otherwise.
     */
    const SPECIAL_CHARS     = '/*^,#_~?+!\\\\\\[\\]{}|=-';

    /**
     * Mapping of confluence image attribute names to image start token
     * properties.
     *
     * @var array
     */
    protected $imageAttributeMapping = array(
        'width'  => 'width',
        'height' => 'height',
        'align'  => 'alignement',
    );

    /**
     * Construct tokenizer
     *
     * Create token array with regular repression matching the respective
     * token.
     *
     * @return void
     */
    public function __construct()
    {
        $this->tokens = array(
            // Match tokens which require to be at the start of a line before
            // matching the actual newlines, because they are the indicator for
            // line starts.
            array(
                'class' => 'ezcDocumentWikiTitleToken',
                'match' => '(\\A\\n(?P<value>h[1-6].' . self::WHITESPACE_CHARS . '*))S' ),
            array(
                'class' => 'ezcDocumentWikiParagraphIndentationToken',
                'match' => '(\\A\\n(?P<value>bq.)' . self::WHITESPACE_CHARS . '*)S' ),
            array(
                'class' => 'ezcDocumentWikiQuoteToken',
                'match' => '(\\A\\n(?P<value>\\{quote\\}))S' ),
            array(
                'class' => 'ezcDocumentWikiPageBreakToken',
                'match' => '(\\A(?P<match>\\n' . self::WHITESPACE_CHARS . '*(?P<value>-{4})' . self::WHITESPACE_CHARS . '*)\\n)S' ),
            array(
                'class' => 'ezcDocumentWikiBulletListItemToken',
                'match' => '(\\A\\n(?P<value>[#*-]*[*-])' . self::WHITESPACE_CHARS . '+)S' ),
            array(
                'class' => 'ezcDocumentWikiEnumeratedListItemToken',
                'match' => '(\\A\\n(?P<value>[#*-]*#)' . self::WHITESPACE_CHARS . '+)S' ),
            array(
                'class' => 'ezcDocumentWikiTableRowToken',
                'match' => '(\\A(?P<match>\\n)(?P<value>\\|))S' ),
            array(
                'class' => 'ezcDocumentWikiPluginToken',
                'match' => '(\\A\\n(?P<value>\\{([a-zA-Z]+)[^}]*\\}(?:.*?\\n\\{\\2\\})?))Ss' ),

            // Whitespaces
            array(
                'class' => 'ezcDocumentWikiNewLineToken',
                'match' => '(\\A' . self::WHITESPACE_CHARS . '*(?P<value>\\r\\n|\\r|\\n))S' ),
            array(
                'class' => 'ezcDocumentWikiWhitespaceToken',
                'match' => '(\\A(?P<value>' . self::WHITESPACE_CHARS . '+))S' ),
            array(
                'class' => 'ezcDocumentWikiEndOfFileToken',
                'match' => '(\\A(?P<value>\\x0c))S' ),

            // Escape character
            array(
                'class' => 'ezcDocumentWikiEscapeCharacterToken',
                'match' => '(\\A(?P<match>(?P<value>\\\\))[^\\\\])S' ),

            // Inline markup
            array(
                'class' => 'ezcDocumentWikiBoldToken',
                'match' => '(\\A(?P<value>\\*))S' ),
            array(
                'class' => 'ezcDocumentWikiItalicToken',
                'match' => '(\\A(?P<value>_))S' ),
            array(
                'class' => 'ezcDocumentWikiSuperscriptToken',
                'match' => '(\\A(?P<value>\\^))S' ),
            array(
                'class' => 'ezcDocumentWikiSubscriptToken',
                'match' => '(\\A(?P<value>~))S' ),
            array(
                'class' => 'ezcDocumentWikiUnderlineToken',
                'match' => '(\\A(?P<value>\\+))S' ),
            array(
                'class' => 'ezcDocumentWikiStrikeToken',
                'match' => '(\\A(?P<value>-))S' ),
            array(
                'class' => 'ezcDocumentWikiInlineQuoteToken',
                'match' => '(\\A(?P<value>\\?\\?))S' ),
            array(
                'class' => 'ezcDocumentWikiMonospaceToken',
                'match' => '(\\A(?P<value>\\{\\{|\\}\\}))S' ),
            array(
                'class' => 'ezcDocumentWikiLineBreakToken',
                'match' => '(\\A(?P<value>\\\\\\\\))S' ),
            array(
                'class' => 'ezcDocumentWikiConfluenceLinkStartToken',
                'match' => '(\\A(?P<value>\\[))S' ),
            array(
                'class' => 'ezcDocumentWikiLinkEndToken',
                'match' => '(\\A(?P<value>\\]))S' ),
            array(
                'class' => 'ezcDocumentWikiTableHeaderToken',
                'match' => '(\\A(?P<value>\\|\\|))S' ),
            array(
                'class' => 'ezcDocumentWikiSeparatorToken',
                'match' => '(\\A(?P<value>\\|))S' ),
            array(
                'class' => 'ezcDocumentWikiExternalLinkToken',
                'match' => '(\\A(?P<match>(?P<value>[a-z]+://\\S+?|mailto:\\S+?))[,.?!:;"\']?(?:' . self::WHITESPACE_CHARS . '|\\n|\\||]|$))S' ),
            array(
                'class' => 'ezcDocumentWikiImageStartToken',
                'match' => '(\\A(?P<match>(?P<value>!))\S)S' ),
            array(
                'class' => 'ezcDocumentWikiImageEndToken',
                'match' => '(\\A(?P<value>!))S' ),

            // Match text except
            array(
                'class' => 'ezcDocumentWikiTextLineToken',
                'match' => '(\\A(?P<value>[^' . self::TEXT_END_CHARS . ']+))S' ),

            // Match all special characters, which are not valid textual chars,
            // but do not have been matched by any other expression.
            array(
                'class' => 'ezcDocumentWikiSpecialCharsToken',
                'match' => '(\\A(?P<value>([' . self::SPECIAL_CHARS . '])\\2*))S' ),
        );
    }

    /**
     * Parse plugin contents
     *
     * Plugins are totally different in each wiki component and its contents
     * should not be passed through the normal wiki parser. So we fetch its
     * contents completely and let each tokinzer extract names and parameters
     * from the complete token itself.
     *
     * @param ezcDocumentWikiPluginToken $plugin
     * @return void
     */
    protected function parsePluginContents( ezcDocumentWikiPluginToken $plugin )
    {
        // Match title, property string and plugin contents
        //   {code:title=Bar.java|borderStyle=solid} ... {code}
        if ( preg_match( '(^{(?P<type>[a-zA-Z]+)(?::(?P<params>[^}]+))?}(?:(?P<text>.*){\\1})?$)s', $plugin->content, $match ) )
        {
            $plugin->type = $match['type'];

            if ( isset( $match['text'] ) )
            {
                $plugin->text = $match['text'];
            }

            // Parse plugin parameters
            if ( isset( $match['params'] ) )
            {
                $rawParams  = explode( '|', $match['params'] );
                $parameters = array();
                foreach ( $rawParams as $content )
                {
                    if ( preg_match( '(^(?P<name>[a-zA-Z]+)=(?P<value>.*)$)', $content, $match ) )
                    {
                        $parameters[$match['name']] = $match['value'];
                    }
                    else
                    {
                        $parameters[] = $content;
                    }
                }

                $plugin->parameters = $parameters;
            }
        }
    }

    /**
     * Parse confluence image descriptors
     *
     * Parse confluence image descriptors which are completely different from
     * other wiki languages, so that they cannot be handled by the default
     * parser.
     *
     * @param ezcDocumentWikiImageStartToken $token
     * @param mixed $descriptor
     * @return void
     */
    protected function parseImageDescriptor( ezcDocumentWikiImageStartToken $token, $descriptor )
    {
        if ( !preg_match_all( '((?P<name>[a-zA-Z]+)(?:=(?P<value>[^,]+))?)', $descriptor, $matches ) )
        {
            return;
        }

        // Set known properties on image start node, if available.
        foreach ( $matches['name'] as $nr => $name )
        {
            $name = strtolower( $name );
            if ( isset( $this->imageAttributeMapping[$name] ) )
            {
                $property         = $this->imageAttributeMapping[$name];
                $token->$property = $matches['value'][$nr];
            }
        }
    }

    /**
     * Filter tokens
     *
     * Method to filter tokens, after the input string ahs been tokenized. The
     * filter should extract additional information from tokens, which are not
     * generally available yet, like the depth of a title depending on the
     * title markup.
     *
     * @param array $tokens
     * @return array
     */
    protected function filterTokens( array $tokens )
    {
        $lastImageStartToken = null;
        $lastImageSeparator  = null;
        foreach ( $tokens as $nr => $token )
        {
            switch ( true )
            {
                // Extract the title / indentation level from the tokens
                // length.
                case $token instanceof ezcDocumentWikiTitleToken:
                    $token->level = (int) $token->content[1];
                    break;

                case $token instanceof ezcDocumentWikiParagraphIndentationToken:
                    $token->level = 1;
                    break;

                case $token instanceof ezcDocumentWikiBulletListItemToken:
                case $token instanceof ezcDocumentWikiEnumeratedListItemToken:
                    $token->indentation = strlen( $token->content );
                    break;

                case $token instanceof ezcDocumentWikiPluginToken:
                    $this->parsePluginContents( $token );
                    break;

                case $token instanceof ezcDocumentWikiImageStartToken:
                    // Store reference to last image start token
                    $lastImageStartToken = $token;
                    break;

                case $token instanceof ezcDocumentWikiSeparatorToken:
                    if ( $lastImageStartToken !==  null )
                    {
                        $lastImageSeparator = $token;
                    }
                    break;

                case $token instanceof ezcDocumentWikiImageEndToken:
                    if ( $lastImageSeparator === null )
                    {
                        // No relating start token and/or separator - we do not
                        // need to care.
                        continue;
                    }

                    // Aggregate all texts until the separator
                    $imageTokens = array();
                    $i           = $nr - 1;
                    while ( ( $i > 0 ) &&
                            ( $tokens[$i] !== $lastImageSeparator ) )
                    {
                        $imageTokens[] = $tokens[$i]->content;
                        unset( $tokens[$i--] );
                    }
                    unset( $tokens[$i] );

                    // Extract and combine image descritor string, and remove
                    // relating tokens, so that are not used elsewhere.
                    $descriptior = implode( '', array_reverse( $imageTokens ) );

                    $this->parseImageDescriptor( $lastImageStartToken, $descriptior );

                    // Reset image token parsing environment
                    $lastImageStartToken = null;
                    $lastImageSeparator  = null;
                    break;

                case $token instanceof ezcDocumentWikiPluginToken:
                    $this->parsePluginContents( $token );
                    break;
            }
        }

        return array_values( $tokens );
    }
}

?>
