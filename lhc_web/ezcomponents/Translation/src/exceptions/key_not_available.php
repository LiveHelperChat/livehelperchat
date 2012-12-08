<?php
/**
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.3.2
 * @filesource
 * @package Translation
 */

/**
 * Thrown by the getTranslation() method when a requested key doesn't exist.
 *
 * @package Translation
 * @version 1.3.2
 */
class ezcTranslationKeyNotAvailableException extends ezcTranslationException
{
    /**
     * Constructs a new ezcTranslationKeyNotAvailableException.
     *
     * @param string $keyName
     * @return void
     */
    function __construct( $keyName )
    {
        parent::__construct( "The key '{$keyName}' does not exist in the translation map." );
    }
}
?>
