<?php
/**
 * File containing the ezcDbSchemaValidator class.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcDbSchemaValidator validates schemas for correctness.
 *
 * Example:
 * <code>
 * <?php
 * $xmlSchema = ezcDbSchema::createFromFile( 'xml', 'wanted-schema.xml' );
 * $messages = ezcDbSchemaValidator::validate( $xmlSchema );
 * foreach ( $messages as $message )
 * {
 *     echo $message, "\n";
 * }
 * ?>
 * </code>
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @mainclass
 */
class ezcDbSchemaValidator
{
    /**
     * An array containing all the different classes that implement validation methods.
     *
     * The array contains the classnames that implement validators. The
     * validation classes all should implement a method called "validate()"
     * which accepts an ezcDbSchema object.
     */
    static private $validators = array(
        'ezcDbSchemaTypesValidator',
        'ezcDbSchemaIndexFieldsValidator',
        'ezcDbSchemaAutoIncrementIndexValidator',
        'ezcDbSchemaUniqueIndexNameValidator',
    );

    /**
     * Validates the ezcDbSchema object $schema with the recorded validator classes.
     *
     * This method loops over all the known validator classes and calls their
     * validate() method with the $schema as argument. It returns an array
     * containing validation errors as strings.
     * 
     * @todo implement from an interface
     *
     * @param ezcDbSchema $schema
     * @return array(string)
     */
    static public function validate( ezcDbSchema $schema )
    {
        $validationErrors = array();
        
        foreach ( self::$validators as $validatorClass )
        {
            $errors = call_user_func( array( $validatorClass, 'validate' ), $schema );
            foreach ( $errors as $error )
            {
                $validationErrors[] = $error;
            }
        }
        return $validationErrors;
    }
}
?>
