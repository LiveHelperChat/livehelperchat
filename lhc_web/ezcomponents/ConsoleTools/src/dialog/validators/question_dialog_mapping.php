<?php
/**
 * File containing the ezcConsoleQuestionDialogMappingValidator class.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Validator class to map certain results to others.
 * This validator class, for {@link ezcConsoleQuestionDialog} objects,
 * validates a given result against a set of predefined values, exactly like
 * {@link ezcConsoleQuestionDialogCollectionValidator} does, but allows in
 * addition to map certain results to other results. The $map property contains
 * an array of mappings that are checked before a received result is validated.
 * If a mapping matches, the received result is converted to the mapping target
 * before being validated.
 *
 * A valid $map looks like this:
 * <code>
 *      array(
 *          'yes' => 'y',
 *          'no'  => 'n',
 *          '1'   => 'y',
 *          '0'   => 'n'
 *      )
 * </code>
 * While the corresponding collection of valid answers would look like
 * this:
 * <code>
 *      array(
 *          'y', 'n'
 *      )
 * </code>
 * If the answer 'yes' is received by the validator, it is mapped to 'y',
 * therefore considered valid and 'y' is returned as the received value.
 * 
 * @package ConsoleTools
 * @version 1.6.1
 *
 * @property array(string) $collection
 *           The collection of valid answers.
 * @property mixed $default
 *           Default value.
 * @property int $conversion
 *           ezcConsoleDialogValidator::CONVERT_NONE (default) or
 *           ezcConsoleDialogValidator::CONVERT_LOWER or
 *           ezcConsoleDialogValidator::CONVERT_UPPER.
 * @property array(string=>string) $map
 *           Mapping of answers to valid answers (e.g. array('yes' => 'y') to
 *           map 'yes' to 'y' while 'y' must be in $collection).
 */
class ezcConsoleQuestionDialogMappingValidator extends ezcConsoleQuestionDialogCollectionValidator
{
    /**
     * Creates a new question dialog mapping validator. 
     * Creates a new question dialog mapping validator, which validates the
     * result specified by the user against an array of valid results
     * ($collection). If not value is provided by the user a possibly set
     * $default value is used instead. The $conversion parameter can optionally
     * define a conversion to be performed on the result before validating it.
     * Valid conversions are defined by the CONVERT_* constants in this class.
     *
     * While this functionality is already provided by {@link
     * ezcConsoleQuestionDialogCollectionValidator}, the additional $map
     * paramater allows the sepcification of a map of result values. These
     * mapping is then checked for matches, before a received answer is
     * validated against the collection.
     *
     * @param array(string) $collection The collection of valid results.
     * @param mixed $default    Optional default value.
     * @param int $conversion   CONVERT_* constant.
     * @param array(string=>string) $map
     * @return void
     */
    public function __construct( array $collection, $default = null, $conversion = self::CONVERT_NONE, array $map = array() )
    {
        // Initialize additional property
        $this->properties['map'] = $map;

        parent::__construct( $collection, $default, $conversion );
    }

    /**
     * Returns a fixed version of the result, if possible.
     * Converts the given result according to the conversion defined in the
     * $conversion property.
     * 
     * @param mixed $result The received result.
     * @return mixed The manipulated result.
     */
    public function fixup( $result )
    {
        if ( $result === "" && $this->default !== null )
        {
            return $this->default;
        }
        switch ( $this->conversion )
        {
            case self::CONVERT_UPPER:
                $result = strtoupper( $result );
                break;
            case self::CONVERT_LOWER:
                $result = strtolower( $result );
                break;
        }
        return ( isset( $this->map[$result] ) ? $this->map[$result] : $result );
    }
    
    /**
     * Property write access.
     * 
     * @param string $propertyName Name of the property.
     * @param mixed $propertyValue The value for the property.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If a the value for the property options is not an instance of
     * @throws ezcBaseValueException
     *         If a the value for a property is out of range.
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case "map":
                if ( is_array( $propertyValue ) === false )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, "array" );
                }
                break;
            default:
                return parent::__set( $propertyName, $propertyValue );
        }
        $this->properties[$propertyName] = $propertyValue;
    }
}

?>
