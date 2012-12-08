<?php
/**
 * File containing the ezcDocumentWikiMediawikiTokenizer
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Tokenizer for Mediawiki wiki documents.
 *
 * Mediawiki is probably the most popular wiki, and the driving force behing
 * Wikipedia. The markup has a lot extension, but the basics are defined at:
 *
 * http://www.mediawiki.org/wiki/Markup_spec
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentWikiMediawikiTokenizer extends ezcDocumentWikiTokenizer
{
    /**
     * Common whitespace characters. The vertical tab is excluded, because it
     * causes strange problems with PCRE.
     */
    const WHITESPACE_CHARS  = '[\\x20\\t]';

    /**
     * Regular sub expression to match newlines.
     */
    const NEW_LINE  = '(?:\\r\\n|\\r|\\n)';

    /**
     * Characters ending a pure text section.
     */
    const TEXT_END_CHARS    = '/*^,\'_<\\\\\\[\\]{}()|=\\r\\n\\t\\x20';

    /**
     * Special characters, which do have some special meaaning and though may
     * not have been matched otherwise.
     */
    const SPECIAL_CHARS     = '/*^,\'_<>\\\\\\[\\]{}()|=';

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
        /*
            // Match tokens which require to be at the start of a line before
            // matching the actual newlines, because they are the indicator for
            // line starts.
            array(
                'class' => 'ezcDocumentWikiTitleToken',
                'match' => '(\\A\\n(?P<value>=+)' . self::WHITESPACE_CHARS . '+)S' ),
            array(
                'class' => 'ezcDocumentWikiTitleToken',
                'match' => '(\\A(?P<match>' . self::WHITESPACE_CHARS . '+(?P<value>=+))\\n)S' ),
        */
            array(
                'class' => 'ezcDocumentWikiBulletListItemToken',
                'match' => '(\\A\\n(?P<value>[:*#]*\\*)' . self::WHITESPACE_CHARS . '*)S' ),
            array(
                'class' => 'ezcDocumentWikiEnumeratedListItemToken',
                'match' => '(\\A\\n(?P<value>[:*#]*#)' . self::WHITESPACE_CHARS . '*)S' ),
            array(
                'class' => 'ezcDocumentWikiDefinitionListItemToken',
                'match' => '(\\A\\n(?P<value>[:*#]*:)' . self::WHITESPACE_CHARS . '*)S' ),
            array(
                'class' => 'ezcDocumentWikiLiteralLineToken',
                'match' => '(\\A\\n(?P<value>' . self::WHITESPACE_CHARS . '))SUs' ),
        /*
            array(
                'class' => 'ezcDocumentWikiPageBreakToken',
                'match' => '(\\A(?P<match>\n' . self::WHITESPACE_CHARS . '*(?P<value>-{4})' . self::WHITESPACE_CHARS . '*)\\n)S' ),
            array(
                'class' => 'ezcDocumentWikiTableRowToken',
                'match' => '(\\A(?P<match>\\n)(?P<value>\\|))S' ),
        */
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
        /*
            // Escape character
            array(
                'class' => 'ezcDocumentWikiEscapeCharacterToken',
                'match' => '(\\A(?P<value>~))S' ),
        */

            // Inline markup
            array(
                'class' => 'ezcDocumentWikiMediawikiEmphasisToken',
                'match' => '(\\A(?P<value>\'{2,}))S' ),
            array(
                'class' => 'ezcDocumentWikiTextLineToken',
                'match' => '(\\A(?P<match><nowiki>(?P<value>.+)</nowiki>))SUsi' ),
        /*
            array(
                'class' => 'ezcDocumentWikiMonospaceToken',
                'match' => '(\\A(?P<value>##))S' ),
            array(
                'class' => 'ezcDocumentWikiSuperscriptToken',
                'match' => '(\\A(?P<value>\\^\\^))S' ),
            array(
                'class' => 'ezcDocumentWikiSubscriptToken',
                'match' => '(\\A(?P<value>,,))S' ),
            array(
                'class' => 'ezcDocumentWikiUnderlineToken',
                'match' => '(\\A(?P<value>__))S' ),
            array(
                'class' => 'ezcDocumentWikiInlineLiteralToken',
                'match' => '(\\A\\{\\{\\{(?P<value>.+?\\}*)\\}\\}\\})Ss' ),
            array(
                'class' => 'ezcDocumentWikiLineBreakToken',
                'match' => '(\\A(?P<value>\\\\\\\\))S' ),
            array(
                'class' => 'ezcDocumentWikiImageStartToken',
                'match' => '(\\A(?P<value>\\{\\{))S' ),
            array(
                'class' => 'ezcDocumentWikiImageEndToken',
                'match' => '(\\A(?P<value>\\}\\}))S' ),
        */
            array(
                'class' => 'ezcDocumentWikiLinkStartToken',
                'match' => '(\\A(?P<value>\\[\\[))S' ),
            array(
                'class' => 'ezcDocumentWikiLinkEndToken',
                'match' => '(\\A(?P<value>\\]\\]))S' ),
        /*
            array(
                'class' => 'ezcDocumentWikiTableHeaderToken',
                'match' => '(\\A(?P<value>\\|=))S' ),
        */
            array(
                'class' => 'ezcDocumentWikiSeparatorToken',
                'match' => '(\\A(?P<value>\\|))S' ),
        /*
            array(
                'class' => 'ezcDocumentWikiInterWikiLinkToken',
                'match' => '(\\A(?P<value>([A-Za-z]+):(?:[A-Z][a-z0-9_-]+){2,}))S' ),
            array(
                'class' => 'ezcDocumentWikiInternalLinkToken',
                'match' => '(\\A(?P<value>(?:[A-Z][a-z]+){2,}))S' ),
            array(
                'class' => 'ezcDocumentWikiExternalLinkToken',
                'match' => '(\\A(?P<match>(?P<value>[a-z]+://\S+?))[,.?!:;"\']?(?:' . self::WHITESPACE_CHARS . '|\\n|\\||]]|\\||$))S' ),

            // Handle plugins
            array(
                'class' => 'ezcDocumentWikiPluginToken',
                'match' => '(\\A<<(?P<value>.*?)>>)Ss' ),
        */

            // Match text except
            array(
                'class' => 'ezcDocumentWikiTextLineToken',
                'match' => '(\\A(?P<value>[^' . self::TEXT_END_CHARS . ']+))S' ),
        );
    }

    /**
     * Tokenize the given string
     *
     * The method tries to tokenize the passed strings and returns an array of
     * ezcDocumentWikiToken struct on succes, or throws a
     * ezcDocumentTokenizerException, if something could not be matched by any
     * token.
     *
     * @param string $string
     * @return array
     */
    public function tokenizeString( $string )
    {
        // Remove all comments, since they are ignored anyways and make some
        // checks a lot harder.
        $string = preg_replace( '(' . self::NEW_LINE . '?<!--.*?(?:-->|\\Z))Ss', '', $string );

        return parent::tokenizeString( $string );
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
        foreach ( $tokens as $nr => $token )
        {
            switch ( true )
            {
                // Extract the title / indentation level from the tokens
                // length.
                case $token instanceof ezcDocumentWikiTitleToken:
                case $token instanceof ezcDocumentWikiParagraphIndentationToken:
                    $token->level = strlen( trim( $token->content ) );
                    break;
            }
        }

        return $tokens;
    }
}

?>
