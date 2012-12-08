<?php
/**
 * File containing the ezcTemplateParserException class
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
class ezcTemplateParserException extends ezcTemplateException
{
    /**
     * The source code object which caused the error.
     *
     * @var ezcTemplateSource
     */
    public $source;

    /**
     * Cursor of the parsed line.
     *
     * @var ezcTemplateCursor
     */
    public $startCursor;

    /**
     * Cursor where the error occured.
     *
     * @var ezcTemplateCursor
     */
    public $errorCursor;

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
     * @param ezcTemplateSource $source         The source code which caused the error, used for file path.
     * @param ezcTemplateCursor $startCursor  
     * @param ezcTemplateCursor $errorCursor  
     * @param string $errorMessage The error message.
     * @param string $errorDetails Extra details for error.
     */
    public function __construct( 
                                 $source,
                                 ezcTemplateCursor $startCursor,
                                 ezcTemplateCursor $errorCursor,
                                 $errorMessage,
                                 $errorDetails = "" )
    {
        $this->source = $source;
        $this->startCursor = $startCursor;
        $this->errorCursor = $errorCursor;

        $this->errorMessage = $errorMessage;
        $this->errorDetails = $errorDetails;

        parent::__construct( $this->getErrorMessage() );
    }

    /**
     * Generates the error message from member variables and returns it.
     *
     * @return string
     */
    public function getErrorMessage()
    {
        // Show failed code for element
        $code = $this->getAstNodeFailure( $this->startCursor, $this->errorCursor, $this->errorCursor );
        $details = $this->errorDetails;

        if ( strlen( $details ) > 0 )
        {
            $details = "\n" . $details;
        }

        $locationMessage = "{$this->source->stream}:{$this->errorCursor->line}:" . ($this->errorCursor->column + 1). ":";
        $message = $locationMessage . " " . $this->errorMessage . "\n\n" . $code . $details . "\n"; 

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
     * @param ezcTemplateCursor $errorCursor The point in the code where the error appears
     * @return string
     */
    private function getAstNodeFailure( $startCursor, $endCursor, $errorCursor )
    {
        $code = substr( $startCursor->text,
                        $startCursor->position - $startCursor->column,
                        $endCursor->position - $startCursor->position + $startCursor->column );

        // Include some code which appears after the failure points, max 30 characters
        $extraAstNode = substr( $startCursor->text,
                             $endCursor->position,
                             $errorCursor->position - $endCursor->position + 30 );


        $eolPos = strpos( $extraAstNode, "\n" );
        if ( $eolPos !== false )
        {
            $extraAstNode = substr( $extraAstNode, 0, $eolPos );
        }

        $code .= $extraAstNode;
        $code .= "\n";
        if ( $errorCursor->column > 0 )
        {
            $code .= str_repeat( " ", $errorCursor->column );
        }
        $code .= "^";
        return $code;
    }
}
?>
