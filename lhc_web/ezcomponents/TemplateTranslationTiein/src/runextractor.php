<?php
/**
 * File running the ezcTemplateTranslationExtractor class.
 *
 * @package TemplateTranslationTiein
 * @version 1.1.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Require the base class
 */
// Silenced warning here, will be handeled below, if second include fails.
if ( ( @include dirname( __FILE__ ) . '/../../Base/src/base.php' ) === false )
{
    // Silenced warning here, will be handeled below, if second include fails.
    if ( ( @include dirname( __FILE__ ) . '/../Base/base.php' ) === false )
    {
        echo <<<EOT
eZ components environment not setup correctly. Could neither include eZ Base
component from 'Base/src/base.php', nor from 'Base/base.php'. Please check your
include path!

EOT;
        exit( -1 );
    }
}

function __autoload( $className )
{
    ezcBase::autoload( $className );
}

$extractor = new ezcTemplateTranslationExtractor();
$extractor->run();

?>
