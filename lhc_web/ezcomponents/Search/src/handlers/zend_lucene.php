<?php
/**
 * File containing the ezcSearchZendLuceneHandler class.
 *
 * @package Search
 * @version 1.0.9
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @ignore
 */

/**
 * ZendLucene backend implementation
 *
 * @package Search
 * @version 1.0.9
 * @ignore
 */
class ezcSearchZendLuceneHandler implements ezcSearchHandler, ezcSearchIndexHandler
{
    /**
     * Holds the connection to ZendLucene
     *
     * @var resource(stream)
     */
    public $connection;

    /**
     * Stores the transaction nesting depth.
     *
     * @var integer
     */
    private $inTransaction;

    /**
     * Creates a new ZendLucene handler connection
     *
     * @param string $location
     */
    public function __construct( $location )
    {
        /**
         * We're using realpath here because Zend_Search_Lucene does not do
         * that itself. It can cause issues because their destructor uses the
         * same filename but the cwd could have been changed.
         */
        $location = realpath( $location );

        /* If the $location doesn't exist, ZSL throws a *generic* exception. We
         * don't care here though and just always assume it is because the
         * index does not exist. If it doesn't exist, we create it.
         */
        try
        {
            $this->connection = Zend_Search_Lucene::open( $location );
        }
        catch ( Zend_Search_Lucene_Exception $e )
        {
            $this->connection = Zend_Search_Lucene::create( $location );
        }
        $this->inTransaction = 0;
        if ( !$this->connection )
        {
            throw new ezcSearchCanNotConnectException( 'zendlucene', $location );
        }

        // Set proper default encoding for query parser
        Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding( 'UTF-8' );
        Zend_Search_Lucene_Analysis_Analyzer::setDefault( 
            new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive()
        );
    }

    /**
     * Starts a transaction for indexing.
     *
     * When using a transaction, the amount of processing that zendlucene does
     * decreases, increasing indexing performance. Without this, the component
     * sends a commit after every document that is indexed. Transactions can be
     * nested, when commit() is called the same number of times as
     * beginTransaction(), the component sends a commit.
     */
    public function beginTransaction()
    {
    }

    /**
     * Ends a transaction and calls commit.
     *
     * As transactions can be nested, this method will only call commit when
     * all the nested transactions have been ended.
     *
     * @throws ezcSearchTransactionException if no transaction is active.
     */
    public function commit()
    {
        $this->connection->optimize();
    }

    /**
     * Builds query parameters from the different query fields
     *
     * @param string $queryWord
     * @param array(string=>string) $searchFieldList
     * @return array
     */
    private function buildQuery( $queryWord, $searchFieldList = array() )
    {
        if ( count( $searchFieldList ) > 0 )
        {
            $queryString = '';
            foreach ( $searchFieldList as $searchField )
            {
                $queryString .= "$searchField:$queryWord ";
            }
        }
        else
        {
            $queryString = $queryWord;
        }
        return $queryString;
    }

    private function createDataForHit( $hit, $def )
    {
        $document = $hit->getDocument();
        $className = $def->documentType;
        $obj = new $className;

        $attr = array();
        foreach ( $def->fields as $field )
        {
            $fieldName = $this->mapFieldType( $field->field, $field->type );
            if ( $field->inResult /*&& isset( $document->$fieldName ) */ )
            {
                $attr[$field->field] = $this->mapFieldValuesForReturn( $field, $document->$fieldName );
            }
        }
        $obj->setState( $attr );
        return new ezcSearchResultDocument( $hit->score, $obj );
    }

    /**
     * Converts a raw zendlucene result into a document using the definition $def
     *
     * @param ezcSearchQuery $query
     * @param mixed $response
     * @return ezcSearchResult
     */
    private function createResponseFromData( ezcSearchQuery $query, $response )
    {
        $def = $query->getDefinition();
        $s = new ezcSearchResult();
        $s->resultCount = count( $response );

        $counter = -1;

        foreach ( $response as $hit )
        {
            $counter++;
            if ( $counter < $query->offset )
            {
                continue;
            }
            if ( $query->limit !== null && $counter >= ( $query->limit + $query->offset ) )
            {
                break;
            }
            $resultDocument = $this->createDataForHit( $hit, $def );

            $idProperty = $def->idProperty;
            $s->documents[$resultDocument->document->$idProperty] = $resultDocument;
        }

        return $s;
    }

    /**
     * Executes a search by building and sending a query and returns the raw result
     *
     * @param string $queryWord
     * @param array(string=>string) $searchFieldList
     * @param array(string=>string) $order
     * @return stdClass
     */
    public function search( $queryWord, $searchFieldList = array(), $order = array() )
    {
        $query = $this->buildQuery( $queryWord, $searchFieldList );
        $args = array();
        $args[] = $query;

        if ( is_array( $order ) )
        {
            foreach ( $order as $field => $sort )
            {
                $args[] = $field;
                $args[] = SORT_REGULAR;
                $args[] = $sort == ezcSearchQueryTools::ASC ? SORT_ASC : SORT_DESC;
            }
        }

        $result = call_user_func_array( array( $this->connection, 'find' ), $args );
        return $result;
    }

    /**
     * Returns 'zendlucene'.
     *
     * @return string
     */
    static public function getName()
    {
        return 'zendlucene';
    }

    /**
     * Creates a search query object with the fields from the definition filled in.
     *
     * @param string $type
     * @param ezcSearchDocumentDefinition $definition
     * @return ezcSearchFindQuery
     */
    public function createFindQuery( $type, ezcSearchDocumentDefinition $definition )
    {
        $query = new ezcSearchQueryZendLucene( $this, $definition );
        $query->select( 'score' );
        if ( $type )
        {
            $selectFieldNames = array();
            foreach ( $definition->getSelectFieldNames() as $docProp )
            {
                $selectFieldNames[] = $this->mapFieldType( $docProp, $definition->fields[$docProp]->type );
            }
            $highlightFieldNames = array();
            foreach ( $definition->getHighlightFieldNames() as $docProp )
            {
                $highlightFieldNames[] = $this->mapFieldType( $docProp, $definition->fields[$docProp]->type );
            }
            $query->select( $selectFieldNames );
            $query->where( $query->eq( 'ezcsearch_type', $type ) );
            $query->highlight( $highlightFieldNames );
        }
        return $query;
    }

    /**
     * Builds the search query and returns the parsed response
     *
     * @param ezcSearchFindQuery $query
     * @return ezcSearchResult
     */
    public function find( ezcSearchFindQuery $query )
    {
        $queryWord = join( ' AND ', $query->whereClauses );
        $order = $query->orderByClauses;

        $res = $this->search( $queryWord, array(), $order );
        return $this->createResponseFromData( $query, $res );
    }

    /**
     * Returns the query as a string for debugging purposes
     *
     * @param ezcSearchQueryZendLucene $query
     * @return string
     * @ignore
     */
    public function getQuery( ezcSearchQueryZendLucene $query )
    {
        $queryWord = join( ' AND ', $query->whereClauses );

        return $this->buildQuery( $queryWord );
    }

    /**
     * Returns the field name as used by zendlucene created from the field $name and $type.
     *
     * @param string $name
     * @param string $type
     * @return string
     */
    public function mapFieldType( $name, $type )
    {
        return $name;
    }

    /**
     * This method prepares a $value before it is passed to the indexer.
     *
     * Depending on the $fieldType the $value is modified so that the indexer understands the value.
     *
     * @param string $fieldType
     * @param mixed $value
     * @return mixed
     */
    public function mapFieldValueForIndex( $fieldType, $value )
    {
        switch ( $fieldType )
        {
            case ezcSearchDocumentDefinition::DATE:
                if ( is_numeric( $value ) )
                {
                    $d = new DateTime( "@$value" );
                    $value = $d->format( 'U' );
                }
                else
                {
                    try
                    {
                        $d = new DateTime( $value );
                    }
                    catch ( Exception $e )
                    {
                        throw new ezcSearchInvalidValueException( $type, $value );
                    }
                    $value = $d->format( 'U' );
                }
                break;

            case ezcSearchDocumentDefinition::BOOLEAN:
                $value = $value ? 'true' : 'false';
                break;
        }
        return $value;
    }

    /**
     * This method prepares a $value before it is passed to the search handler.
     *
     * Depending on the $fieldType the $value is modified so that the search
     * handler understands the value.
     *
     * @param string $fieldType
     * @param mixed $value
     * @return mixed
     */
    public function mapFieldValueForSearch( $fieldType, $value )
    {
        switch ( $fieldType )
        {
            case ezcSearchDocumentDefinition::STRING:
            case ezcSearchDocumentDefinition::TEXT:
            case ezcSearchDocumentDefinition::HTML:
                $value = trim( $value );
                if ( strpbrk( $value, ' "' ) !== false )
                {
                    $value = '"' . str_replace( '"', '\"', $value ) . '"';
                }
                break;

            case ezcSearchDocumentDefinition::INT:
            case ezcSearchDocumentDefinition::FLOAT:
                $value = '"' . $value . '"';
                break;

            case ezcSearchDocumentDefinition::DATE:
                if ( is_numeric( $value ) )
                {
                    $d = new DateTime( "@$value" );
                    $value = $d->format( 'U' );
                }
                else
                {
                    try
                    {
                        $d = new DateTime( $value );
                    }
                    catch ( Exception $e )
                    {
                        throw new ezcSearchInvalidValueException( $type, $value );
                    }
                    $value = $d->format( 'U' );
                }
                break;

            case ezcSearchDocumentDefinition::BOOLEAN:
                $value = ($value ? 'true' : 'false');
                break;
        }
        return $value;
    }

    /**
     * This method prepares a $value before it is passed to the search handler.
     *
     * Depending on the $fieldType the $value is modified so that the search
     * handler understands the value.
     *
     * @param string $fieldType
     * @param mixed $value
     * @return mixed
     */
    public function mapFieldValueForReturn( $fieldType, $value )
    {
        switch ( $fieldType )
        {
            case ezcSearchDocumentDefinition::BOOLEAN:
                $value = $value == 'true' ? true : false;
                break;

            case ezcSearchDocumentDefinition::DATE:
                $value = new DateTime( "@$value" );
                break;

        }
        return $value;
    }

    /**
     * This method prepares a value or an array of $values before it is passed to the search handler.
     *
     * Depending on the $field the $values is modified so that the search
     * handler understands the value. It will also correctly deal with
     * multi-data fields in the search index.
     *
     * @throws ezcSearchInvalidValueException if an array of values is
     *         submitted, but the field has not been defined as a multi-value field.
     *
     * @param ezcSearchDocumentDefinitionField $field
     * @param mixed $values
     * @return array(mixed)
     */
    public function mapFieldValuesForSearch( $field, $values )
    {
        if ( is_array( $values ) && $field->multi == false )
        {
            throw new ezcSearchInvalidValueException( $field->type, $values, 'multi' );
        }
        if ( !is_array( $values ) )
        {
            $values = array( $values );
        }
        foreach ( $values as &$value )
        {
            $value = $this->mapFieldValueForSearch( $field->type, $value );
        }
        return $values;
    }

    /**
     * This method prepares a value or an array of $values before it is passed to the indexer.
     *
     * Depending on the $field the $values is modified so that the search
     * handler understands the value. It will also correctly deal with
     * multi-data fields in the search index.
     *
     * @throws ezcSearchInvalidValueException if an array of values is
     *         submitted, but the field has not been defined as a multi-value field.
     *
     * @param ezcSearchDocumentDefinitionField $field
     * @param mixed $values
     * @return array(mixed)
     */
    public function mapFieldValuesForIndex( $field, $values )
    {
        if ( is_array( $values ) && $field->multi == false )
        {
            throw new ezcSearchInvalidValueException( $field->type, $values, 'multi' );
        }
        if ( !is_array( $values ) )
        {
            $values = array( $values );
        }
        foreach ( $values as &$value )
        {
            $value = $this->mapFieldValueForIndex( $field->type, $value );
        }
        return $values;
    }

    /**
     * This method prepares a value or an array of $values after it has been returned by search handler.
     *
     * Depending on the $field the $values is modified.  It will also correctly
     * deal with multi-data fields in the search index.
     *
     * @param ezcSearchDocumentDefinitionField $field
     * @param mixed $values
     * @return mixed|array(mixed)
     */
    public function mapFieldValuesForReturn( $field, $values )
    {
        $values = $this->mapFieldValueForReturn( $field->type, $values );
        return $values;
    }

    /**
     * Runs a commit command to tell zendlucene we're done indexing.
     */
    protected function runCommit()
    {
        $r = $this->sendRawPostCommand( 'update', array( 'wt' => 'json' ), '<commit/>' );
    }

    /**
     * Indexes the document $document using definition $definition
     *
     * @param ezcSearchDocumentDefinition $definition
     * @param mixed $document
     */
    public function index( ezcSearchDocumentDefinition $definition, $document )
    {
        $doc = new Zend_Search_Lucene_Document();

        $doc->addField( Zend_Search_Lucene_Field::Text( 'ezcsearch_type', $definition->documentType ) );
        foreach ( $definition->fields as $field )
        {
            $values = $this->mapFieldValuesForIndex( $field, $document[$field->field] );
            foreach ( $values as $value )
            {
                switch ( $field->type )
                {
                    case ezcSearchDocumentDefinition::INT:
                    case ezcSearchDocumentDefinition::DATE:
                        $doc->addField( Zend_Search_Lucene_Field::Keyword( $field->field, $value ) );
                        break;

                    default:
                        $doc->addField( Zend_Search_Lucene_Field::Text( $field->field, $value, 'UTF-8' ) );
                }
            }
        }
        $this->connection->addDocument( $doc );
    }

    /**
     * Creates a delete query object with the fields from the definition filled in.
     *
     * @param string $type
     * @param ezcSearchDocumentDefinition $definition
     * @return ezcSearchDeleteQuery
     */
    public function createDeleteQuery( $type, ezcSearchDocumentDefinition $definition )
    {
        $query = new ezcSearchDeleteQueryZendLucene( $this, $definition );
        if ( $type )
        {
            $selectFieldNames = array();
            $query->where( $query->eq( 'ezcsearch_type', $type ) );
        }
        return $query;
    }

    /**
     * Deletes a document by the document's $id.
     *
     * If the document with ID $id does not exist, no warning is returned.
     *
     * @param mixed $id
     * @param ezcSearchDocumentDefinition $definition
     */
    public function deleteById( $id, ezcSearchDocumentDefinition $definition )
    {
        $idProperty = $definition->idProperty;
        $fieldName = $this->mapFieldType( $definition->fields[$idProperty]->field, $definition->fields[$idProperty]->type );
        $res = $this->search( "ezcsearch_type:{$definition->documentType} AND {$fieldName}:$id" );
        if ( count( $res ) == 1 )
        {
            $this->connection->delete( $res[0]->id );
        }
    }

    /**
     * Builds the delete query and returns the parsed response
     *
     * @param ezcSearchDeleteQuery $query
     */
    public function delete( ezcSearchDeleteQuery $query )
    {
        $queryWord = join( ' AND ', $query->whereClauses );

        $res = $this->search( $queryWord );
        foreach ( $res as $hit )
        {
            $this->connection->delete( $hit->id );
        }
    }

    /**
     * Finds a document by the document's $id
     *
     * @throws ezcSearchIdNotFoundException
     *         if the document with ID $id did not exist.
     *
     * @param mixed $id
     * @param ezcSearchDocumentDefinition $definition
     * @return ezcSearchResult
     */
    public function findById( $id, ezcSearchDocumentDefinition $definition )
    {
        $idProperty = $definition->idProperty;
        $fieldName = $this->mapFieldType( $definition->fields[$idProperty]->field, $definition->fields[$idProperty]->type );
        $res = $this->search( "{$fieldName}:$id" );
        if ( count( $res ) != 1 )
        {
            throw new ezcSearchIdNotFoundException( $id );
        }
        return $this->createDataForHit( $res[0], $definition );
    }
}
?>
