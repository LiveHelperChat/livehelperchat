<?php
/**
 * File containing the ezcTemplateAstToPhpStringGenerator class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Generator of PHP code.
 *
 * Implements the ezcTemplateBasicAstNodeVisitor interface for visiting code elements
 * and generating code for them. As apposed to the parent class
 * ezcTemplateConfiguration, this class stores the generated code internally.
 * The generated code can be retrieved using the getString() method.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateAstToPhpStringGenerator extends ezcTemplateAstToPhpGenerator
{
    /**
     * Contains the generated PHP Code
     *
     * @var string
     */
    private $string;

    /**
     * Constructs a new ezcTemplateAstToPhpGenerator
     *
     * @param ezcTemplateConfiguration $configuration The template configuration.
     * @param int $indentation The default indentation to use when increasing it.
     */
    public function __construct( $configuration, $indentation = 4 )
    {
        $this->string = '';
        $this->indentation = $indentation;
        $this->hasWrittenFooter = false;
        $this->currentIndentation = 0;
        $this->indentationText = '';
        $this->indentationStack = array();
        $this->newline = true;

        $this->sourceCharset = $configuration->sourceCharset;
        $this->targetCharset = $configuration->targetCharset;
    }

    /**
     * Does nothing now, but the parent does file handling so we have to override it.
     */
    public function __destruct()
    {
    }

    /**
     * Adds text to the internal string.
     * The text string will be split up into lines and will have each line
     * indented according to current indentation rules.
     *
     * @param string $text The text string to write.
     * @param string $pre  Text to place in front of each line (after indentation).
     * @param string $post Text to place after each line (before newline character).
     * @return void
     */
    protected function write( $text, $pre = null, $post = null )
    {
        $lines = preg_split( "#(\r\n|\r|\n)#", $text, -1, PREG_SPLIT_DELIM_CAPTURE );
        $count = count( $lines );
        for ( $i = 0; $i < $count; $i += 2 )
        {
            $line = $lines[$i];
            if ( $i + 1 < $count )
            {
                $str = $pre . $line . $post . $lines[$i + 1];
                if ( $this->newline )
                {
                    $str = $this->indentationText . $str;
                }
                $this->string .= $str;
                $this->newline = true;
            }
            else // The last line.
            {
                if ( strlen( $line ) > 0 )
                {
                    $str = $pre . $line . $post;
                    if ( $this->newline )
                    {
                        $str = $this->indentationText . $str;
                    }
                    $this->string .= $str;
                    $this->newline = false;
                }
            }
        }
    }

    /**
     * Visits a code element containing a body of statements.
     * A body consists of a series of statements in sequence.
     *
     * @param ezcTemplateRootAstNode $body The code element containing the body.
     * @return void
     */
    public function visitRootAstNode( ezcTemplateRootAstNode $body )
    {
        $this->charsetInTemplate = $body->charset;

        foreach ( $body->statements as $statement )
        {
            $statement->accept( $this );
        }
    }

    /**
     * Returns the generated string containing PHP code
     *
     * @return string
     */
    public function getString()
    {
        return $this->string;
    }
}
?>
