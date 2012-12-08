<?php
/**
 * File containing the ezcWorkflowInvalidInputException class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This exception will be thrown when an error occurs
 * during input validation in an input node.
 *
 * @property-read array $errors The input validation error(s).
 *
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowInvalidInputException extends ezcWorkflowExecutionException
{
    /**
     * Container to hold the properties
     *
     * @var array(string=>mixed)
     */
    protected $properties = array(
      'errors' => array(),
    );

    /**
     * Constructor.
     *
     * @param array $message
     */
    public function __construct( $message )
    {
        $this->properties['errors'] = $message;

        $messages = array();

        foreach ( $message as $variable => $condition )
        {
            $messages[] = $variable . ' ' . $condition;
        }

        parent::__construct( join( "\n", $messages ) );
    }

    /**
     * Property read access.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the the desired property is not found.
     *
     * @param string $propertyName Name of the property.
     * @return mixed Value of the property or null.
     * @ignore
     */
    public function __get( $propertyName )
    {
        switch ( $propertyName )
        {
            case 'errors':
                return $this->properties[$propertyName];
        }

        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

    /**
     * Property write access.
     *
     * @param string $propertyName Name of the property.
     * @param mixed $val  The value for the property.
     *
     * @throws ezcBasePropertyPermissionException
     *         If there is a write access to errors.
     * @ignore
     */
    public function __set( $propertyName, $val )
    {
        switch ( $propertyName )
        {
            case 'errors':
                throw new ezcBasePropertyPermissionException( $propertyName, ezcBasePropertyPermissionException::WRITE );
        }

        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

    /**
     * Property isset access.
     *
     * @param string $propertyName Name of the property.
     * @return bool True is the property is set, otherwise false.
     * @ignore
     */
    public function __isset( $propertyName )
    {
        switch ( $propertyName )
        {
            case 'errors':
                return true;
        }

        return false;
    }
}
?>
