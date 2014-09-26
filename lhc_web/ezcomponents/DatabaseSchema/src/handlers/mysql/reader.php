<?php
/**
 * File containing the ezcDbSchemaMysqlReader class.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Handler for files containing PHP arrays that represent DB schema.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaMysqlReader extends ezcDbSchemaCommonSqlReader implements ezcDbSchemaDbReader
{
    /**
     * Contains a type map from MySQL native types to generic DbSchema types.
     *
     * @var array
     */
    static private $typeMap = array(
        'bit' => 'integer',
        'tinyint' => 'integer',
        'smallint' => 'integer',
        'mediumint' => 'integer',
        'int' =>  'integer',
        'bigint' => 'integer',
        'integer' => 'integer',
        'bool' => 'boolean',
        'boolean' => 'boolean',
        'float' => 'float',
        'double' => 'float',
        'dec' => 'decimal',
        'decimal' => 'decimal',
        'numeric' => 'decimal',
        'fixed' => 'decimal',
        
        'date' => 'date',
        'datetime' => 'timestamp',
        'timestamp' => 'timestamp',
        'time' => 'time',
        'year' => 'integer',
       
        'char' => 'text',
        'varchar' => 'text',
        'binary' => 'blob',
        'varbinary' => 'blob',
        'tinyblob' => 'blob',
        'blob' => 'blob',
        'mediumblob' => 'blob',
        'longblob' => 'blob',
        'tinytext' => 'clob',
        'text' => 'clob',
        'mediumtext' => 'clob',
        'longtext' => 'clob',
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
        $tables = $this->db->query( "SHOW TABLES" )->fetchAll();
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

        $resultArray = $this->db->query( "DESCRIBE `$tableName`" );
        $resultArray->setFetchMode( PDO::FETCH_ASSOC );

        foreach ( $resultArray as $row )
        {
            $fieldLength = false;

            // bool and boolean is synonyms for TINYINT(1) in MySQL
            if ( $row['type'] == 'tinyint(1)' ) 
            {
                $fieldType = 'boolean';
            }
            else
            {
                $fieldType = self::convertToGenericType( $row['type'], $fieldLength, $fieldPrecision );
                if ( !$fieldLength )
                {
                    $fieldLength = false;
                }
            }

            $fieldNotNull = false;
            if ( strlen( $row['null'] ) == 0 || $row['null'][0] != 'Y' || $fieldType == 'timestamp' )
            {
                $fieldNotNull = true;
            }

            $fieldDefault = null;

            if ( strlen( $row['default'] ) != 0 )
            {
                if ( $fieldType == 'boolean' )
                {
                    $fieldDefault = ( $row['default'] == '0' ) ? 'false' : 'true';
                }
                else if ( $fieldType != 'timestamp' ) 
                {
                    $fieldDefault = $row['default'];
                }
            }
            if ( $fieldType == 'integer' && $row['default'] !== null )
            {
                $fieldDefault = (int) $fieldDefault;
            }

            $fieldAutoIncrement = false;
            if ( strstr ( $row['extra'], 'auto_increment' ) !== false )
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
     * Converts the native MySQL type in $typeString to a generic DbSchema type.
     *
     * This method converts a string like "float(5,10)" to the generic DbSchema
     * type and uses the by-reference parameters $typeLength and $typePrecision
     * to communicate the optional length and precision of the field's type.
     *
     * @param string  $typeString
     * @param int    &$typeLength
     * @param int    &$typePrecision
     * @return string
     */
    static function convertToGenericType( $typeString, &$typeLength, &$typePrecision )
    {
        preg_match( "@([a-z]*)(\((\d*)(,(\d+))?\))?@", $typeString, $matches );
        if ( !isset( self::$typeMap[$matches[1]] ) )
        {
            throw new ezcDbSchemaUnsupportedTypeException( 'MySQL', $matches[1] );
        }
        $genericType = self::$typeMap[$matches[1]];

        if ( in_array( $genericType, array( 'text', 'decimal', 'float', 'integer' ) ) && isset( $matches[3] ) && $typeString != 'bigint(20)' )
        {
            $typeLength = $matches[3];
            if ( is_numeric( $typeLength ) )
            {
                $typeLength = (int) $typeLength;
            }
        }
        if ( in_array( $genericType, array( 'decimal', 'float' ) ) && isset( $matches[5] ) )
        {
            $typePrecision = $matches[5];
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
        $types = array( 'float', 'int' );
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
        $types = array( 'tinytext', 'text', 'mediumtext', 'longtext' );
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
        $types = array( 'varchar', 'char' );
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

        $resultArray = $this->db->query( "SHOW INDEX FROM `$tableName`" );
        
        foreach ( $resultArray as $row )
        {
            $keyName = $row['key_name'];
            if ( $keyName == 'PRIMARY' )
            {
                $keyName = 'primary';
            }

            $indexBuffer[$keyName]['primary'] = false;
            $indexBuffer[$keyName]['unique'] = true;

            if ( $keyName == 'primary' )
            {
                $indexBuffer[$keyName]['primary'] = true;
                $indexBuffer[$keyName]['unique'] = true;
            }
            else
            {
                $indexBuffer[$keyName]['unique'] = $row['non_unique'] ? false : true;
            }

            $indexBuffer[$keyName]['fields'][$row['column_name']] = ezcDbSchema::createNewIndexField();

//            if ( $row['sub_part'] )
//            {
//                $indexBuffer[$keyName]['options']['limitations'][$row['column_name']] = $row['sub_part'];
//            }
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
