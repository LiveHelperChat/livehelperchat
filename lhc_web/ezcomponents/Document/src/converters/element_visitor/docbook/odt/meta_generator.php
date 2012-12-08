<?php
/**
 * File containing the ezcDocumentOdtMetaGenerator class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Generates basic meta data for ODT files.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 * @todo Add more and especially configurable meta data.
 * @todo Replace meta data from template on a configurable basis.
 */
class ezcDocumentOdtMetaGenerator
{
    /**
     * Version string.
     *
     * Automatically replaced during release.
     */
    const VERSION = '1.3.1';

    /**
     * Development version string.
     *
     * Used when {@link self::VERSION} is not replaced with a version number.
     */
    const DEV_VERSION = 'dev';

    /**
     * Generator string template. 
     */
    const GENERATOR = 'eZComponents/Document-%s';

    /**
     * Generates basic meta data in $odtDocument.
     * 
     * @param DOMDocument $odtDocument 
     */
    public function generateMetaData( DOMElement $odtMetaSection )
    {
        $this->insertGenerator( $odtMetaSection );
        $this->insertDate( $odtMetaSection );
    }

    /**
     * Inserts the <meta:generator/> tag.
     * 
     * @param DOMElement $metaSection 
     */
    protected function insertGenerator( DOMElement $metaSection )
    {
        $version = ( self::VERSION === '//auto' . 'gen//'
            ? self::DEV_VERSION
            : self::VERSION
        );

        $metaSection->appendChild(
            $metaSection->ownerDocument->createElementNS(
                ezcDocumentOdt::NS_ODT_META,
                'meta:generator',
                sprintf( self::GENERATOR, $version )
            )
        );
    }

    /**
     * Inserts <meta:creation-date /> and <dc:date/> tags.
     *
     * Note that OpenOffice.org 3.1.1 is not capable of parsing W3C compliant 
     * dates with TZ offset correctly {@see
     * http://www.openoffice.org/issues/show_bug.cgi?id=107437}. We do not work 
     * around this issue, since it's too minor.
     * 
     * @param DOMElement $metaSection 
     */
    protected function insertDate( DOMElement $metaSection )
    {
        $date       = new DateTime();
        $dateString = $date->format( DateTime::W3C );

        $metaSection->appendChild(
            $metaSection->ownerDocument->createElementNS(
                ezcDocumentOdt::NS_ODT_META,
                'meta:creation-date',
                $dateString
            )
        );
        $metaSection->appendChild(
            $metaSection->ownerDocument->createElementNS(
                ezcDocumentOdt::NS_DC,
                'dc:date',
                $dateString
            )
        );
    }
}

?>
