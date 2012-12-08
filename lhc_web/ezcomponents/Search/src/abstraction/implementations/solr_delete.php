<?php
/**
 * File containing the ezcSearchQuerySolr class.
 *
 * @package Search
 * @version 1.0.9
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcSearchQuerySolr implements the find query for searching documents.
 *
 * @package Search
 * @version 1.0.9
 */
class ezcSearchDeleteQuerySolr implements ezcSearchDeleteQuery
{
    /**
     * Holds all the search clauses that will be used to create the search query.
     *
     * @var array(string)
     */
    public $whereClauses;

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
     * Constructs a new ezcSearchQuerySolr object for the handler $handler
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
        $this->whereClauses = array();
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
        return $this->handler->getDeleteQuery( $this );
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
     * Adds a select/filter statement to the query
     *
     * @param string $clause
     * @return ezcSearchQuerySolr
     */
    public function where( $clause )
    {
        $this->whereClauses[] = $clause;
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
}
?>
