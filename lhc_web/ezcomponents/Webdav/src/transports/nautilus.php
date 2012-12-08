<?php
/**
 * File containing the ezcWebdavNautilusCompatibleTransport class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Transport layer for Nautilus (GNOME).
 *
 * In newer Nautilus (aka gvfs) versions, WebDAV with authentication does not 
 * accept Multi-Status responses that include absolute URIs. Since using 
 * relative URIs in this cases does not disturb Nautilus in general, this 
 * transport simply converts all URIs in Multi-Status responses to be relative 
 * to the server root.
 *
 * @version 1.1.4
 * @package Webdav
 * @access private
 */
class ezcWebdavNautilusCompatibleTransport extends ezcWebdavTransport
{
    /**
     * Post-processes <href/> XML elements to contain relative URIs.
     *
     * This is needed by Nautilus when auth is enabled.
     * 
     * @param ezcWebdavPropFindResponse $response 
     * @return ezcWebdavXmlDisplayInformation
     */
    protected function processPropFindResponse( ezcWebdavPropFindResponse $response )
    {
        $xmlDispInfo = parent::processPropFindResponse( $response );
        $subResponses = $xmlDispInfo->body->getElementsByTagNameNS(
            ezcWebdavXmlTool::XML_DEFAULT_NAMESPACE,
            'response'
        );
        foreach ( $subResponses as $subResponse )
        {
            $hrefs = $subResponse->getElementsByTagNameNS(
                ezcWebdavXmlTool::XML_DEFAULT_NAMESPACE,
                'href'
            );
            foreach ( $hrefs as $href )
            {
                $href->nodeValue = parse_url( $href->nodeValue, PHP_URL_PATH );
            }
        }
        return $xmlDispInfo;
    }
}
?>
