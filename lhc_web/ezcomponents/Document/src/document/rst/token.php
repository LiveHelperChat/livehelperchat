<?php
/**
 * File containing the ezcDocumentRstToken struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Struct for RST document document tokens
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentRstToken extends ezcBaseStruct
{
    // Token type constants
    const WHITESPACE    = 1;
    const NEWLINE       = 2;

    const BACKSLASH     = 3;

    const SPECIAL_CHARS = 4;

    const TEXT_LINE     = 5;

    const EOF           = 6;

    /**
     * Token type
     *
     * @var int
     */
    public $type;

    /**
     * Token content
     *
     * @var mixed
     */
    public $content;

    /**
     * Line of the token in the source file
     *
     * @var int
     */
    public $line;

    /**
     * Position of the token in its line.
     *
     * @var int
     */
    public $position;

    /**
     * For text nodes we need an additional identifier, if this text node has
     * been escaped, and though is intentionally freed from any potential
     * special meaning.
     *
     * @var bool
     */
    public $escaped = false;

    /**
     * Construct RST token
     *
     * @ignore
     * @param int $type
     * @param mixed $content
     * @param int $line
     * @param int $position
     * @param bool $escaped
     * @return void
     */
    public function __construct( $type, $content, $line, $position = 0, $escaped = false )
    {
        $this->type     = $type;
        $this->content  = $content;
        $this->line     = $line;
        $this->position = $position;
        $this->escaped  = $escaped;
    }

    /**
     * Get token name from type
     *
     * Return a user readable name from the numeric token type.
     *
     * @param int $type
     * @return string
     */
    public static function getTokenName( $type )
    {
        $names = array(
            self::WHITESPACE    => 'Whitespace',
            self::NEWLINE       => 'Newline',
            self::BACKSLASH     => 'Backslash',
            self::SPECIAL_CHARS => 'Special character group',
            self::TEXT_LINE     => 'Text',
            self::EOF           => 'End Of File',
        );

        if ( !isset( $names[$type] ) )
        {
            return 'Unknown';
        }

        return $names[$type];
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
        return new ezcDocumentRstToken(
            $properties['type'],
            $properties['content'],
            $properties['line'],
            $properties['position'],
            isset( $properties['escaped'] ) ? $properties['escaped'] : false
        );
    }
}

?>
