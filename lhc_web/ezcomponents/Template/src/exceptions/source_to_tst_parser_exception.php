<?php
/**
 * File containing the ezcTemplateSourceToTstParserException class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception for failed element parsers.
 * The exception will display the exact location(s) where the error occured
 * with some extra description of what went wrong.
 *
 * @package Template
 * @version 1.4.2
 */
class ezcTemplateSourceToTstParserException extends ezcTemplateException
{
    /**
     * Array of elements which should be used to extract failed code.
     * Each element needs to have the property $startCursor and $endCursor.
     *
     * @var array
     */
    public $elements;

    /**
     * The source code object which caused the error.
     *
     * @var ezcTemplateSource
     */
    public $source;

    /**
     * The one-liner error message.
     *
     * @var string
     */
    public $errorMessage;

    /**
     * A more detailed error message which can for instance give hints to the
     * end-user why it failed.
     *
     * @var string
     */
    public $errorDetails;

    /**
     * Initialises the exception with the failing elements, parser, source code
     * and error messages.
     *
     * @param ezcTemplateSourceToTstParser $parser  The parser which was used when error occured, can be null.
     * @param ezcTemplateCursor $cursor             The cursor pointing to the error.
     * @param string $errorMessage                  The error message.
     * @param string $errorDetails                  Extra details for error.
     */
    public function __construct( /*ezcTemplateSourceToTstParser*/ $parser,
                                 ezcTemplateCursor $cursor,
                                 $errorMessage,
                                 $errorDetails = "" )
    {
        
            // TODO fix this. 
        $this->elements = array( $parser );
        $this->parser = $parser;
        $this->source = $parser->parser->source;
        $this->elements[0]->startCursor = $cursor;

        $this->errorMessage = $errorMessage;
        $this->errorDetails = $errorDetails;

        parent::__construct( $this->getErrorMessage() );
    }

    /**
     * Generates the error message from member variables and returns it.
     * @return string
     */
    public function getErrorMessage()
    {
        // Display tree structure of parsers, for debugging
        if ( $this->parser !== null )
        {
            $parser = $this->parser;
            $parsers = array();
            $currentParser = $parser->programParser;
            $parsers = get_class( $currentParser );
            $level = 0;
            while ( $currentParser->subParser !== null )
            {
                ++$level;
                $currentParser = $currentParser->subParser;
                $parsers .= "\n" . str_repeat( " ", $level ) . "-> " . get_class( $currentParser );
            }
            $parsers .= "\n";
        }

        // Extract code parts which failed
        $code = '';
        $i = 0;
        $lastStartCursor = $this->elements[0]->startCursor;
        $lastEndCursor = $this->elements[0]->endCursor;
        foreach ( $this->elements as $element )
        {
            if ( $i > 0 )
            {
                $code .= "\n";
                // Diff-style marker for line number difference
                $code .= "@@ -{$lastStartCursor->line} +{$element->endCursor->line}\n";
            }
            ++$i;

            // Show failed code for element
            $code .= $this->getAstNodeFailure( $element->startCursor, $element->endCursor, $element->startCursor );

            // Store cursor for next iteration
            $lastStartCursor = $element->startCursor;
            $lastEndCursor = $element->endCursor;
        }

        $details = $this->errorDetails;
        if ( strlen( $details ) > 0 )
        {
            $details = "\n" . $details;
        }
        $locationMessage = "{$this->source->stream}:{$lastEndCursor->line}:{$lastEndCursor->column}:";
        $message = $locationMessage . " " . $this->errorMessage . "\n\n" . $code . $details . "\n\nfailed in:\n" . $parsers;

        return $message;
    }

    /**
     * Extracts the code which failed as denoted by $startCursor and $endCursor
     * and display the exact column were it happened.
     * The cursor $markCursor is used to mark where the error occured, it will
     * displayed using a ^ character.
     *
     * @param ezcTemplateCursor $startCursor The start point of the code to extract
     * @param ezcTemplateCursor $endCursor The ending point of the code to extract
     * @param ezcTemplateCursor $markCursor The point in the code which should be highlighted.
     * @return string
     */
    private function getAstNodeFailure( $startCursor, $endCursor, $markCursor )
    {
        $code = substr( $startCursor->text,
                        $startCursor->position - $startCursor->column,
                        $endCursor->position - $startCursor->position + $startCursor->column );

        // Include some code which appears after the failure points, max 10 characters
        $extraAstNode = substr( $startCursor->text,
                             $endCursor->position,
                             $markCursor->position - $endCursor->position + 10 );


        $eolPos = strpos( $extraAstNode, "\n" );
        if ( $eolPos !== false )
        {
            $extraAstNode = substr( $extraAstNode, 0, $eolPos );
        }

        $code .= $extraAstNode;
        $code .= "\n";
        if ( $markCursor->column > 0 )
        {
            $code .= str_repeat( " ", $markCursor->column );
        }
        $code .= "^";
        return $code;
    }
}
?>
