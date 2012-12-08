<?php
/**
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.3.2
 * @filesource
 * @package Translation
 */

/**
 * Thrown by the getTranslation() method when a paramater was missing
 * to a parameterized translation string.
 *
 * @package Translation
 * @version 1.3.2
 */
class ezcTranslationParameterMissingException extends ezcTranslationException
{
    /**
     * Constructs a new ezcTranslationParameterMissingException.
     *
     * @param string $parameterName
     * @return void
     */
    function __construct( $parameterName )
    {
        parent::__construct( "The parameter '%{$parameterName}' does not exist." );
    }
}
?>
