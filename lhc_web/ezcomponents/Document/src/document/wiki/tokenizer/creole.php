<?php
/**
 * File containing the ezcDocumentWikiCreoleTokenizer
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Tokenizer for Creole wiki documents.
 *
 * The Creole wiki syntax is a started effort to unify wiki markup languages.
 * Its documentation can be found at:
 *
 * http://www.wikicreole.org/
 *
 * For the basic workings of the tokenizer see the class level documentation in
 * the ezcDocumentWikiTokenizer class.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentWikiCreoleTokenizer extends ezcDocumentWikiTokenizer
{
    /**
     * Common whitespace characters. The vertical tab is excluded, because it
     * causes strange problems with PCRE.
     */
    const WHITESPACE_CHARS  = '[\\x20\\t]';

    /**
     * Characters ending a pure text section.
     */
    const TEXT_END_CHARS    = '/*^,#_~\\\\\\[\\]{}|=\\r\\n\\t\\x20-';

    /**
     * Special characters, which do have some special meaaning and though may
     * not have been matched otherwise.
     */
    const SPECIAL_CHARS     = '/*^,#_~\\\\\\[\\]{}|=-';

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
                'match' => '(\\A\\n(?P<value>=+)' . self::WHITESPACE_CHARS . '+)S' ),
            array(
                'class' => 'ezcDocumentWikiTitleToken',
                'match' => '(\\A(?P<match>' . self::WHITESPACE_CHARS . '+(?P<value>=+))\\n)S' ),
            array(
                'class' => 'ezcDocumentWikiBulletListItemToken',
                'match' => '(\\A\\n' . self::WHITESPACE_CHARS . '*(?P<value>[*-]+)' . self::WHITESPACE_CHARS . '+)S' ),
            array(
                'class' => 'ezcDocumentWikiEnumeratedListItemToken',
                'match' => '(\\A\\n' . self::WHITESPACE_CHARS . '*(?P<value>#+)' . self::WHITESPACE_CHARS . '+)S' ),
            array(
                'class' => 'ezcDocumentWikiPageBreakToken',
                'match' => '(\\A(?P<match>\n' . self::WHITESPACE_CHARS . '*(?P<value>-{4})' . self::WHITESPACE_CHARS . '*)\\n)S' ),
            array(
                'class' => 'ezcDocumentWikiLiteralBlockToken',
                'match' => '(\\A(?P<match>\\n\\{\\{\\{\\n(?P<value>.+)\\n\\}\\}\\})\\n)SUs' ),
            array(
                'class' => 'ezcDocumentWikiTableRowToken',
                'match' => '(\\A(?P<match>\\n)(?P<value>\\|))S' ),
            array(
                'class' => 'ezcDocumentWikiParagraphIndentationToken',
                'match' => '(\\A\\n(?P<value>(?:>|:)+)' . self::WHITESPACE_CHARS . '*)S' ),

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
                'match' => '(\\A(?P<value>~))S' ),

            // Inline markup
            array(
                'class' => 'ezcDocumentWikiBoldToken',
                'match' => '(\\A(?P<value>\\*\\*))S' ),
            array(
                'class' => 'ezcDocumentWikiItalicToken',
                'match' => '(\\A(?P<value>//))S' ),
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
            array(
                'class' => 'ezcDocumentWikiLinkStartToken',
                'match' => '(\\A(?P<value>\\[\\[))S' ),
            array(
                'class' => 'ezcDocumentWikiLinkEndToken',
                'match' => '(\\A(?P<value>\\]\\]))S' ),
            array(
                'class' => 'ezcDocumentWikiTableHeaderToken',
                'match' => '(\\A(?P<value>\\|=))S' ),
            array(
                'class' => 'ezcDocumentWikiSeparatorToken',
                'match' => '(\\A(?P<value>\\||' . self::WHITESPACE_CHARS . '*->' . self::WHITESPACE_CHARS . '*))S' ),
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
        // Match name of plugin
        if ( preg_match( '(^[a-z]+)i', $plugin->content, $match ) )
        {
            $plugin->type = $match[0];
        }

        // Match plugin parameters
        $parameters = array();
        if ( preg_match_all( '(\s+(?P<key>[a-zA-Z_-]+)=([\'"])(?P<value>.*?)(?!\\\\)\\2)s', $plugin->content, $match ) )
        {
            foreach ( $match['key'] as $nr => $key )
            {
                $parameters[$key] = $match['value'][$nr];
            }
        }
        $plugin->parameters = $parameters;
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
        foreach ( $tokens as $token )
        {
            switch ( true )
            {
                // Extract the title / indentation level from the tokens
                // length.
                case $token instanceof ezcDocumentWikiTitleToken:
                case $token instanceof ezcDocumentWikiParagraphIndentationToken:
                    $token->level = strlen( trim( $token->content ) );
                    break;

                case $token instanceof ezcDocumentWikiBulletListItemToken:
                case $token instanceof ezcDocumentWikiEnumeratedListItemToken:
                    $token->indentation = strlen( $token->content );
                    break;

                case $token instanceof ezcDocumentWikiPluginToken:
                    $this->parsePluginContents( $token );
                    break;
            }
        }

        return $tokens;
    }
}

?>
