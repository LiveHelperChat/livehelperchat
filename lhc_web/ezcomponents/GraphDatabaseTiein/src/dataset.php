<?php
/**
 * File containing the ezcGraphDatabaseDataSet class
 *
 * @package GraphDatabaseTiein
 * @version 1.0.1
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Class to transform PDO results into ezcGraphDataSets
 *
 * @package GraphDatabaseTiein
 * @version 1.0.1
 * @mainclass
 */
class ezcGraphDatabaseDataSet extends ezcGraphDataSet
{
    /**
     * Constructor
     * 
     * Creates a ezcGraphDatabase from a PDOStatement and uses the columns 
     * defined in the definition array as keys and values for the data set. 
     *
     * If the definition array is empty a single column will be used as values,
     * with two columns the first column will be used for the keys and the 
     * second for the data set values.
     *
     * You may define the name of the rows used for keys and values by using 
     * an array like:
     *  array (
     *      ezcGraph::KEY => 'row name',
     *      ezcGraph::VALUE => 'row name',
     *  );
     *
     * PDO by default lowercases all column names, see PDO::setAttribute() for
     * details. If the column names you pass to the dataset definition array
     * are not lowercase, you either need to change the PDO::ATTR_CASE
     * attribute of your PDO connection instance, or lowercase the names passed
     * to the definition array. Otherwise this will throw
     * ezcGraphDatabaseMissingColumnException exceptions.
     *
     * @param PDOStatement $statement
     * @param array $definition
     * @return ezcGraphDatabase
     */
    public function __construct( PDOStatement $statement, array $definition = null )
    {
        parent::__construct();

        $this->data = array();
        $this->createFromPdo( $statement, $definition );
    }

    /**
     * Create dataset from PDO statement
     *
     * This methods uses the values from a PDOStatement to fill up the data 
     * sets data.
     *
     * If the definition array is empty a single column will be used as values,
     * with two columns the first column will be used for the keys and the 
     * second for the data set values.
     *
     * You may define the name of the rows used for keys and values by using 
     * an array like:
     *  array (
     *      ezcGraph::KEY => 'row name',
     *      ezcGraph::VALUE => 'row name',
     *  );
     * 
     * @param PDOStatement $statement
     * @param array $definition
     * @return void
     */
    protected function createFromPdo( PDOStatement $statement, array $definition = null ) 
    {
        if ( $definition === null )
        {
            $this->fetchNumeric( $statement );
        }
        else
        {
            $this->fetchByDefinition( $statement, $definition );
        }

        // Empty result set
        if ( count( $this->data ) <= 0 )
        {
            throw new ezcGraphDatabaseStatementNotExecutedException( $statement );
        }
    }

    /**
     * Fetch numeric
     *
     * If there is only one column returned by the query use the column as
     * values for the dataset. If there are two columns returned use the first
     * as key and the second as values for teh dataset.
     *
     * If there are more or less columns returned, a
     * ezcGraphDatabaseTooManyColumnsException is thrown.
     * 
     * @param PDOStatement $statement 
     * @return void
     */
    protected function fetchNumeric( PDOStatement $statement )
    {
        while ( $row = $statement->fetch( PDO::FETCH_NUM ) )
        {
            switch ( count( $row ) )
            {
                case 1:
                    $this->data[] = $row[0];
                    break;
                case 2:
                    $this->data[$row[0]] = $row[1];
                    break;
                default:
                    throw new ezcGraphDatabaseTooManyColumnsException( $row );
            }
        }
    }

    /**
     * Fecth data by provided definition
     *
     * Use the provided definition with column names to fetch specific columns
     * as key and values. Will throw a ezcGraphDatabaseMissingColumnException,
     * if the column does not exist in the query result.
     *
     * @param PDOStatement $statement 
     * @param array $definition 
     * @return void
     */
    protected function fetchByDefinition( PDOStatement $statement, array $definition )
    {
        while ( $row = $statement->fetch( PDO::FETCH_NAMED ) )
        {
            if ( !array_key_exists( $definition[ezcGraph::VALUE], $row ) )
            {
                throw new ezcGraphDatabaseMissingColumnException( $definition[ezcGraph::VALUE] );
            }

            $value = $row[$definition[ezcGraph::VALUE]];

            if ( array_key_exists( ezcGraph::KEY, $definition ) )
            {
                if ( !array_key_exists( $definition[ezcGraph::KEY], $row ) )
                {
                    throw new ezcGraphDatabaseMissingColumnException( $definition[ezcGraph::KEY] );
                }

                $this->data[$row[$definition[ezcGraph::KEY]]] = $value;
            }
            else
            {
                $this->data[] = $value;
            }
        }
    }

    /**
     * Returns the number of elements in this dataset
     * 
     * @return int
     */
    public function count()
    {
        return count( $this->data );
    }
}

?>
