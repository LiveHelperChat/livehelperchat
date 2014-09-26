<?php
/**
 * File containing the ezcFeedUnsupportedModuleException class.
 *
 * @package Feed
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Thrown when an unsupported module is created.
 *
 * @package Feed
 * @version 1.3
 */
class ezcFeedUnsupportedModuleException extends ezcFeedException
{
    /**
     * Constructs a new ezcFeedUnsupportedModuleException.
     *
     * @param string $module The module name
     */
    public function __construct( $module )
    {
        parent::__construct( "The module '{$module}' is not supported." );
    }
}
?>
