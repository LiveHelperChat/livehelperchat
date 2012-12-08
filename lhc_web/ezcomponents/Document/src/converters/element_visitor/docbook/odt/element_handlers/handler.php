<?php
/**
 * File containing the ezcDocumentDocbookToOdtBaseHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Base class for ODT visitor handlers.
 *
 * ODT visitor handlers require a styler to be available, which is capable of
 * infering style information from DocBook elements and to apply them to ODT 
 * elements.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
abstract class ezcDocumentDocbookToOdtBaseHandler extends ezcDocumentElementVisitorHandler
{
    /**
     * ODT styler. 
     * 
     * @var ezcDocumentOdtStyler
     */
    protected $styler;

    /**
     * Creates a new handler which utilizes the given $styler. 
     * 
     * @param ezcDocumentOdtStyler $styler 
     */
    public function __construct( ezcDocumentOdtStyler $styler )
    {
        $this->styler = $styler;
    }
}

?>
