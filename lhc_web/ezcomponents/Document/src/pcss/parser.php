<?php
/**
 * File containing the ezcDocumentPcssParser class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Parser for simplified CSS rules for PDF layout specifications
 *
 * The EBNF for the parsed grammar is the following. The EBNF does not specify
 * the allowed comments, which are common C-style comments:
 *
 * <code>
 *  File        ::= Directive+
 *  Directive   ::= Address | Definition '{' Formatting* '}'
 *  Formatting  ::= Name ':' Value ';'
 *  Name        ::= [A-Za-z-]+
 *  Value       ::= QuotedValue | RawValue
 *  QuotedValue ::= '"' [^"]+ '"'
 *  RawValue    ::= [^;]+
 *
 *  Definition  ::= '@' [A-Za-z_-]+
 *
 *  Address     ::= Element ( Rule )*
 *  Rule        ::= '>'? Element
 *  Element     ::= ElementName ( '.' ClassName | '#' ElementId )
 *
 *  ClassName   ::= [A-Za-z_-]+
 *  ElementName ::= XMLName¹ | '*'
 *  ElementId   ::= XMLName¹
 *
 *  ¹ XMLName references to http://www.w3.org/TR/REC-xml/#NT-Name
 * </code>
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPcssParser extends ezcDocumentParser
{
    /**
     * Currently parsed file, stored for additional error context
     *
     * @var string
     */
    protected $file;

    /**
     * Expressions for tokenizing the strings.
     *
     * @var array
     */
    protected $expressions = array();

    /**
     * Tokens irrelevant to the parser, which will bee thrown away immediately
     *
     * @var array
     */
    protected $ignoreTokens = array(
        self::T_WHITESPACE,
        self::T_COMMENT,
    );

    /**
     * Names for the known tokens, for nicer error messages
     *
     * @var array
     */
    protected $tokenNames = array(
        self::T_WHITESPACE    => 'T_WHITESPACE',
        self::T_COMMENT       => 'T_COMMENT',
        self::T_ADDRESS       => 'T_ADDRESS (CSS element addressing queries)',
        self::T_DESC_ADDRESS  => 'T_DESC_ADDRESS (CSS element addressing queries)',
        self::T_ADDRESS_ID    => 'T_ADDRESS_ID (CSS element addressing queries)',
        self::T_ADDRESS_CLASS => 'T_ADDRESS_CLASS (CSS element addressing queries)',
        self::T_DEFINITION    => 'T_DEFINITION (CSS definition addressing element)',
        self::T_START         => 'T_START ("{")',
        self::T_END           => 'T_END ("}")',
        self::T_FORMATTING    => 'T_FORMATTING (formatting specification)',
        self::T_VALUE         => 'T_VALUE (formatting value definition)',
        self::T_EOF           => 'T_EOF (end of file)',
    );

    /**
     * Regular expression for characters a XML name may start with, as defined
     * at:
     *
     * http://www.w3.org/TR/REC-xml/#NT-NameStartChar
     */
    const XML_NAME_STARTCHAR = '(?:[:A-Za-z_])';
        // @todo: Integrate: |[#xC0-#xD6]|[#xD8-#xF6]|[#xF8-#x2FF]|[#x370-#x37D]|[#x37F-#x1FFF]|[#x200C-#x200D]|[#x2070-#x218F]|[#x2C00-#x2FEF]|[#x3001-#xD7FF]|[#xF900-#xFDCF]|[#xFDF0-#xFFFD]|[#x10000-#xEFFFF])';

    /**
     * Regular expression for characters a XML name may contain, as defined at:
     *
     * http://www.w3.org/TR/REC-xml/#NT-NameChar
     *
     * We exclude the dot (.) from the name, since this one is used to specify
     * classes, just like in CSS. Should not, but may limit the actual usage.
     * Since now no docbook markup element contains a dot.
     */
    const XML_NAME_CHAR      = '(?:[-0-9])';
        // @todo: Integrate: |#xB7|[#x0300-#x036F]|[#x203F-#x2040])';

    /**
     * Whitespace token
     */
    const T_WHITESPACE    = 1;

    /**
     * Comment token
     */
    const T_COMMENT       = 2;

    /**
     * Common addressing element token
     */
    const T_ADDRESS       = 10;

    /**
     * Direct descendant addressing element token
     */
    const T_DESC_ADDRESS  = 11;

    /**
     * Addressing ID token
     */
    const T_ADDRESS_ID    = 12;

    /**
     * Addressing class token
     */
    const T_ADDRESS_CLASS = 13;

    /**
     * Definition "address" token
     */
    const T_DEFINITION    = 14;

    /**
     * Directive start token
     */
    const T_START         = 20;

    /**
     * Directive end token
     */
    const T_END           = 21;

    /**
     * Formatting rule token
     */
    const T_FORMATTING    = 30;

    /**
     * Formatting rule value token
     */
    const T_VALUE         = 31;

    /**
     * End of file token
     */
    const T_EOF           = 40;

    /**
     * Construct parser
     *
     * Creates the regualr expressions for tokenizing the PCSS file.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $xmlName = '(?:' . self::XML_NAME_STARTCHAR . '(?:' . self::XML_NAME_STARTCHAR . '|' . self::XML_NAME_CHAR . ')*)';

        $this->expressions = array(
            array(
                'type'  => self::T_WHITESPACE,
                'match' => '(\\A\\s+)S' ),
            array(
                'type'  => self::T_COMMENT,
                'match' => '(\\A/\\*.*\\*/)SUs' ),
            array(
                'type'  => self::T_COMMENT,
                'match' => '(\\A//.*$)Sm' ),
            array(
                'type'  => self::T_START,
                'match' => '(\\A\\{)S' ),
            array(
                'type'  => self::T_END,
                'match' => '(\\A\\})S' ),
            array(
                'type'  => self::T_FORMATTING,
                'match' => '(\\A(?P<name>[A-Za-z-]+)\\s*:)S',
                'to'    => 'formats' ),
            array(
                'state' => 'formats',
                'type'  => self::T_VALUE,
                'match' => '(\\A"(?P<value>[^"]+)"\\s*;)S',
                'to'    => 'default' ),
            array(
                'state' => 'formats',
                'type'  => self::T_VALUE,
                'match' => '(\\A(?P<value>[^;]+?)\\s*;)S',
                'to'    => 'default' ),
            array(
                'type'  => self::T_ADDRESS,
                'match' => '(\\A' . $xmlName . ')S' ),
            array(
                'type'  => self::T_DESC_ADDRESS,
                'match' => '(\\A>[\\t\\x20]+' . $xmlName . ')S' ),
            array(
                'type'  => self::T_ADDRESS_CLASS,
                'match' => '(\\A\\.[A-Za-z_-]+)S' ),
            array(
                'type'  => self::T_ADDRESS_ID,
                'match' => '(\\A#' . $xmlName . ')S' ),
            array(
                'type'  => self::T_DEFINITION,
                'match' => '(\\A@[A-Za-z_-]+)S' ),
        );
    }

    /**
     * Parse the given file
     *
     * Try to parse the given PCSS file and return the AST containing the file
     * contents.
     *
     * @param string $file
     * @return void
     */
    public function parseFile( $file )
    {
        $this->file = $file;
        $ast = $this->parseString( file_get_contents( $file ) );
        $this->file = null;
        return $ast;
    }

    /**
     * Parse the given file
     *
     * Try to parse the given PCSS string and return the AST containing the
     * string contents.
     *
     * @param string $string
     * @return void
     */
    public function parseString( $string )
    {
        // Normalize line endings
        $string = preg_replace( '(\r\n|\r|\n)', "\n", $string );

        return $this->parse(
            $this->tokenize( $string )
        );
    }

    /**
     * Tokenize the input string
     *
     * Returns an array of arrays representing the tokens.
     *
     * @param string $string
     * @return array
     */
    protected function tokenize( $string )
    {
        $line     = 1;
        $position = 1;
        $tokens   = array();
        $state    = 'default';

        while ( strlen( $string ) )
        {
            foreach ( $this->expressions as $rule )
            {
                if ( ( isset( $rule['state'] ) &&
                       ( $rule['state'] !== $state ) ) ||
                     !preg_match( $rule['match'], $string, $match ) )
                {
                    continue;
                }

                // Remove matched string from input
                $string = substr( $string, strlen( $match[0] ) );

                // Update tokenizer state
                if ( isset( $rule['to'] ) )
                {
                    $state = $rule['to'];
                }

                // Update position in file
                $line     += substr_count( $match[0], "\n" );
                if ( ( $pos = strrpos( $match[0], "\n" ) ) !== false )
                {
                    $position  = strrpos( $match[0], "\n" ) + 1;
                }
                else
                {
                    $position += strlen( $match[0] );
                }

                // Skip irrelevant rules
                if ( in_array( $rule['type'], $this->ignoreTokens ) )
                {
                    continue 2;
                }

                // Add all other rules including their match to the token
                // array
                $tokens[] = array(
                    'type'     => $rule['type'],
                    'line'     => $line,
                    'position' => $position,
                    'match'    => $match,
                );

                continue 2;
            }

            // No matching rule could be found
            return $this->triggerError( E_PARSE,
                "Could not parse string: '" . substr( $string, 0, 20 ) . "' in state: $state.",
                $this->file, $line, $position
            );
        }

        $tokens[] = array(
            'type'     => self::T_EOF,
            'line'     => $line,
            'position' => $position,
            'match'    => null,
        );

        return $tokens;
    }

    /**
     * Read expected from token array
     *
     * Try to read the given token from the token array. If another token is
     * found, a parse error is issued. If the token is found, the token is
     * removed fromt he token array and returned.
     *
     * @param array $types
     * @param array $tokens
     * @return array
     */
    private function read( array $types, array &$tokens )
    {
        $token = array_shift( $tokens );

        if ( !in_array( $token['type'], $types, true ) )
        {
            $names = array();
            foreach ( $types as $type )
            {
                $names[] = $this->tokenNames[$type];
            }

            $this->triggerError( E_PARSE,
                "Expected one of: " . implode( ', ', $names ) . ", found " . $this->tokenNames[$token['type']] . '.',
                $this->file, $token['line'], $token['position']
            );
        }

        return $token;
    }

    /**
     * Parse given token array
     *
     * Parse the given token array, and create an array of directive objects
     * from it, if the token array specifies a valid PCSS file.
     *
     * @param array $tokens
     * @return array
     */
    protected function parse( array $tokens )
    {
        $directives = array();

        $addressTokens = array(
            self::T_ADDRESS,
            self::T_DESC_ADDRESS,
            self::T_ADDRESS_ID,
            self::T_ADDRESS_CLASS,
        );

        while ( count( $tokens ) > 1 )
        {
            // Address should always be followed by a start token
            $formats = array();
            $address = array();
            
            if ( $tokens[0]['type'] === self::T_DEFINITION )
            {
                $addressType  = 'ezcDocumentPcssDeclarationDirective';
                $addressToken = $this->read( array( self::T_DEFINITION ), $tokens );
                $address      = $addressToken['match'][0];
            }
            else
            {
                do {
                    $addressType  = 'ezcDocumentPcssLayoutDirective';
                    $addressToken = $this->read( $addressTokens, $tokens );
                    $address[]    = $addressToken['match'][0];
                }
                while ( $tokens[0]['type'] !== self::T_START );
            }

            $this->read( array( self::T_START ), $tokens );

            while ( $tokens[0]['type'] !== self::T_END )
            {
                $format = $this->read( array( self::T_FORMATTING ), $tokens );
                $value  = $this->read( array( self::T_VALUE ), $tokens );
                $formats[$format['match']['name']] = $value['match']['value'];
            }

            $this->read( array( self::T_END ), $tokens );

            // Create successfully read directive
            $directives[] = new $addressType(
                $address,
                $formats,
                $this->file, $addressToken['line'], $addressToken['position']
            );
        }

        return $directives;
    }
}
?>
