<?php
/**
 * File containing the abstract ezcDocumentDocbookToRstBaseHandler base class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Basic converter which stores a list of handlers for each node in the docbook
 * element tree. Those handlers will be executed for the elements, when found.
 * The handler can then handle the repective subtree.
 *
 * Additional handlers may be added by the user to the converter class.
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocumentDocbookToRstBaseHandler extends ezcDocumentElementVisitorHandler
{
    /**
     * Render a directive
     *
     * Render a directive with the given paramters.
     *
     * @param string $name
     * @param string $parameter
     * @param array $options
     * @param string $content
     * @return string
     */
    protected function renderDirective( $name, $parameter, array $options, $content = null )
    {
        $indentation = str_repeat( ' ', ezcDocumentDocbookToRstConverter::$indentation );

        // Show directive with given parameters
        $directive = sprintf( "\n%s.. %s:: %s\n",
            $indentation,
            $name,
            $parameter
        );

        // Append options
        foreach ( $options as $key => $value )
        {
            $directive .= sprintf( "%s   :%s: %s\n",
                $indentation,
                ezcDocumentDocbookToRstConverter::escapeRstText( $key ),
                ezcDocumentDocbookToRstConverter::escapeRstText( $value )
            );
        }

        // Append content, if given
        if ( $content !== null )
        {
            $directive .= "\n" . str_repeat( ' ', ezcDocumentDocbookToRstConverter::$indentation + 3 ) .
                trim( ezcDocumentDocbookToRstConverter::wordWrap( $content, 3 ) ) . "\n";
        }

        // Append an additional newline after the directive contents
        $directive .= "\n";

        return $directive;
    }
}

?>
