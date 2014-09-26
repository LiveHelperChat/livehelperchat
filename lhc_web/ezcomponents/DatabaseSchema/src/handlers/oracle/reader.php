<?php
/**
 * File containing the ezcDbSchemaOracleReader class.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Handler for Oracle connections representing a DB schema.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaOracleReader extends ezcDbSchemaCommonSqlReader implements ezcDbSchemaDbReader
{
    /**
     * Contains a type map from Oracle native types to generic DbSchema types.
     *
     * @var array
     */
    static private $typeMap = array(
        'NUMBER'    => 'integer', // or 'decimal' in case precision and scale available
        'FLOAT'     => 'float',
        'CHAR'      => 'text', // or 'boolean' for char(1)
        'VARCHAR'   => 'text',
        'VARCHAR2'  => 'text',
        'DATE'      => 'date',        
        'BLOB'      => 'blob',
        'CLOB'      => 'clob',       
        'TIMESTAMP' => 'timestamp',
        'TIMESTAMP(6)' => 'timestamp'
    );

    /**
     * Loops over all the tables in the database and extracts schema information.
     *
     * This method extracts information about a database's schema from the
     * database itself and returns this schema as an ezcDbSchema object.
     *
     * @return ezcDbSchema
     */
    protected function fetchSchema()
    {
        $tables = $this->db->query( "SELECT table_name FROM user_tables ORDER BY table_name" )->fetchAll();
        return $this->processSchema( $tables );
    }

    /**
     * Fetch fields definition for the table $tableName
     *
     * This method loops over all the fields in the table $tableName and
     * returns an array with the field specification. The key in the returned
     * array is the name of the field.
     *
     * @param string $tableName
     * @return array(string=>ezcDbSchemaField)
     */
    protected function fetchTableFields( $tableName )
    {
        $fields = array();

        // will detect autoincrement field by presence of sequence tableName_fieldPos_seq
        $sequencesQuery = $this->db->query( "SELECT * FROM user_sequences" );   
        $sequencesQuery->setFetchMode( PDO::FETCH_ASSOC );
        $sequences = array();
        foreach ( $sequencesQuery as $seq )
        {
            $sequences[] = $seq['sequence_name'];
        }

        // fetching fields info from Oracle
        $resultArray = $this->db->query( "SELECT   a.column_name AS field, " .    
                                         "         a.column_id AS field_pos, " .
                                         "         DECODE (a.nullable, 'N', 1, 'Y', 0) AS notnull, " .
                                         "         a.data_type AS type, " .
                                         "         a.data_length AS length, " .
                                         "         a.data_precision AS precision, " .
                                         "         a.data_scale AS scale, " .
                                         "         a.data_default AS default_val " .
                                         "FROM     user_tab_columns a ".
                                         "WHERE    a.table_name = '$tableName' " .
                                         "ORDER BY a.column_id" );


        $resultArray->setFetchMode( PDO::FETCH_ASSOC );

        foreach ( $resultArray as $row )
        {
            $fieldLength = $row['length'];
            $fieldPrecision = null;
            $fieldType = self::convertToGenericType( $row['type'], $fieldLength, $fieldPrecision );
            if ( in_array( $fieldType, array( 'clob', 'blob', 'date', 'float', 'timestamp' ) ) )
            {
                    $fieldLength = false;
            }
            else if ( $fieldType == 'integer' )
            {
                if ( $row['precision']!= '' )
                {
                    $fieldType = 'decimal';
                    $fieldLength = $row['precision'];
                } 
                else if ( $fieldLength == 22 ) // 22 is the default length for NUMBER in Oracle, so don't include length
                {
                    $fieldLength = false;
                }
            }

            $fieldNotNull = $row['notnull'];

            $fieldDefault = null;

            if ( $row['default_val'] != '' ) 
            {
                $row['default_val'] = rtrim( $row['default_val'] );
                if ( $fieldType == 'boolean' )
                {
                    ( $row['default_val'] == '1' ) ? $fieldDefault = 'true': $fieldDefault = 'false';
                } 
                else if ( $fieldType == 'text' ) 
                {
                    $fieldDefault = substr( $row['default_val'], 1, -1 ); // avoid quotes for text
                }
                else 
                {
                    $fieldDefault = $row['default_val']; // have a number value
                }
            }

            $fieldAutoIncrement = false;           
            // new sequence naming included
            if ( in_array( 
                    ezcDbSchemaOracleHelper::generateSuffixCompositeIdentName( $tableName, $row['field_pos'], 'seq' ),
                    $sequences
                 ) || in_array(
                    ezcDbSchemaOracleHelper::generateSuffixCompositeIdentName( $tableName, $row['field'], 'seq' ),
                    $sequences
                 ) )
            {
                $fieldAutoIncrement = true;
            }

            // FIXME: unsigned needs to be implemented
            $fieldUnsigned = false;
            $fields[$row['field']] = ezcDbSchema::createNewField( $fieldType, $fieldLength, $fieldNotNull, $fieldDefault, $fieldAutoIncrement, $fieldUnsigned );
        }

        return $fields;
    }

    /**
     * Converts the native Oracle type in $typeString to a generic DbSchema type.
     *
     * This method converts a string like "VARCHAR2" to the generic DbSchema
     * type and uses parameters $typeLength and $typePrecision
     * to detect emulated types char(1) == 'boolean', number(N) == "decimal".
     *
     * @param string  $typeString
     * @param int     $typeLength
     * @param int     $typePrecision
     * @return string
     */
    static function convertToGenericType( $typeString, &$typeLength, $typePrecision )
    {
        if ( $typeString == 'NUMBER' && $typePrecision != '' )
        {
            $genericType = 'decimal';
        }
        else if ( $typeString == 'CHAR' && $typeLength == 1 )
        {
            $genericType = 'boolean';
            $typeLength = 0;
        }
        else 
        {
            if ( !isset( self::$typeMap[$typeString] ) )
            {
                throw new ezcDbSchemaUnsupportedTypeException( 'Oracle', $typeString );
            }
            $genericType = self::$typeMap[$typeString];
        }

        return $genericType;
    }

    /**
     * Returns whether the type $type is a numeric type
     *
     * @param string $type
     * @return bool
     */
    private function isNumericType( $type )
    {
        $types = array( 'float', 'int', 'NUMBER' );
        return in_array( $type, $types );
    }

    /**
     * Returns whether the type $type is a string type
     *
     * @param string $type
     * @return bool
     */
    private function isStringType( $type )
    {
        $types = array( 'VARCHAR', 'VARCHAR2', 'text', 'CHAR' );
        return in_array( $type, $types );
    }

    /**
     * Returns whether the type $type is a blob type
     *
     * @param string $type
     * @return bool
     */
    private function isBlobType( $type )
    {
        $types = array( 'blob' );
        return in_array( $type, $types );
    }


    /**
     * Loops over all the indexes in the table $table and extracts information.
     *
     * This method extracts information about the table $tableName's indexes
     * from the database and returns this schema as an array of
     * ezcDbSchemaIndex objects. The key in the array is the index' name.
     *
     * @param  string $tableName
     * @return array(string=>ezcDbSchemaIndex)
     */
    protected function fetchTableIndexes( $tableName )
    {
        $indexBuffer = array();
        $indexesArray = array();

        // fetching index info from Oracle
        $getIndexSQL = "SELECT uind.index_name AS name, " .
                 "       uind.index_type AS type, " .
                 "       decode( uind.uniqueness, 'NONUNIQUE', 0, 'UNIQUE', 1 ) AS is_unique, " .
                 "       uind_col.column_name AS column_name, " .
                 "       uind_col.column_position AS column_pos " .
                 "FROM user_indexes uind, user_ind_columns uind_col " .
                 "WHERE uind.index_name = uind_col.index_name AND uind_col.table_name = '$tableName'";

        $indexesArray = $this->db->query( $getIndexSQL )->fetchAll();

        $primaryFound = false;

        // getting columns to which each index related.
        foreach ( $indexesArray as $row )
        {
            $keyName = $row['name'];

            if ( $keyName == $tableName.'_pkey' ) 
            {
                $keyName = 'primary';
                $indexBuffer[$keyName]['primary'] = true;
                $indexBuffer[$keyName]['unique'] = true;
                $primaryFound = true;
            }
            else
            {
                $indexBuffer[$keyName]['primary'] = false;
                $indexBuffer[$keyName]['unique'] = ( $row['is_unique'] == 1 ) ? true : false;
            }
            $indexBuffer[$keyName]['fields'][$row['column_name']] = ezcDbSchema::createNewIndexField();

        }

        $indexes = array();

        foreach ( $indexBuffer as $indexName => $indexInfo )
        {
            $indexes[$indexName] = ezcDbSchema::createNewIndex( $indexInfo['fields'], $indexInfo['primary'], $indexInfo['unique'] );
        }

        return $indexes;
    }
}
?>
