<?php
/**
 * File containing the ezcTemplateIfConditionTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Control structure: switch.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateSwitchTstNode extends ezcTemplateBlockTstNode
{
    public $condition;

    public $defaultCaseFound = false;

    /**
     *
     * @param ezcTemplateSource $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        parent::__construct( $source, $start, $end );
        $this->condition = null;
        $this->name = "switch";
    }

    public function getTreeProperties()
    {
        return array( 'name'      => $this->name,
                      'condition' => $this->condition,
                      'children'  => $this->children );
    }

    public function handleElement( ezcTemplateTstNode $element )
    {
        if ( $element instanceof ezcTemplateCaseTstNode  )
        {
            if ( $element->conditions === null )
            {
                if ( $this->defaultCaseFound )
                {
                    throw new ezcTemplateParserException( $element->source, $element->startCursor, $element->startCursor, ezcTemplateSourceToTstErrorMessages::MSG_DEFAULT_DUPLICATE );
                }

                $this->defaultCaseFound = true;
            }
            elseif ( $this->defaultCaseFound ) // Found a default case already..
            {
                throw new ezcTemplateParserException( $element->source, $element->startCursor, $element->startCursor, ezcTemplateSourceToTstErrorMessages::MSG_DEFAULT_LAST );
            }

            $this->children[] = $element;
            return true;


            // parent::handleElement( $element );
        }
        elseif ( $element instanceof ezcTemplateDocCommentTstNode )
        {
            parent::handleElement( $element );
        }
        else
        {
            if ( $element instanceof ezcTemplateTextBlockTstNode )
            {
                // Only spaces, newlines and tabs?
                if ( preg_match( "#^\s*$#", $element->text) != 0 )
                {
                    // It's okay, but ignore it.
                    return;
                }
                else
                {
                    $trimmedLength = strlen( ltrim( $element->text ) );
                    $element->startCursor->advance( strlen($element->text) - $trimmedLength );
                }
            }

            throw new ezcTemplateParserException( $element->source, $element->startCursor, $element->startCursor, ezcTemplateSourceToTstErrorMessages::MSG_EXPECT_CASE_STATEMENT );
        }
    }
}
?>
