<?php
/**
 * File containing the ezcDocumentDocbookToHtmlXsltConverterOptions class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing the basic options for the docbook to HTMl conversion.
 *
 * By default the XSLT published by the OASIS [1] is used, with the options
 * documented here:
 * http://docbook.sourceforge.net/release/xsl/current/doc/html/
 *
 * [1] http://docbook.sourceforge.net/release/xsl/current/html/docbook.xsl
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToHtmlXsltConverterOptions extends ezcDocumentXsltConverterOptions
{
    /**
     * Constructs an object with the specified values.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if $options contains a property not defined
     * @throws ezcBaseValueException
     *         if $options contains a property with a value not allowed
     * @param array(string=>mixed) $options
     */
    public function __construct( array $options = array() )
    {
        $this->xslt       = 'http://docbook.sourceforge.net/release/xsl/current/html/docbook.xsl';
        $this->parameters = array(
            '' => array(
                'make.valid.html' => '1',
            ),
        );

        parent::__construct( $options );
    }
}

?>
