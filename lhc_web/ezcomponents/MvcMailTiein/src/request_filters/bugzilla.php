<?php
/**
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.0.1
 * @filesource
 * @package MvcTools
 */

/**
 * Request filter that ...
 *
 * @package MvcTools
 * @version 1.0.1
 * @mainclass
 */
class ezcMvcMailBugzillaRequestFilter implements ezcMvcRequestFilter
{
    /**
     * This function
     *
     * @param ezcMvcRequest $request
     */
    public function filterRequest( ezcMvcRequest $request )
    {
        // set the short_desc from the subject variable and init description
        $request->variables['short_desc'] = $request->variables['subject'];
        $request->variables['description'] = '';

        $lastTag = '';
        $inHeader = true;
        $lines = explode( "\n", $request->body );
        foreach ( $lines as $line )
        {
            $line = trim( $line );
            // check if we have a tag
            if ( $inHeader && preg_match( '/^@([a-z_]+)\s*=\s*(.*)$/', $line, $matches ) )
            {
                if ( in_array( $matches[1], array( 'product', 'component',
                    'version', 'short_desc', 'rep_platform',
                    'bug_severity', 'priority', 'op_sys',
                    'assigned_to', 'bug_file_loc',
                    'status_whiteboard', 'target_milestone',
                    'group_set', 'qa_contact' ) ) )
                {
                    $lastTag = $matches[1];
                    $request->variables[$lastTag] = $matches[2];
                }
            }
            else if ( $inHeader && !empty( $line ) )
            {
                $request->variables[$lastTag] .= ' ' . $line;
            }
            else if ( $inHeader && empty( $line ) )
            {
                $inHeader = false;
            }
            else
            {
                if ( !empty( $line ) )
                {
                    $request->variables['description'] .= ' ' . $line;
                }
            }
        }
        $request->variables['description'] = trim( $request->variables['description'] );
    }

    /**
     * Should not be called with any options, as this filter doesn't support any.
     *
     * @throws ezcMvcFilterHasNoOptionsException if the $options array is not
     * empty.
     * @param array $options
     */
    public function setOptions( array $options )
    {
        if ( count( $options ) )
        {
            throw new ezcMvcFilterHasNoOptionsException( __CLASS__ );
        }
    }
}
?>
