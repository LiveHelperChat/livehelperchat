<?php
/**
 * File containing the ezcDocumentBBCodeTokenizer
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Tokenizer for bbcode documents
 *
 * The tokenizer used for all bbcode documents should prepare a token array,
 * which can be used by the bbcode parser, without any bbcode language specific
 * handling in the parser itself required.
 *
 * Token extraction
 * ----------------
 *
 * For the token extraction the reqular expressions in the $tokens property are
 * used. The $tokens array has to be build like, and can be created in the
 * constrctor:
 *
 * <code>
 *  array(
 *      array(
 *          'class' => Class name of token,
 *          'match' => Regular expression to match,
 *      ),
 *      ...
 *  )
 * </code>
 *
 * The array is evaluated in the given order, until one of the regular
 * expressions match. The regular expression should have at least one named
 * match (?P<value> ... ), with the name "value", which will be assigned to the
 * token, created form the given class name, as its content. The matched
 * contents will be removed from the beginning of the string.

 * Optionally a second named match, called "match", may be used inside the
 * regular expression. If so, only the contents inside this match will be
 * removed from the beginning of the string. This enables you to perform a
 * trivial lookahead inside the tokenizer.
 *
 * If no expression matches, an exception will be thrown.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentBBCodeTokenizer
{
    /**
     * List with tokens and a regular expression matching the given token.
     *
     * The tokens are matched in the given order.
     *
     * @var array
     */
    protected $tokens = array();

    /**
     * Common whitespace characters. The vertical tab is excluded, because it
     * causes strange problems with PCRE.
     */
    const WHITESPACE_CHARS  = '[\\x20\\t]';

    /**
     * Characters ending a pure text section.
     */
    const TEXT_END_CHARS    = '\\[\\]\\r\\n';

    /**
     * Special characters, which do have some special meaaning and though may
     * not have been matched otherwise.
     */
    const SPECIAL_CHARS     = '\\[\\]';

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
                'class' => 'ezcDocumentBBCodeLiteralBlockToken',
                'match' => '(\\A(?P<match>\\[code(?:=[^\\]]+)?\\](?P<value>.+)\\[/code\\]))SUs' ),
            array(
                'class' => 'ezcDocumentBBCodeListItemToken',
                'match' => '(\\A(?P<match>\\[\\*\\]))SUs' ),
            array(
                'class' => 'ezcDocumentBBCodeTagOpenToken',
                'match' => '(\\A(?P<match>\\[(?P<value>[A-Za-z]+(?:=[^\\]]+)?)\\]))SUs' ),
            array(
                'class' => 'ezcDocumentBBCodeTagCloseToken',
                'match' => '(\\A(?P<match>\\[/(?P<value>[A-Za-z]+)\\]))SUs' ),

            // Whitespaces
            array(
                'class' => 'ezcDocumentBBCodeNewLineToken',
                'match' => '(\\A' . self::WHITESPACE_CHARS . '*(?P<value>\\r\\n|\\r|\\n))S' ),
            array(
                'class' => 'ezcDocumentBBCodeWhitespaceToken',
                'match' => '(\\A(?P<value>' . self::WHITESPACE_CHARS . '+))S' ),
            array(
                'class' => 'ezcDocumentBBCodeEndOfFileToken',
                'match' => '(\\A(?P<value>\\x0c))S' ),

            // Escape character
            array(
                'class' => 'ezcDocumentBBCodeEscapeCharacterToken',
                'match' => '(\\A(?P<value>~))S' ),

            // Match text except
            array(
                'class' => 'ezcDocumentBBCodeTextLineToken',
                'match' => '(\\A(?P<value>[^' . self::TEXT_END_CHARS . ']+))S' ),

            // Match all special characters, which are not valid textual chars,
            // but do not have been matched by any other expression.
            array(
                'class' => 'ezcDocumentBBCodeSpecialCharsToken',
                'match' => '(\\A(?P<value>([' . self::SPECIAL_CHARS . '])\\2*))S' ),
        );
    }

    /**
     * Tokenize the given file
     *
     * The method tries to tokenize the passed files and returns an array of
     * ezcDocumentBBCodeToken struct on succes, or throws a
     * ezcDocumentTokenizerException, if something could not be matched by any
     * token.
     *
     * @param string $file
     * @return array
     */
    public function tokenizeFile( $file )
    {
        if ( !file_exists( $file ) || !is_readable( $file ) )
        {
            throw new ezcBaseFileNotFoundException( $file );
        }

        return $this->tokenizeString( file_get_contents( $file ) );
    }

    /**
     * Convert tabs to spaces
     *
     * Convert all tabs to spaces, as defined in:
     * http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#whitespace
     *
     * @param ezcDocumentBBCodeToken $token
     * @return void
     */
    protected function convertTabs( ezcDocumentBBCodeToken $token )
    {
        while ( ( $position = strpos( $token->content, "\t" ) ) !== false )
        {
            $token->content =
                substr( $token->content, 0, $position ) .
                str_repeat( ' ', 9 - ( ( $position + $token->position ) % 8 ) ) .
                substr( $token->content, $position + 1 );
        }
    }

    /**
     * Tokenize the given string
     *
     * The method tries to tokenize the passed strings and returns an array of
     * ezcDocumentBBCodeToken struct on succes, or throws a
     * ezcDocumentTokenizerException, if something could not be matched by any
     * token.
     *
     * @param string $string
     * @return array
     */
    public function tokenizeString( $string )
    {
        $line     = 1;
        $position = 1;
        $tokens   = array();

        // Normalize newlines
        $string = preg_replace( '([\x20\\t]*(?:\\r\\n|\\r|\\n))', "\n", $string );

        while ( strlen( $string ) > 0 )
        {
            foreach ( $this->tokens as $match )
            {
                if ( preg_match( $match['match'], $string, $matches ) )
                {
                    // If the first part of the match is a
                    // newline, add a respective token to the
                    // stack.
                    if ( ( $matches[0][0] === "\n" ) &&
                         ( $match['class'] !== 'ezcDocumentBBCodeNewLineToken' ) )
                    {
                        $tokens[] = new ezcDocumentBBCodeNewLineToken( $matches[0][0], $line, $position );
                        ++$line;
                        $position = 0;
                    }

                    // A token matched, so add the matched token to the token
                    // list and update all variables.
                    $class = $match['class'];
                    $newToken = new $class(
                        ( isset( $matches['value'] ) ? $matches['value'] : null ),
                        $line,
                        $position
                    );

                    $match = isset( $matches['match'] ) ? $matches['match'] : $matches[0];

                    // Removed matched stuff from input string
                    $string = substr( $string, $length = strlen( $match ) );

                    // On a newline token reset the line position and increase the line value
                    if ( $newToken instanceof ezcDocumentBBCodeNewLineToken )
                    {
                        ++$line;
                        $position = 0;
                    }
                    else
                    {
                        // Otherwise still update the line
                        // value, when there is at minimum
                        // one newline in the match. This may
                        // lead to a false position value.
                        if ( ( $newLines = substr_count( $match, "\n" ) ) > 0 )
                        {
                            $line += $newLines;
                            $position = 0;
                        }
                    }

                    // Convert tabs to spaces for whitespace tokens
                    if ( $newToken instanceof ezcDocumentBBCodeWhitespaceToken )
                    {
                        $this->convertTabs( $newToken );
                    }

                    // If we found an explicit EOF token, just exit the parsing process.
                    if ( $newToken instanceof ezcDocumentBBCodeEndOfFileToken )
                    {
                        break 2;
                    }

                    // Add token to extracted token list
                    $tokens[] = $newToken;

                    // Update position, not before converting tabs to spaces.
                    $position += ( $newToken instanceof ezcDocumentBBCodeNewLineToken ) ? 1 : strlen( $newToken->content );

                    // Restart the while loop, because we matched a token and
                    // can retry with shortened string.
                    continue 2;
                }
            }

            // None of the token definitions matched the input string. We throw
            // an exception with the position of the content in the input
            // string and the contents we could not match.
            //
            // This should never been thrown, but it is hard to prove that
            // there is nothing which is not matched by the regualr expressions
            // above.
            throw new ezcDocumentBBCodeTokenizerException(
                $line,
                $position,
                $string
            );
        }

        // Finally append ainother newline token and a end of file token, to
        // make parsing the end easier.
        $tokens[] = new ezcDocumentBBCodeNewLineToken( "\n", $line, $position );
        $tokens[] = new ezcDocumentBBCodeNewLineToken( "\n", $line, $position );
        $tokens[] = new ezcDocumentBBCodeEndOfFileToken( null, $line, $position );
        return $tokens;
    }
}

?>
