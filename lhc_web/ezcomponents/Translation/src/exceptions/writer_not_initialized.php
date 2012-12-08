<?php
/**
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.3.2
 * @filesource
 * @package Translation
 */

/**
 * Thrown when methods are called that require a ContextWriter to be
 * initialized.
 *
 * @package Translation
 * @version 1.3.2
 */
class ezcTranslationWriterNotInitializedException extends ezcTranslationException
{
    /**
     * Constructs a new ezcTranslationWriterNotInitializedException.
     *
     * @return void
     */
    function __construct()
    {
        parent::__construct( "The writer is not initialized with the initWriter() method." );
    }
}
?>
