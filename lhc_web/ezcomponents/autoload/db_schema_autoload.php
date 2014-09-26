<?php
/**
 * Autoloader definition for the DatabaseSchema component.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.4.4
 * @filesource
 * @package DatabaseSchema
 */

return array(
    'ezcDbSchemaException'                       => 'DatabaseSchema/exceptions/exception.php',
    'ezcDbSchemaDropAllColumnsException'         => 'DatabaseSchema/exceptions/drop_all_columns_exception.php',
    'ezcDbSchemaInvalidDiffReaderClassException' => 'DatabaseSchema/exceptions/invalid_diff_reader_class.php',
    'ezcDbSchemaInvalidDiffWriterClassException' => 'DatabaseSchema/exceptions/invalid_diff_writer_class.php',
    'ezcDbSchemaInvalidReaderClassException'     => 'DatabaseSchema/exceptions/invalid_reader_class.php',
    'ezcDbSchemaInvalidSchemaException'          => 'DatabaseSchema/exceptions/invalid_schema.php',
    'ezcDbSchemaInvalidWriterClassException'     => 'DatabaseSchema/exceptions/invalid_writer_class.php',
    'ezcDbSchemaSqliteDropFieldException'        => 'DatabaseSchema/exceptions/sqlite_drop_field_exception.php',
    'ezcDbSchemaUnknownFormatException'          => 'DatabaseSchema/exceptions/unknown_format.php',
    'ezcDbSchemaUnsupportedTypeException'        => 'DatabaseSchema/exceptions/unsupported_type.php',
    'ezcDbSchemaDiffWriter'                      => 'DatabaseSchema/interfaces/schema_diff_writer.php',
    'ezcDbSchemaReader'                          => 'DatabaseSchema/interfaces/schema_reader.php',
    'ezcDbSchemaWriter'                          => 'DatabaseSchema/interfaces/schema_writer.php',
    'ezcDbSchemaDbReader'                        => 'DatabaseSchema/interfaces/db_reader.php',
    'ezcDbSchemaDbWriter'                        => 'DatabaseSchema/interfaces/db_writer.php',
    'ezcDbSchemaDiffDbWriter'                    => 'DatabaseSchema/interfaces/db_diff_writer.php',
    'ezcDbSchemaDiffReader'                      => 'DatabaseSchema/interfaces/schema_diff_reader.php',
    'ezcDbSchemaCommonSqlReader'                 => 'DatabaseSchema/handlers/common_sql_reader.php',
    'ezcDbSchemaCommonSqlWriter'                 => 'DatabaseSchema/handlers/common_sql_writer.php',
    'ezcDbSchemaDiffFileReader'                  => 'DatabaseSchema/interfaces/file_diff_reader.php',
    'ezcDbSchemaDiffFileWriter'                  => 'DatabaseSchema/interfaces/file_diff_writer.php',
    'ezcDbSchemaFileReader'                      => 'DatabaseSchema/interfaces/file_reader.php',
    'ezcDbSchemaFileWriter'                      => 'DatabaseSchema/interfaces/file_writer.php',
    'XMLWriter'                                  => 'DatabaseSchema/handlers/xml/xmlwritersubstitute.php',
    'ezcDbSchema'                                => 'DatabaseSchema/schema.php',
    'ezcDbSchemaAutoIncrementIndexValidator'     => 'DatabaseSchema/validators/auto_increment_index.php',
    'ezcDbSchemaComparator'                      => 'DatabaseSchema/comparator.php',
    'ezcDbSchemaDiff'                            => 'DatabaseSchema/schema_diff.php',
    'ezcDbSchemaField'                           => 'DatabaseSchema/structs/field.php',
    'ezcDbSchemaHandlerDataTransfer'             => 'DatabaseSchema/handlers/data_transfer.php',
    'ezcDbSchemaHandlerManager'                  => 'DatabaseSchema/handler_manager.php',
    'ezcDbSchemaIndex'                           => 'DatabaseSchema/structs/index.php',
    'ezcDbSchemaIndexField'                      => 'DatabaseSchema/structs/index_field.php',
    'ezcDbSchemaIndexFieldsValidator'            => 'DatabaseSchema/validators/index_fields.php',
    'ezcDbSchemaMysqlReader'                     => 'DatabaseSchema/handlers/mysql/reader.php',
    'ezcDbSchemaMysqlWriter'                     => 'DatabaseSchema/handlers/mysql/writer.php',
    'ezcDbSchemaOptions'                         => 'DatabaseSchema/options/schema.php',
    'ezcDbSchemaOracleHelper'                    => 'DatabaseSchema/handlers/oracle/helper.php',
    'ezcDbSchemaOracleReader'                    => 'DatabaseSchema/handlers/oracle/reader.php',
    'ezcDbSchemaOracleWriter'                    => 'DatabaseSchema/handlers/oracle/writer.php',
    'ezcDbSchemaPersistentClassWriter'           => 'DatabaseSchema/handlers/persistent/class_writer.php',
    'ezcDbSchemaPersistentWriter'                => 'DatabaseSchema/handlers/persistent/writer.php',
    'ezcDbSchemaPgsqlReader'                     => 'DatabaseSchema/handlers/pgsql/reader.php',
    'ezcDbSchemaPgsqlWriter'                     => 'DatabaseSchema/handlers/pgsql/writer.php',
    'ezcDbSchemaPhpArrayReader'                  => 'DatabaseSchema/handlers/php_array/reader.php',
    'ezcDbSchemaPhpArrayWriter'                  => 'DatabaseSchema/handlers/php_array/writer.php',
    'ezcDbSchemaSqliteReader'                    => 'DatabaseSchema/handlers/sqlite/reader.php',
    'ezcDbSchemaSqliteWriter'                    => 'DatabaseSchema/handlers/sqlite/writer.php',
    'ezcDbSchemaTable'                           => 'DatabaseSchema/structs/table.php',
    'ezcDbSchemaTableDiff'                       => 'DatabaseSchema/structs/table_diff.php',
    'ezcDbSchemaTypesValidator'                  => 'DatabaseSchema/validators/types.php',
    'ezcDbSchemaUniqueIndexNameValidator'        => 'DatabaseSchema/validators/unique_index_name.php',
    'ezcDbSchemaValidator'                       => 'DatabaseSchema/validator.php',
    'ezcDbSchemaXmlReader'                       => 'DatabaseSchema/handlers/xml/reader.php',
    'ezcDbSchemaXmlWriter'                       => 'DatabaseSchema/handlers/xml/writer.php',
);
?>
