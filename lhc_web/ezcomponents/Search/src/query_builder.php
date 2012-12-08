<?php
/**
 * File containing the ezcSearchQueryBuilder class.
 *
 * @package Search
 * @version 1.0.9
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcSearchQueryBuilder provides a method to add a natural language search
 * query to an exisiting query object.
 *
 * @package Search
 * @version 1.0.9
 * @mainclass
 */
class ezcSearchQueryBuilder
{
    /**
     * Holds the parser's state
     *
     * @var string
     */
    private $state;

    /**
     * Keeps a list of where clauses per nested level
     *
     * @var array(array(string))
     */
    private $stack;

    /**
     * Contains the current stack level
     *
     * @var int
     */
    private $stackLevel;

    /**
     * Contains the current stack elements query type ('default', 'and' or 'or').
     *
     * @var string
     */
    private $stackType;

    /**
     * Contains a prefix for the following clause ('+', '-' or null).
     *
     * @var mixed
     */
    private $prefix;

    /**
     * Parses the $searchQuery and adds the selection clauses to the $query object
     *
     * @param ezcSearchQuery $query
     * @param string $searchQuery
     * @param array(string) $searchFields
     */
    public function parseSearchQuery( ezcSearchQuery $query, $searchQuery, $searchFields )
    {
        $this->reset();

        $tokens = $this->tokenize( $searchQuery );
        $this->buildQuery( $query, $tokens, $searchFields );
        if ( $this->stackType[0] == 'and' || $this->stackType[0] == 'default' )
        {
            foreach ( $this->stack[0] as $element )
            {
                $query->where( $element );
            }
        }
        else
        {
            $query->where( $query->lOr( $this->stack[0] ) );
        }
    }

    /**
     * Resets the parser to its initial state.
     */
    public function reset()
    {
        $this->state = 'normal';
        $this->stackLevel = 0;
        $this->stack = array();
        $this->stack[$this->stackLevel] = array();
        $this->stackType = array();
        $this->stackType[$this->stackLevel] = 'default';
        $this->prefix = null;
    }

    /**
     * Tokenizes the search query into tokens
     *
     * @param string $searchQuery
     * @return array(ezcSearchQueryToken)
     */
    static protected function tokenize( $searchQuery )
    {
        $map = array(
            ' '  => ezcSearchQueryToken::SPACE,
            '\t' => ezcSearchQueryToken::SPACE,
            '"'  => ezcSearchQueryToken::QUOTE,
            '+'  => ezcSearchQueryToken::PLUS,
            '-'  => ezcSearchQueryToken::MINUS,
            '('  => ezcSearchQueryToken::BRACE_OPEN,
            ')'  => ezcSearchQueryToken::BRACE_CLOSE,
            'and' => ezcSearchQueryToken::LOGICAL_AND,
            'or'  => ezcSearchQueryToken::LOGICAL_OR,
            ':'   => ezcSearchQueryToken::COLON,
        );
        $tokens = array();
        $tokenArray = preg_split( '@(\s)|(["+():-])@', $searchQuery, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );
        foreach ( $tokenArray as $token )
        {
            if ( isset( $map[strtolower( $token )] ) )
            {
                $tokens[] = new ezcSearchQueryToken( $map[strtolower( $token )], $token );
            }
            else
            {
                $tokens[] = new ezcSearchQueryToken( ezcSearchQueryToken::STRING, $token );
            }
        }
        return $tokens;
    }

    /**
     * Applies the current prefix to the clause in $string
     *
     * @param ezcSearchQuery $q
     * @param string $string
     *
     * @return string
     */
    private function processPrefix( ezcSearchQuery $q, $string )
    {
        switch ( $this->prefix )
        {
            case ezcSearchQueryToken::PLUS:
                $string = $q->important( $string );
                break;

            case ezcSearchQueryToken::MINUS:
                $string = $q->not( $string );
                break;
        }
        return $string;
    }

    /**
     * Assembles a query part for a search term for the fields passed in $searchFields
     *
     * If there is only one search field, it just processes the prefix. In case
     * there are multiple fields they are joined together with OR, unless the
     * whole clause is negated. In that case they're joined by AND.
     *
     * @param ezcSearchQuery $q
     * @param string $term
     * @param array(string) $searchFields
     *
     * @return string
     */
    private function constructSearchWhereClause( ezcSearchQuery $q, $term, $searchFields )
    {
        if ( count( $searchFields ) > 1 )
        {
            $parts = array();
            foreach ( $searchFields as $searchField )
            {
                $parts[] = $this->processPrefix( $q, $q->eq( $searchField, $term ) );
            }
            if ( $this->prefix == ezcSearchQueryToken::MINUS )
            {
                $ret = $q->lAnd( $parts );
            }
            else
            {
                $ret = $q->lOr( $parts );
            }
        }
        else
        {
            $ret = $this->processPrefix( $q, $q->eq( $searchFields[0], $term ) );
        }
        $this->prefix = null;
        return $ret;
    }

    /**
     * Walks over the $tokens and builds the query $q from them and the $searchFields
     *
     * @param ezcSearchQuery $q
     * @param array(ezcSearchQueryToken) $tokens
     * @param array(string) $searchFields
     *
     * @throws ezcSearchBuildQueryException if there is an uneven set of quotes.
     */
    protected function buildQuery( ezcSearchQuery $q, $tokens, $searchFields )
    {
        foreach ( $tokens as $token )
        {
            switch ( $this->state )
            {
                case 'normal':
                    switch ( $token->type )
                    {
                        case ezcSearchQueryToken::SPACE:
                            /* ignore */
                            break;

                        case ezcSearchQueryToken::STRING:
                            $this->stack[$this->stackLevel][] = $this->constructSearchWhereClause( $q, $token->token, $searchFields );
                            break;

                        case ezcSearchQueryToken::QUOTE:
                            $this->state = 'in-quotes';
                            $string = '';
                            break;

                        case ezcSearchQueryToken::LOGICAL_OR:
                            if ( $this->stackType[$this->stackLevel] === 'and' )
                            {
                                throw new ezcSearchBuildQueryException( 'You can not mix AND and OR without using "(" and ")".' );
                            }
                            else
                            {
                                $this->stackType[$this->stackLevel] = 'or';
                            }
                            break;

                        case ezcSearchQueryToken::LOGICAL_AND:
                            if ( $this->stackType[$this->stackLevel] === 'or' )
                            {
                                throw new ezcSearchBuildQueryException( 'You can not mix OR and AND without using "(" and ")".' );
                            }
                            else
                            {
                                $this->stackType[$this->stackLevel] = 'and';
                            }
                            break;

                        case ezcSearchQueryToken::BRACE_OPEN:
                            $this->stackLevel++;
                            $this->stackType[$this->stackLevel] = 'default';
                            break;

                        case ezcSearchQueryToken::BRACE_CLOSE:
                            $this->stackLevel--;
                            if ( $this->stackType[$this->stackLevel + 1] == 'and' || $this->stackType[$this->stackLevel + 1] == 'default' )
                            {
                                $this->stack[$this->stackLevel][] = $q->lAnd( $this->stack[$this->stackLevel + 1] );
                            }
                            else
                            {
                                $this->stack[$this->stackLevel][] = $q->lOr( $this->stack[$this->stackLevel + 1] );
                            }
                            break;

                        case ezcSearchQueryToken::PLUS:
                        case ezcSearchQueryToken::MINUS:
                            $this->prefix = $token->type;
                            break;
                    }
                    break;


                case 'in-quotes':
                    switch ( $token->type )
                    {
                        case ezcSearchQueryToken::QUOTE:
                            $this->stack[$this->stackLevel][] = $this->constructSearchWhereClause( $q, $string, $searchFields );
                            $this->state = 'normal';
                            break;

                        case ezcSearchQueryToken::STRING:
                        case ezcSearchQueryToken::COLON:
                        case ezcSearchQueryToken::SPACE:
                        case ezcSearchQueryToken::LOGICAL_AND:
                        case ezcSearchQueryToken::LOGICAL_OR:
                        case ezcSearchQueryToken::PLUS:
                        case ezcSearchQueryToken::MINUS:
                        case ezcSearchQueryToken::BRACE_OPEN:
                        case ezcSearchQueryToken::BRACE_CLOSE:
                            $string .= $token->token;
                            break;
                    }
                    break;
            }
        }

        if ( $this->state == 'in-quotes' )
        {
            throw new ezcSearchBuildQueryException( 'Unterminated quotes in query string.' );
        }
    }
}
?>
