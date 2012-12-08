<?php
/**
 * File containing the ezcDocumentWikiTokenizer
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Tokenizer for wiki documents
 *
 * The tokenizer used for all wiki documents should prepare a token array,
 * which can be used by the wiki parser, without any wiki language specific
 * handling in the parser itself required. For this the tokenizing is performed
 * in two steps:
 *
 * 1) Extract tokens from text
 * 2) Filter tokens
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
 * Token filtering
 * ---------------
 *
 * After all tokens are extracted from the text, they may miss some values,
 * which may be required by the parser, like the level of title tokens. Those
 * should be extracted and assigned during the filtering stage. For this the
 * filterTokens() method should be implemented, which may iterate over the
 * token stream and assign the required values.
 *
 * If the wiki markup language supports plugins you may also want to "parse"
 * the plugin contents to extract type, parameters and its text here.
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocumentWikiTokenizer
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
     * Construct tokenizer
     *
     * Create token array with regular repression matching the respective
     * token.
     *
     * @return void
     */
    abstract public function __construct();

    /**
     * Tokenize the given file
     *
     * The method tries to tokenize the passed files and returns an array of
     * ezcDocumentWikiToken struct on succes, or throws a
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
     * @param ezcDocumentWikiToken $token
     * @return void
     */
    protected function convertTabs( ezcDocumentWikiToken $token )
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
    abstract protected function filterTokens( array $tokens );

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
        $line     = 0;
        $position = 1;
        $tokens   = array();
        $string   = "\n" . $string;

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
                         ( $match['class'] !== 'ezcDocumentWikiNewLineToken' ) )
                    {
                        $tokens[] = new ezcDocumentWikiNewLineToken( $matches[0][0], $line, $position );
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
                    if ( $newToken instanceof ezcDocumentWikiNewLineToken )
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
                    if ( $newToken instanceof ezcDocumentWikiWhitespaceToken )
                    {
                        $this->convertTabs( $newToken );
                    }

                    // If we found an explicit EOF token, just exit the parsing process.
                    if ( $newToken instanceof ezcDocumentWikiEndOfFileToken )
                    {
                        break 2;
                    }

                    // Add token to extracted token list
                    $tokens[] = $newToken;

                    // Update position, not before converting tabs to spaces.
                    $position += ( $newToken instanceof ezcDocumentWikiNewLineToken ) ? 1 : strlen( $newToken->content );

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
            throw new ezcDocumentWikiTokenizerException(
                $line,
                $position,
                $string
            );
        }

        // Finally append ainother newline token and a end of file token, to
        // make parsing the end easier.
        $tokens[] = new ezcDocumentWikiNewLineToken( "\n", $line, $position );
        $tokens[] = new ezcDocumentWikiNewLineToken( "\n", $line, $position );
        $tokens[] = new ezcDocumentWikiEndOfFileToken( null, $line, $position );

        return $this->filterTokens( $tokens );
    }
}

?>
