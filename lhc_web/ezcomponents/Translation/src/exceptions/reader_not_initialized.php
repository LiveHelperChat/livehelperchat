<?php
/**
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.3.2
 * @filesource
 * @package Translation
 */

/**
 * Thrown when methods are called that require a ContextReader to be
 * initialized.
 *
 * @package Translation
 * @version 1.3.2
 */
class ezcTranslationReaderNotInitializedException extends ezcTranslationException
{
    /**
     * Constructs a new ezcTranslationReaderNotInitializedException.
     *
     * @return void
     */
    function __construct()
    {
        parent::__construct( "The reader is not initialized with the initReader() method." );
    }
}
?>
