<?php
/**
 * File containing the ezcSearchQueryZendLucene class.
 *
 * @package Search
 * @version 1.0.9
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @ignore
 */

/**
 * ezcSearchQueryZendLucene implements the find query for searching documents.
 *
 * @package Search
 * @version 1.0.9
 * @ignore
 */
class ezcSearchQueryZendLucene implements ezcSearchFindQuery
{
    /**
     * Holds the columns to return in the search result
     *
     * @var array
     */
    public $resultFields;

    /**
     * Holds the columns to highlight in the search result
     *
     * @var array(string)
     */
    public $highlightFields;

    /**
     * Holds all the search clauses that will be used to create the search query.
     *
     * @var array(string)
     */
    public $whereClauses;

    /**
     * Holds all the order by clauses that will be used to create the search query.
     *
     * @var array(string)
     */
    public $orderByClauses;

    /**
     * Holds the maximum number of results for the query.
     *
     * @var int
     */
    public $limit = null;

    /**
     * Holds the number of the first element to return in the results.
     *
     * This is used in combination with the $limit option.
     *
     * @var int
     */
    public $offset;

    /**
     * Holds all the facets
     *
     * @var array(string)
     */
    public $facets;

    /**
     * Holds the search handler for which this query is built.
     *
     * @var ezcSearchHandler
     */
    private $handler;

    /**
     * Contains the document definition for which this query is built.
     *
     * @param ezcSearchDocumentDefinition $definition
     */
    private $definition;

    /**
     * Constructs a new ezcSearchQueryZendLucene object for the handler $handler
     *
     * The handler implements mapping field names and values based on the
     * document $definition.
     *
     * @param ezcSearchHandler $handler
     * @param ezcSearchDocumentDefinition $definition
     */
    public function __construct( ezcSearchHandler $handler, ezcSearchDocumentDefinition $definition )
    {
        $this->handler = $handler;
        $this->definition = $definition;
        $this->reset();
    }

    /**
     * Resets all the internal query values to their defaults.
     */
    public function reset()
    {
        $this->resultFields = array();
        $this->highlightFields = array();
        $this->whereClauses = array();
        $this->limit = null;
        $this->offset = 0;
        $this->facets = array();
    }

    /**
     * Returns the definition that belongs to this query
     *
     * @return ezcSearchDocumentDefinition
     */
    function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Returns the query as a string for debugging purposes
     *
     * @return string
     * @ignore
     */
    public function getQuery()
    {
        return $this->handler->getQuery( $this );
    }

    /**
     * Checks whether the field $field exists in the definition.
     *
     * @param string $field
     * @throws ezcSearchFieldNotDefinedException if the field is not defined.
     */
    private function checkIfFieldExists( $field )
    {
        if ( !isset( $this->definition->fields[$field] ) )
        {
            throw new ezcSearchFieldNotDefinedException( $this->definition->documentType, $field );
        }
    }

    /**
     * Adds the fields to return in the results.
     *
     * This method accepts either an array of fieldnames, but can also accept
     * multiple parameters as field names. The following is therefore
     * equivalent:
     * <code>
     * $q->select( array( 'one', 'two', 'three' ) );
     * $q->select( 'one', 'two', 'three' ) );
     * </code>
     *
     * If fields already have been added with this function, they will not be
     * overwritten when this function is called subsequently.
     *
     * @param mixed $...
     * @return ezcSearchQueryZendLucene
     */
    public function select()
    {
        $args = func_get_args();
        $cols = ezcSearchQueryTools::arrayFlatten( $args );
        $this->resultFields = array_merge( $this->resultFields, $cols );
        return $this;
    }

    /**
     * Adds the fields to highlight in the results.
     *
     * This method accepts either an array of fieldnames, but can also accept
     * multiple parameters as field names. The following is therefore
     * equivalent:
     * <code>
     * $q->highlight( array( 'one', 'two', 'three' ) );
     * $q->highlight( 'one', 'two', 'three' ) );
     * </code>
     *
     * If fields already have been added with this function, they will not be
     * overwritten when this function is called subsequently.
     *
     * @param mixed $...
     * @return ezcSearchQueryZendLucene
     */
    public function highlight()
    {
        $args = func_get_args();
        $cols = ezcSearchQueryTools::arrayFlatten( $args );
        $this->highlightFields = array_merge( $this->highlightFields, $cols );
        return $this;
    }

    /**
     * Adds a select/filter statement to the query
     *
     * @param string $clause
     * @return ezcSearchQueryZendLucene
     */
    public function where( $clause )
    {
        $this->whereClauses[] = $clause;
        return $this;
    }

    /**
     * Registers from which offset to start returning results, and how many results to return.
     *
     * $limit controls the maximum number of rows that will be returned.
     * $offset controls which row that will be the first in the result
     * set from the total amount of matching rows.
     *
     * @param int $limit
     * @param int $offset
     * @return ezcSearchQueryZendLucene
     */
    public function limit( $limit, $offset = 0 )
    {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

    /**
     * Tells the query on which field to sort on, and in which order
     *
     * You can call orderBy multiple times. Each call will add a
     * column to order by.
     *
     * @param string $field
     * @param int    $type
     * @return ezcSearchQueryZendLucene
     */
    public function orderBy( $field, $type = ezcSearchQueryTools::ASC )
    {
        $field = $this->handler->mapFieldType( $field, $this->definition->fields[$field]->type );
        $this->orderByClauses[$field] = $type;
        return $this;
    }

    /**
     * Adds one facet to the query.
     *
     * Facets should only be used for STRING fields, and not TEXT fields.
     *
     * @param string $facet
     * @return ezcSearchQueryZendLucene
     */
    public function facet( $facet )
    {
        $field = $this->handler->mapFieldType( $facet, $this->definition->fields[$facet]->type );
        $this->facets[] = $field;
        return $this;
    }

    /**
     * Returns a string containing a field/value specifier, and an optional boost value.
     * 
     * The method uses the document definition field type to map the fieldname
     * to a solr fieldname, and the $fieldType argument to escape the $value
     * correctly. If a definition is set, the $fieldType will be overridden
     * with the type from the definition.
     *
     * @param string $field
     * @param mixed $value
     *
     * @return string
     */
    public function eq( $field, $value )
    {
        $field = trim( $field );

        $this->checkIfFieldExists( $field );
        $fieldType = $this->definition->fields[$field]->type;
        $value = $this->handler->mapFieldValueForSearch( $fieldType, $value );
        $fieldName = $this->handler->mapFieldType( $field, $this->definition->fields[$field]->type );

        $ret = "$fieldName:$value";

        if ( $this->definition->fields[$field]->boost != 1 )
        {
            $ret .= "^{$this->definition->fields[$field]->boost}";
        }
        return $ret;
    }

    /**
     * Returns a string containing a field/value specifier, and an optional boost value.
     * 
     * The method uses the document definition field type to map the fieldname
     * to a solr fieldname, and the $fieldType argument to escape the values
     * correctly.
     *
     * @param string $field
     * @param mixed $value1
     * @param mixed $value2
     *
     * @return string
     */
    public function between( $field, $value1, $value2 )
    {
        $field = trim( $field );

        $this->checkIfFieldExists( $field );
        $fieldType = $this->definition->fields[$field]->type;
        $value1 = $this->handler->mapFieldValueForSearch( $fieldType, $value1 );
        $value2 = $this->handler->mapFieldValueForSearch( $fieldType, $value2 );
        $fieldName = $this->handler->mapFieldType( $field, $this->definition->fields[$field]->type );

        $ret = "$fieldName:[$value1 TO $value2]";

        if ( $this->definition->fields[$field]->boost != 1 )
        {
            $ret .= "^{$this->definition->fields[$field]->boost}";
        }
        return $ret;
    }

    /**
     * Creates an OR clause
     *
     * This method accepts either an array of fieldnames, but can also accept
     * multiple parameters as field names.
     *
     * @param mixed $...
     * @return string
     */
    public function lOr()
    {
        $args = func_get_args();
        if ( count( $args ) < 1 )
        {
            throw new ezcSearchQueryVariableParameterException( 'lOr', count( $args ), 1 );
        }

        $elements = ezcSearchQueryTools::arrayFlatten( $args );
        if ( count( $elements ) == 1 )
        {
            return $elements[0];
        }
        else
        {
            return '( ' . join( ' OR ', $elements ) . ' )';
        }
    }

    /**
     * Creates an AND clause
     *
     * This method accepts either an array of fieldnames, but can also accept
     * multiple parameters as field names.
     *
     * @param mixed $...
     * @return string
     */
    public function lAnd()
    {
        $args = func_get_args();
        if ( count( $args ) < 1 )
        {
            throw new ezcSearchQueryVariableParameterException( 'lAnd', count( $args ), 1 );
        }

        $elements = ezcSearchQueryTools::arrayFlatten( $args );
        if ( count( $elements ) == 1 )
        {
            return $elements[0];
        }
        else
        {
            return '( ' . join( ' AND ', $elements ) . ' )';
        }
    }

    /**
     * Creates a NOT clause
     *
     * This method accepts a clause and negates it.
     *
     * @param string $clause
     * @return string
     */
    public function not( $clause )
    {
        return "!$clause";
    }

    /**
     * Creates an 'important' clause
     *
     * This method accepts a clause and marks it as important.
     *
     * @param string $clause
     * @return string
     */
    public function important( $clause )
    {
        return "+$clause";
    }

    /**
     * Modifies a clause to give it higher weight while searching.
     *
     * This method accepts a clause and adds a boost factor.
     *
     * @param string $clause
     * @param float $boostFactor
     * @return string
     */
    public function boost( $clause, $boostFactor )
    {
        // make sure we only apply boost once
        if ( preg_match( '@(.*)\^[0-9]+(\.[0-9]+)?$@', $clause, $matches ) )
        {
            $clause = $matches[1];
        }
        return "$clause^$boostFactor";
    }

    /**
     * Modifies a clause make it fuzzy.
     *
     * This method accepts a clause and registers it as a fuzzy search, an
     * optional fuzz factor is also supported.
     *
     * @param string $clause
     * @param float $fuzzFactor
     * @return string
     */
    public function fuzz( $clause, $fuzzFactor = null )
    {
        if ( $fuzzFactor )
        {
            return "$clause~$fuzzFactor";
        }
        return "$clause~";
    }
}
?>
