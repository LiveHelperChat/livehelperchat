<?php
/**
 * File containing the ezcTemplateTstNodeException class
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
 * @access private
 */
class ezcTemplateTstNodeException extends ezcTemplateException
{
    /**
     * Element has no children, cannot get first child.
     */
    const NO_FIRST_CHILD = 1;

    /**
     * Element has no children, cannot get last child.
     */
    const NO_LAST_CHILD = 2;

    /**
     * Initialises the exception with the type of error which automatically generates an exception message.
     *
     * @param int $type The type of element error.
     * @param string $comment Optional comment for the error, depends on $type.
     */
    public function __construct( $type )
    {
        switch ( $type )
        {
            case self::NO_FIRST_CHILD:
                $message = "Element has no children, cannot get first child.";
                break;
            case self::NO_LAST_CHILD:
                $message = "Element has no children, cannot get last child.";
                break;
        }
        parent::__construct( $message );
    }
}
?>
