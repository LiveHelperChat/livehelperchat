<?php
/**
 * File containing the ezcPersistentObjectSchemaTemplateFunctions class.
 *
 * @package PersistentObjectDatabaseSchemaTiein
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Custom template functions for {@link ezcDbSchemaPersistentClassWriter}.
 * 
 * @package PersistentObjectDatabaseSchemaTiein
 * @version 1.3
 * @access private
 */
class ezcPersistentObjectSchemaTemplateFunctions implements ezcTemplateCustomFunction
{
    /**
     * Registers custom template functions available in this class.
     *
     * Returns the {@link ezcTemplateCustomFunctionDefinition} for template
     * function with $name, if defined in this class. Otherwise returns false.
     * 
     * @param string $name 
     * @return ezcTemplateCustomFunctionDefinition|false
     */
    public static function getCustomFunctionDefinition( $name )
    {
        switch ( $name )
        {
            case 'underScoreToCamelCase':
                $def = new ezcTemplateCustomFunctionDefinition();
                $def->class = __CLASS__;
                $def->method = 'underScoreToCamelCase';
                $def->parameters = array( 'name', '[firstLower]' );
                return $def;
        }
        return false;
    }

    /**
     * Convert '_' delimited table/column names to CamelCase.
     *
     * Takes a '_' delimited table/column $name and returns it converted to
     * CamelCase. For example "my_cool_table_name" is converted to
     * "MyCoolTableName". If $firstLower is set to true, the first character of
     * the returned string will be made lower case (for property names).
     *  
     * @param string $name 
     * @param bool $firstLower
     * @return string
     */
    public static function underScoreToCamelCase( $name, $firstLower = false )
    {
        if ( $name == '' )
        {
            return $name;
        }

        $name = implode(
            '',
            array_map(
                'ucfirst',
                explode( '_', $name )
            )
        );

        if ( $firstLower )
        {
            $name = strtolower( $name[0] ) . substr( $name, 1 );
        }
        return $name;
    }
}

?>
