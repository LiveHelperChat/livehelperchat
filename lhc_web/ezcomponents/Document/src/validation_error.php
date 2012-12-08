<?php
/**
 * File containing the ezcDocumentValidationError class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Unifies different errors into a single structure for all kinds of validation
 * errors. The validation error message can be fetched using the __toString()
 * method, while the original error is still be available, fi required.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentValidationError
{
    /**
     * Original error object
     *
     * @var mixed
     */
    protected $error;

    /**
     * Transformed error message.
     *
     * @var string
     */
    protected $message;

    /**
     * textual mapping for libxml error types.
     *
     * @var array
     */
    protected static $errorTypes = array(
        LIBXML_ERR_WARNING => 'Warning',
        LIBXML_ERR_ERROR   => 'Error',
        LIBXML_ERR_FATAL   => 'Fatal error',
    );

    /**
     * Create validation error object
     *
     * @param string $message
     * @param mixed $error
     * @return void
     */
    protected function __construct( $message, $error = null )
    {
        $this->message = $message;
        $this->error   = $error;
    }

    /**
     * Get original error object
     *
     * @return mixed
     */
    public function getOriginalError()
    {
        return $this->error;
    }

    /**
     * Convert libXML error to string
     *
     * @return void
     */
    public function __toString()
    {
        return $this->message;
    }

    /**
     * Create from LibXmlError
     *
     * Create a validation error object from a LibXmlError error object.
     *
     * @param LibXMLError $error
     * @return ezcDocumentValidationError
     */
    public static function createFromLibXmlError( LibXMLError $error )
    {
        return new ezcDocumentValidationError(
            sprintf( "%s in %d:%d: %s.",
                self::$errorTypes[$error->level],
                $error->line,
                $error->column,
                trim( $error->message )
            ),
            $error
        );
    }

    /**
     * Create validation error from Exception
     *
     * @param Exception $e
     * @return ezcDocumentValidationError
     */
    public static function createFromException( Exception $e )
    {
        return new ezcDocumentValidationError(
            $e->getMessage(),
            $e
        );
    }
}

?>
