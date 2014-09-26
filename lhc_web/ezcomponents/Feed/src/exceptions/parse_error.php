<?php
/**
 * File containing the ezcFeedParseErrorException class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Thrown when a feed can not be parsed at all.
 *
 * @package Feed
 * @version 1.3
 */
class ezcFeedParseErrorException extends ezcFeedException
{
    /**
     * Constructs a new ezcFeedParseErrorException.
     *
     * If $uri is not null the generated message will contain it.
     *
     * @param string $uri The URI which identifies the XML document which was tried to be parsed
     * @param string $message An extra message to be included in the thrown exception text
     */
    public function __construct( $uri = null, $message )
    {
        if ( $uri !== null )
        {
            parent::__construct( "Parse error while parsing feed '{$uri}': {$message}." );
        }
        else
        {
            parent::__construct( "Parse error while parsing feed: {$message}." );
        }
    }
}
?>
