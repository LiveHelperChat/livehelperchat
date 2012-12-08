<?php
/**
 * File containing the ezcWebdavPluginPreconditionFailedException class.
 * 
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Exception thrown if a plugin precondition failed.
 *
 * This exception is thrown if a plugin should be used in {@link
 * ezcWebdavServer}, which find one of its preconditions not fulfilled. All
 * preconditions for a plugin should be checked during intialization phase in
 * {@link eczWebdavPluginConfiguration::init()}.
 * 
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavPluginPreconditionFailedException extends ezcWebdavException
{
    /**
     * Creates a new exception.
     *
     * Creates a new exception for the plugin with $pluginNamespace for which a
     * precondition failed due to $reason.
     * 
     * @param string $pluginNamespace 
     * @param string $reason 
     */
    public function __construct( $pluginNamespace, $reason )
    {
        parent::__construct(
            "Precondition for plugin '$pluginNamespace' failed: $reason"
        );
    }
}

?>
