<?php
/**
 * File containing the ezcDbSchemaOracleWriter class.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Handler for storing database schemas and applying differences that uses Oracle as backend.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaOracleHelper
{
    /**
     * Constant for the maximum identifier length.
     *
     * @var int
     */
    const IDENTIFIER_MAX_LENGTH = 30;

    /**
     * Generate composite identifier name for sequence or triggers and looking for oracle 30 chars ident restriction.
     * 
     * @param string $tableName
     * @param string $fieldName
     * @param string $suffix
     * @return string
     */
    public static function generateSuffixCompositeIdentName( $tableName, $fieldName, $suffix )
    {
        return self::generateSuffixedIdentName( array( $tableName, $fieldName ), $suffix );
    }

    /**
     * Generate single identifier name for constraints for example obeying oracle 30 chars ident restriction.
     * 
     * @param array $identNames
     * @param string $suffix
     * @return string
     */
    public static function generateSuffixedIdentName( array $identNames, $suffix )
    {
        $ident = implode( "_", $identNames ) . "_" . $suffix;
        $i = 0;
        $last = -1;

        while ( strlen( $ident ) > self::IDENTIFIER_MAX_LENGTH )
        {
            if ( strlen( $identNames[$i] ) > 1 || $last == $i )
            {
                $identNames[$i] = substr( $identNames[$i], 0, strlen( $identNames[$i] ) - 1 );
                $last = $i;
            }
            $i = ( $i + 1 ) % count( $identNames );
            $ident = implode( "_", $identNames ) . "_" . $suffix;
        }
        return $ident;
    }
}
?>
