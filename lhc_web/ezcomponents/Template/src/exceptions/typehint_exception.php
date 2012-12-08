<?php
/**
 * File containing the ezcTemplatTypeHintException class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception for problems in parser element code.
 *
 * Instantiate the exception with one of the class constants, e.g.:
 * <code>
 * throw new ezcTemplateTstNodeException( ezcTemplateTstNodeException::NO_FIRST_CHILD );
 * </code>
 *
 * @package Template
 * @version 1.4.2
 */
class ezcTemplateTypeHintException extends ezcTemplateException
{
    /**
     * This exception should be caught, and rethrown. The message is not important.
     */
    public function __construct()
    {
        $message = "Typehint failure";
        parent::__construct( $message );
    }
}
?>
