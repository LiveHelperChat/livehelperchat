<?php
/**
 * File containing the ezcDocumentRstTokenizer
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Tokenizer for RST documents
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentRstTokenizer
{
    /**
     * Common whitespace characters. The vertical tab is excluded, because it
     * causes strange problems with PCRE.
     */
    const WHITESPACE_CHARS  = ' \\t';

    /**
     * Allowed character sets for headlines.
     *
     * @see http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#sections
     */
    const SPECIAL_CHARS     = '!"#$%&\'()*+,./:;<=>?@[\\]^_`{|}~-';

    /**
     * Characters ending a pure text section.
     *
     * @see http://docutils.sourceforge.net/docs/ref/rst/restructuredtext.html#enumerated-lists
     */
    const TEXT_END_CHARS    = '`*_\\\\[\\]|()"\':.\\r\\n\\t ';

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
    public function __construct()
    {
        $this->tokens = array(
            // Whitespaces
            ezcDocumentRstToken::NEWLINE =>
                '(\\A[' . self::WHITESPACE_CHARS . ']*(?P<value>\\r\\n|\\r|\\n))S',
            ezcDocumentRstToken::WHITESPACE =>
                '(\\A(?P<value>[' . self::WHITESPACE_CHARS . ']+))S',

            // Sequences of special characters
            ezcDocumentRstToken::SPECIAL_CHARS =>
                '(\\A(?P<value>([' . self::SPECIAL_CHARS . ']|\\xe2\\x80\\xa2|\\xe2\\x80\\xa3|\\xe2\\x81\\x83)\\2*))S',
            ezcDocumentRstToken::BACKSLASH =>
                '(\\A(?P<value>\\\\))S',
            ezcDocumentRstToken::EOF =>
                '(\\A(?P<value>))S',

            // This should be last match
            ezcDocumentRstToken::TEXT_LINE =>
                '(\\A(?P<value>(?: [^' . self::TEXT_END_CHARS . ']|[^' . self::TEXT_END_CHARS . '])+))S',
        );
    }

    /**
     * Tokenize the given file
     *
     * The method tries to tokenize the passed files and returns an array of
     * ezcDocumentRstToken struct on succes, or throws a
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
     * @param ezcDocumentRstToken $token
     * @return void
     */
    protected function convertTabs( ezcDocumentRstToken $token )
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
     * ezcDocumentRstToken struct on succes, or throws a
     * ezcDocumentTokenizerException, if something could not be matched by any
     * token.
     *
     * @param string $string
     * @return array
     */
    public function tokenizeString( $string )
    {
        $line = 1;
        $position = 1;
        $tokens = array();

        while ( strlen( $string ) > 0 )
        {
            foreach ( $this->tokens as $token => $expression )
            {
                if ( preg_match( $expression, $string, $matches ) )
                {
                    // A token matched, so add the matched token to the token
                    // list and update all variables.
                    $newToken = new ezcDocumentRstToken(
                        $token,
                        ( isset( $matches['value'] ) ? $matches['value'] : null ),
                        $line,
                        $position
                    );

                    // Removed matched stuff from input string
                    $string = substr( $string, $length = strlen( $matches[0] ) );

                    // On a newline token reset the line position and increase the line value
                    if ( $token === ezcDocumentRstToken::NEWLINE )
                    {
                        ++$line;
                        $position = 0;
                    }

                    // Convert tabs to spaces for whitespace tokens
                    if ( $token === ezcDocumentRstToken::WHITESPACE )
                    {
                        $this->convertTabs( $newToken );
                    }

                    // If we found an explicit EOF token, just exit the parsing process.
                    if ( $token === ezcDocumentRstToken::EOF )
                    {
                        break 2;
                    }

                    // Add token to extracted token list
                    $tokens[] = $newToken;

                    // Update position, not before converting tabs to spaces.
                    $position += ( $token === ezcDocumentRstToken::NEWLINE ) ? 1 : strlen( $newToken->content );

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
            throw new ezcDocumentRstTokenizerException(
                $line,
                $position,
                $string
            );
        }

        // Finally append ainother newline token and a end of file token, to
        // make parsing the end easier.
        $tokens[] = new ezcDocumentRstToken(
            ezcDocumentRstToken::NEWLINE,
            "\n", $line, $position
        );
        $tokens[] = new ezcDocumentRstToken(
            ezcDocumentRstToken::NEWLINE,
            "\n", $line, $position
        );
        $tokens[] = new ezcDocumentRstToken(
            ezcDocumentRstToken::EOF,
            null, $line, $position
        );

        return $tokens;
    }
}

?>
