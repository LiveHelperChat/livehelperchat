<?php
/**
 * File containing the ezcSearchFindQuery class.
 *
 * @package Search
 * @version 1.0.9
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class to create select search backend indepentent search queries.
 *
 * @package Search
 * @version 1.0.9
 * @mainclass
 */
interface ezcSearchQuery
{
    /**
     * Creates a new search query with handler $handler and document definition $definition.
     *
     * @param ezcSearchHandler $handler
     * @param ezcSearchDocumentDefinition $definition
     */
    public function __construct( ezcSearchHandler $handler, ezcSearchDocumentDefinition $definition );

    /**
     * Resets the query object for reuse.
     *
     * @return void
     */
    public function reset();

    /**
     * Adds a select/filter statement to the query
     *
     * @param string $clause
     * @return ezcSearchQuery
     */
    public function where( $clause );

    /**
     * Returns the query as a string for debugging purposes
     *
     * @return string
     * @ignore
     */
    public function getQuery();

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
    public function eq( $field, $value );

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
    public function between( $field, $value1, $value2 );

    /**
     * Creates an OR clause
     *
     * This method accepts either an array of fieldnames, but can also accept
     * multiple parameters as field names.
     *
     * @param mixed $...
     * @return string
     */
    public function lOr();

    /**
     * Creates an AND clause
     *
     * This method accepts either an array of fieldnames, but can also accept
     * multiple parameters as field names.
     *
     * @param mixed $...
     * @return string
     */
    public function lAnd();

    /**
     * Creates a NOT clause
     *
     * This method accepts a clause and negates it.
     *
     * @param string $clause
     * @return string
     */
    public function not( $clause );
}

?>
