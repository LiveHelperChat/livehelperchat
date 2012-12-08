<?php
/**
 * File containing the ezcTreeInvalidXmlException class.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.4
 * @filesource
 * @package Tree
 */

/**
 * Exception that is thrown when an XML tree document is not well-formed.
 *
 * @package Tree
 * @version 1.1.4
 */
class ezcTreeInvalidXmlException extends ezcTreeException
{
    /**
     * Constructs a new ezcTreeInvalidXmlException.
     *
     * @param string $xmlFile
     * @param array $errors
     */
    public function __construct( $xmlFile, array $errors )
    {
        $message = '';
        foreach ( $errors as $error )
        {
            $message .= sprintf( "%s:%d:%d: %s\n", $error->file, $error->line, $error->column, trim( $error->message ) );
        }
        parent::__construct( "The XML file '$xmlFile' is not well-formed:\n". $message );
    }
}
?>
