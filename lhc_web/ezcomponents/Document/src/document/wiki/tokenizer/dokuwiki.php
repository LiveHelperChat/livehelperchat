<?php
/**
 * File containing the ezcDocumentWikiDokuwikiTokenizer
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Tokenizer for Dokuwiki wiki documents.
 *
 * The Dokuwiki wiki is a very popular wiki, which for example is currently
 * used at http://wiki.php.net. The Dokuwiki syntax definition can be found at:
 *
 * http://www.dokuwiki.org/syntax
 *
 * For the basic workings of the tokenizer see the class level documentation in
 * the ezcDocumentWikiTokenizer class.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentWikiDokuwikiTokenizer extends ezcDocumentWikiTokenizer
{
    /**
     * Common whitespace characters. The vertical tab is excluded, because it
     * causes strange problems with PCRE.
     */
    const WHITESPACE_CHARS  = '[\\x20\\t]';

    /**
     * Characters ending a pure text section.
     */
    const TEXT_END_CHARS    = '/*^,\'_<>\\\\\\[\\]{}()|=\\r\\n\\t\\x20';

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
            // Match tokens which require to be at the start of a line before
            // matching the actual newlines, because they are the indicator for
            // line starts.
            array(
                'class' => 'ezcDocumentWikiTitleToken',
                'match' => '(\\A(?P<match>(?:\\n|' . self::WHITESPACE_CHARS . '+)(?P<value>={2,6}))(?:\\n|' . self::WHITESPACE_CHARS . '+))S' ),
            array(
                'class' => 'ezcDocumentWikiBulletListItemToken',
                'match' => '(\\A\\n(?P<value>\\x20*\\*)' . self::WHITESPACE_CHARS . '+)S' ),
            array(
                'class' => 'ezcDocumentWikiEnumeratedListItemToken',
                'match' => '(\\A\\n(?P<value>\\x20*-)' . self::WHITESPACE_CHARS . '+)S' ),
            array(
                'class' => 'ezcDocumentWikiLiteralBlockToken',
                'match' => '(\\A(?P<match>\\n<(code|file)>\\n(?P<value>.+)\\n</\\2>)\\n)SUsi' ),
            array(
                'class' => 'ezcDocumentWikiLiteralBlockToken',
                'match' => '(\\A(?P<match>\\n(?P<value>(' . self::WHITESPACE_CHARS . '+).*\n(?:\\3.*\n)*)))S' ),
            array(
                'class' => 'ezcDocumentWikiTextLineToken',
                'match' => '(\\A(?P<match>\\n<nowiki>\\n(?P<value>.+)\\n</nowiki>)\\n)SUsi' ),
            array(
                'class' => 'ezcDocumentWikiTableRowToken',
                'match' => '(\\A(?P<match>\\n)(?P<value>[|^]))S' ),
            array(
                'class' => 'ezcDocumentWikiParagraphIndentationToken',
                'match' => '(\\A\\n(?P<value>>+)' . self::WHITESPACE_CHARS . '*)S' ),

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
            /*
            array(
                'class' => 'ezcDocumentWikiEscapeCharacterToken',
                'match' => '(\\A(?P<value>~))S' ),
            // */

            // Inline markup
            array(
                'class' => 'ezcDocumentWikiBoldToken',
                'match' => '(\\A(?P<value>\\*\\*))S' ),
            array(
                'class' => 'ezcDocumentWikiItalicToken',
                'match' => '(\\A(?P<value>//))S' ),
            array(
                'class' => 'ezcDocumentWikiMonospaceToken',
                'match' => '(\\A(?P<value>\'\'))S' ),
            array(
                'class' => 'ezcDocumentWikiSuperscriptToken',
                'match' => '(\\A(?P<value></?sup>))Si' ),
            array(
                'class' => 'ezcDocumentWikiSubscriptToken',
                'match' => '(\\A(?P<value></?sub>))Si' ),
            array(
                'class' => 'ezcDocumentWikiUnderlineToken',
                'match' => '(\\A(?P<value>__))S' ),
            array(
                'class' => 'ezcDocumentWikiDeletedToken',
                'match' => '(\\A(?P<value></?del>))Si' ),
            array(
                'class' => 'ezcDocumentWikiInlineLiteralToken',
                'match' => '(\\A<nowiki>(?P<value>.*)</nowiki>)SUi' ),
            array(
                'class' => 'ezcDocumentWikiTextLineToken',
                'match' => '(\\A%%(?P<value>.*)%%)SUi' ),
            array(
                'class' => 'ezcDocumentWikiLineBreakToken',
                'match' => '(\\A(?P<match>(?P<value>\\\\\\\\))(?:' . self::WHITESPACE_CHARS . '|\\n))S' ),
            array(
                'class' => 'ezcDocumentWikiLinkStartToken',
                'match' => '(\\A(?P<value>\\[\\[))S' ),
            array(
                'class' => 'ezcDocumentWikiLinkEndToken',
                'match' => '(\\A(?P<value>\\]\\]))S' ),
            array(
                'class' => 'ezcDocumentWikiSeparatorToken',
                'match' => '(\\A(?P<value>\\||' . self::WHITESPACE_CHARS . '*->' . self::WHITESPACE_CHARS . '*))S' ),
            array(
                'class' => 'ezcDocumentWikiExternalLinkToken',
                'match' => '(\\A
                        (?P<match>
                            (?P<value>
                                # Match common URLs
                                [a-z]+://\S+? |
                                # Match mail addresses enclosed by <>
                                <[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?>
                            )
                         # Greedy match on text end chars, which should NOT be included in URLs
                         )[,.?!:;"\']?(?:' . self::WHITESPACE_CHARS . '|\\n|\\||]]|\\}\\}|$)
                    )Sx' ),
            array(
                'class' => 'ezcDocumentWikiInterWikiLinkToken',
                'match' => '(\\A(?P<value>([A-Za-z]+)>[^\\]|]+))S' ),
            array(
                'class' => 'ezcDocumentWikiImageStartToken',
                'match' => '(\\A(?P<value>\\{\\{))S' ),
            array(
                'class' => 'ezcDocumentWikiImageEndToken',
                'match' => '(\\A(?P<value>\\}\\}))S' ),
            array(
                'class' => 'ezcDocumentWikiFootnoteStartToken',
                'match' => '(\\A(?P<value>\\(\\())S' ),
            array(
                'class' => 'ezcDocumentWikiFootnoteEndToken',
                'match' => '(\\A(?P<value>\\)\\)))S' ),
            array(
                'class' => 'ezcDocumentWikiTableHeaderToken',
                'match' => '(\\A(?P<value>\\^))S' ),
            array(
                'class' => 'ezcDocumentWikiPluginToken',
                'match' => '(\\A(?P<value><([a-zA-Z]+).*?</\\2>))Ss' ),

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
        if ( preg_match( '(^\\s*<(?P<type>[a-zA-Z]+)(?:\\s+(?P<params>[^>]+))?>(?P<content>.*?)\\s*</\\1>\\s*)si', $plugin->content, $match ) )
        {
            $plugin->type       = strtolower( $match['type'] );
            $plugin->parameters = isset( $match['params'] ) && $match['params'] ? array( $match['params'] ) : array();
            $plugin->text       = $match['content'];
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
        foreach ( $tokens as $nr => $token )
        {
            switch ( true )
            {
                // Extract the title / indentation level from the tokens
                // length.
                case $token instanceof ezcDocumentWikiTitleToken:
                    $token->level = 7 - strlen( trim( $token->content ) );
                    break;

                case $token instanceof ezcDocumentWikiParagraphIndentationToken:
                    $token->level = strlen( trim( $token->content ) );
                    break;

                case $token instanceof ezcDocumentWikiImageStartToken:
                    // Check if an alignement has been specified by whitespace
                    // tokens.
                    $lastImageStartToken = $token;
                    if ( $tokens[$next = $nr + 1] instanceof ezcDocumentWikiWhitespaceToken )
                    {
                        $token->alignement = 'right';
                        unset( $tokens[$nr + 1] );
                        ++$next;
                    }

                    if ( preg_match( '(\\?(?P<width>\d+)(?:x(?P<height>\d+))?$)', $tokens[$next]->content, $match ) )
                    {
                        $tokens[$next]->content = substr( $tokens[$next]->content, 0, -strlen( $match[0] ) );
                        $token->width   = isset( $match['width'] ) ? (int) $match['width'] : null;
                        $token->height  = isset( $match['height'] ) ? (int) $match['height'] : null;
                    }
                    break;

                case $token instanceof ezcDocumentWikiImageEndToken:
                case $token instanceof ezcDocumentWikiSeparatorToken:
                    // Check if an alignement has been specified by whitespace
                    // tokens.
                    if ( ( $tokens[$nr - 1] instanceof ezcDocumentWikiWhitespaceToken ) &&
                         ( $lastImageStartToken !== null ) )
                    {
                        $lastImageStartToken->alignement = $lastImageStartToken->alignement === 'right' ? 'center' : 'left';
                        unset( $tokens[$nr - 1] );
                    }
                    $lastImageStartToken = null;
                    break;

                case $token instanceof ezcDocumentWikiBulletListItemToken:
                case $token instanceof ezcDocumentWikiEnumeratedListItemToken:
                    $token->indentation = substr_count( $token->content, ' ' );
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
