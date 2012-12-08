<?php
/**
 * File containing the ezcTemplateBodyAstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents a body consisting of statements.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateBodyAstNode extends ezcTemplateAstNode
{
    /**
     * Array of statements which make up the body.
     * @var array(ezcTemplateStatementAstNode)
     */
    public $statements;

    /**
     * Initialize with function name code and optional arguments
     *
     * @param array(ezcTemplateStatementAstNode) $statements
     */
    public function __construct( Array $statements = null )
    {
        parent::__construct();
        $this->statements = array();

        if ( $statements !== null )
        {
            foreach ( $statements as $statement )
            {
                if ( !$statement instanceof ezcTemplateStatementAstNode )
                {
                    throw new ezcTemplateInternalException( "Body code element can only use objects of instance ezcTemplateStatementAstNode as statements" );
                }
            }
            $this->statements = $statements;
        }
    }

    /**
     * Appends the statement to the current list of statements.
     *
     * @param ezcTemplateStatementAstNode $statement Statement object to append.
     */
    public function appendStatement( ezcTemplateStatementAstNode $statement )
    {
        $this->statements[] = $statement;
    }

    /**
     * Returns the last statement object from the body.
     * If there are no statements in the body it returns null.
     *
     * @return ezcTemplateStatementAstNode
     */
    public function getLastStatement()
    {
        $count = count( $this->statements );
        if ( $count === 0 )
        {
            return null;
        }
        return $this->statements[$count - 1];
    }
}
?>
