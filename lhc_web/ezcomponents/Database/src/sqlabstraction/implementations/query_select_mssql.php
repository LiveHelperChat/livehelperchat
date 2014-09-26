<?php
/**
 * File containing the ezcQuerySelectMssql class.
 *
 * @package Database
 * @version 1.0
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * SQL Server specific implementation of ezcQuery.
 *
 * This class reimplements the LIMIT method in which the
 * SQL Server differs from the standard implementation in ezcQuery.
 *
 * @see ezcQuery
 * @package Database
 * @version 1.4.7
 */
class ezcQuerySelectMssql extends ezcQuerySelect
{
    /**
     * If a limit and/or offset has been set for this query.
     *
     * @var bool
     */
    private $hasLimit = false;

    /**
     * The limit set.
     *
     * @var int
     */
    private $limit = 0;

    /**
     * The offset set.
     *
     * @var int
     */
    private $offset = 0;

    /**
     * Same as ezcQuerySelect::$orderString but inverted
     * for use in the LIMIT functionality.
     *
     * @var string
     */
    private $invertedOrderString = null;

    /**
     * Resets the query object for reuse.
     *
     * @return void
     */
    public function reset()
    {
        $this->hasLimit = false;
        $this->limit = 0;
        $this->offset = 0;
        $this->orderColumns = array();
        parent::reset();
    }

    /**
     * Returns SQL that limits the result set.
     *
     * $limit controls the maximum number of rows that will be returned.
     * $offset controls which row that will be the first in the result
     * set from the total amount of matching rows.
     *
     * @param int $limit integer expression
     * @param int $offset integer expression
     * @return void
     */
    public function limit( $limit, $offset = 0 )
    {
        $this->hasLimit = true;
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

    /**
     * Saves the ordered columns in an internal array so we can invert that order
     * if we need to in the limit() workaround
     *
     * @param string $column a column name in the result set
     * @param string $type if the column should be sorted ascending or descending.
     *        you can specify this using ezcQuerySelect::ASC or ezcQuerySelect::DESC
     * @return ezcQuery a pointer to $this
     */
    public function orderBy( $column, $type = self::ASC )
    {
        if ( $this->invertedOrderString )
        {
            $this->invertedOrderString .= ', ';
        }
        else
        {
            $this->invertedOrderString = 'ORDER BY ';
        }
        $this->invertedOrderString .= $column . ' ' . ( $type == self::ASC ? self::DESC : self::ASC );
        return parent::orderBy( $column, $type );
    }

    /**
     * Transforms the $query to make it only select the $rowCount first rows
     *
     * @param int $rowCount number of rows to return
     * @param string $query SQL select query
     * @return string
     */
    static private function top( $rowCount, $query )
    {
        return 'SELECT TOP ' . $rowCount . substr( $query, strlen( 'SELECT' ) );
    }

    /**
     * Transforms the query from the parent to provide LIMIT functionality.
     *
     * Note: doesn't work exactly like the MySQL equivalent; it will always return
     * $limit rows even if $offset + $limit exceeds the total number of rows.
     *
     * @throws ezcQueryInvalidException if offset is used and orderBy is not.
     * @return string
     */
    public function getQuery()
    {
        $query = parent::getQuery();
        if ( $this->hasLimit )
        {
            if ( $this->offset) 
            {
                if ( !$this->orderString ) 
                {
                    // Uh ow. We need some columns to sort in the oposite order to make this work
                    throw new ezcQueryInvalidException( "LIMIT workaround for MS SQL", "orderBy() was not called before getQuery()." );
                }
                return 'SELECT * FROM ( SELECT TOP ' . $this->limit . ' * FROM ( ' . self::top( $this->offset + $this->limit, $query ) . ' ) AS ezcDummyTable1 ' . $this->invertedOrderString . ' ) AS ezcDummyTable2 ' . $this->orderString;
            }
            return self::top( $this->limit, $query );
        }
        return $query;
    }
}

?>
