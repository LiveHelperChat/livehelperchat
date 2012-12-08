<?php
/**
 * File containing the ezcWebdavMicrosoftCompatibleTransport class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Transport layer for Microsoft clients with RFC incompatiblities.
 *
 * Clients seen, which need this:
 *  - Microsoft Data Access Internet Publishing Provider Cache Manager
 *  - Microsoft Data Access Internet Publishing Provider DAV 1.1
 *  - Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)
 *
 * Still not working:
 *  - Microsoft-WebDAV-MiniRedir/5.1.2600
 *
 * Seen, but unknown:
 *  - Mozilla/2.0 (compatible; MS FrontPage 4.0)
 *  - MSFrontPage/4.0
 *
 * @version 1.1.4
 * @package Webdav
 */
class ezcWebdavMicrosoftCompatibleTransport extends ezcWebdavTransport
{

    /**
     * Flattens a processed response object to headers and body.
     *
     * Takes a given {@link ezcWebdavDisplayInformation} object and returns an
     * array containg the headers and body it represents.
     *
     * The returned information can be processed (send out to the client) by
     * {@link ezcWebdavTransport::sendResponse()}.
     * 
     * @param ezcWebdavDisplayInformation $info 
     * @return ezcWebdavOutputResult
     */
    protected function flattenResponse( ezcWebdavDisplayInformation $info )
    {
        $output = parent::flattenResponse( $info );

        // Add MS specific header
        $output->headers['MS-Author-Via'] = 'DAV';

        // MS seems always want it this way, even when we do not support
        // locking
        $output->headers['DAV'] = '1, 2';

        if ( $info instanceof ezcWebdavXmlDisplayInformation )
        {
            // Only mangle output if XML is to be sent (which does not include 
            // GET of XML files, but only response XML).
            $this->mangleXml( $output );
        }

        return $output;
    }

    /**
     * Performs MS specific XML mangling on output.
     *
     * MS user agents show strange behaviour regarding XML processing. The 
     * following quirks are resolved by this method, to make such user agents 
     * accept the generated XML:
     *
     * - Add special namespace declarations and special shortcuts for the DAV: 
     *   namespace
     * - Rename shortcuts for some elements into these special ones
     * - Add special XML attributes not defined in the RFC but expected by user 
     *   agents
     * - Remove all non-significant whitespaces
     * - Add a newline at the end of the body
     * 
     * @param ezcWebdavOutputResult $output 
     */
    private function mangleXml( ezcWebdavOutputResult $output )
    {
        // Add date namespace to response elements for MS clients
        // 
        // Mimic Apache mod_dav behaviour for DAV: namespace
        $output->body = preg_replace(
            '(<D:response([^>]*)>)',
            '<D:response\\1 xmlns:lp1="DAV:" xmlns:lp2="http://apache.org/dav/props/" xmlns:ns0="urn:uuid:c2f41010-65b3-11d1-a29f-00aa00c14882/">',
            $output->body
        );

        // Set creationdate namespace
        $output->body = preg_replace(
            '(<D:creationdate([^>]*)>)',
            '<D:creationdate\\1 ns0:dt="dateTime.tz">',
            $output->body
        );

        // Set getlastmodified namespace
        $output->body = preg_replace(
            '(<D:getlastmodified([^>]*)>)',
            '<D:getlastmodified\\1 ns0:dt="dateTime.rfc1123">',
            $output->body
        );

        // Put some elements in DAV: namespace with other namespace identifier
        // to mimic Apache mod_dav behaviour for DAV: namespace
        $output->body = preg_replace(
            '(D:(resourcetype|creationdate|getlastmodified|getetag)([^>]*))',
            'lp1:\\1\\2',
            $output->body
        );

        // Remove all unessecary whitespaces
        $output->body = preg_replace(
            '(>\s+<)',
            '><',
            $output->body
        );

        // MS IE7 requires a newline after the XML.
        $output->body .= "\n";
    }

    /**
     * Parses the PROPFIND request and returns a request object.
     *
     * Microsoft clients may send an empty request, so that we guess, that they
     * meant an allprop request, fill the body struct accordingly and then
     * dispatch to the original method.
     * 
     * @param string $path 
     * @param string $body 
     * @return ezcWebdavPropFindRequest
     */
    protected function parsePropFindRequest( $path, $body )
    {
        // Empty request seem to actually mean an allprop request.
        if ( trim( $body ) === '' )
        {
            $body = '<?xml version="1.0" encoding="utf-8" ?>
<D:propfind xmlns:D="DAV:">
  <D:allprop/>
</D:propfind>';
        }

        return parent::parsePropFindRequest( $path, $body );
    }
}
?>
