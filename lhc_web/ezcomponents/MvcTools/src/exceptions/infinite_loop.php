<?php
/**
 * File containing the ezcMvcInfiniteLoopException class.
 *
 * @package MvcTools
 * @version 1.1.3
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This exception is thrown when there are more internal redirects than allowed.
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcInfiniteLoopException extends ezcMvcToolsException
{
    /**
     * Constructs an ezcMvcInfiniteLoopException
     *
     * @param int $redirectCount
     */
    public function __construct( $redirectCount )
    {
        $message = "{$redirectCount} redirects have occurred, there is a possible infinite redirect loop.";
        parent::__construct( $message );
    }
}
?>
