<?php
/**
 * File containing the ezcFeedUnsupportedTypeException class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Thrown when an unsupported feed is created.
 *
 * @package Feed
 * @version 1.3
 */
class ezcFeedUnsupportedTypeException extends ezcFeedException
{
    /**
     * Constructs a new ezcFeedUnsupportedTypeException.
     *
     * @param string $type The feed type which caused the exception
     */
    public function __construct( $type )
    {
        parent::__construct( "The feed type '{$type}' is not supported." );
    }
}
?>
