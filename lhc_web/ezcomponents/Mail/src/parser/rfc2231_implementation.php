<?php
/**
 * File containing the ezcMailRfc2231Implementation class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * This class parses header fields that conform to RFC2231.
 *
 * Headers conforming to this specification are Content-Type and Content-Disposition.
 *
 * @package Mail
 * @version 1.7.1
 * @access private
 */
class ezcMailRfc2231Implementation
{
    /**
     * Returns the parsed header $header according to RFC 2231.
     *
     * This method returns the parsed header as a structured array and is
     * intended for internal usage. Use parseContentDisposition and
     * parseContentType to retrieve the correct header structs directly.
     *
     * @param string $header
     * @return array( 'argument', array( 'paramName' => array( value => string, charset => string,
     * language => string ) ) );
     */
    public static function parseHeader( $header )
    {
        $result = array();
        // argument
        if ( preg_match( '/^\s*([^;]*);?/i', $header, $matches ) )
        {
            $result[0] = $matches[1];
        }

        // We must go through all parameters and store this data because
        // parameters can be unordered. We will store them in this buffer
        // array( paramName => array( array( value => string, encoding ) ) )
        $parameterBuffer = array();

        // parameters
        if ( preg_match_all( '/\s*(\S*?)="?([^;"]*);?/i', $header, $matches, PREG_SET_ORDER ) )
        {
            foreach ( $matches as $parameter )
            {
                // if normal parameter, simply add it
                if ( !preg_match( '/([^\*]+)\*(\d+)?(\*)?/', $parameter[1], $metaData ) )
                {
                    $result[1][$parameter[1]] = array( 'value' => $parameter[2] );
                }
                else // coded and/or folded
                {
                    // metaData [1] holds the param name
                    // metaData [2] holds the count or is not set in case of charset only
                    // metaData [3] holds '*' if there is charset in addition to folding
                    if ( isset( $metaData[2] ) ) // we have folding
                    {
                        $parameterBuffer[$metaData[1]][$metaData[2]]['value'] = $parameter[2];
                        $parameterBuffer[$metaData[1]][$metaData[2]]['encoding'] =
                            isset( $metaData[3] ) ? true : false;;
                    }
                    else
                    {
                        $parameterBuffer[$metaData[1]][0]['value'] = $parameter[2];
                        $parameterBuffer[$metaData[1]][0]['encoding'] = true;
                    }
                }
            }

            // whohooo... we have all the parameters nicely sorted.
            // Now we must go through them all and convert them into the end result
            foreach ( $parameterBuffer as $paramName => $parts )
            {
                // fetch language and encoding if we have it
                // syntax: '[charset]'[language]'encoded_string
                $language = null;
                $charset = null;
                if ( $parts[0]['encoding'] == true )
                {
                    preg_match( "/(\S*)'(\S*)'(.*)/", $parts[0]['value'], $matches );
                    $charset = $matches[1];
                    $language = $matches[2];
                    $parts[0]['value'] = urldecode( $matches[3] ); // rewrite value: todo: decoding
                    $result[1][$paramName] = array( 'value' => $parts[0]['value'] );
                }

                $result[1][$paramName] = array( 'value' => $parts[0]['value'] );
                if ( strlen( $charset ) > 0 )
                {
                    $result[1][$paramName]['charset'] = $charset;
                }
                if ( strlen( $language ) > 0 )
                {
                    $result[1][$paramName]['language'] = $language;
                }

                if ( count( $parts > 1 ) )
                {
                    for ( $i = 1; $i < count( $parts ); $i++ )
                    {
                        $result[1][$paramName]['value'] .= $parts[$i]['encoding'] ?
                            urldecode( $parts[$i]['value'] ) : $parts[$i]['value'];
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Returns the a ezcMailContentDispositionHeader for the parsed $header.
     *
     * If $cd is provided this object will be used to fill in the blanks. This function
     * will not clear out any old values in the object.
     *
     * @param string $header
     * @param ezcMailContentDispositionHeader $cd
     * @return ezcMailContentDispositionHeader
     */
    public static function parseContentDisposition( $header, ezcMailContentDispositionHeader $cd = null )
    {
        if ( $cd === null )
        {
            $cd = new ezcMailContentDispositionHeader();
        }

        $parsedHeader = self::parseHeader( $header );
        $cd->disposition = $parsedHeader[0];
        if ( isset( $parsedHeader[1] ) )
        {
            foreach ( $parsedHeader[1] as $paramName => $data )
            {
                switch ( $paramName )
                {
                    case 'filename':
                        $cd->fileName = $data['value'];
                        $cd->displayFileName = trim( $data['value'], '"' );
                        if ( isset( $data['charset'] ) )
                        {
                            $cd->fileNameCharSet = $data['charset'];
                            $cd->displayFileName = ezcMailCharsetConverter::convertToUTF8Iconv( $cd->displayFileName, $cd->fileNameCharSet );
                        }
                        // Work around for bogus email clients that think
                        // it's allowed to use mime-encoding for filenames.
                        // It isn't, see RFC 2184, and issue #13038.
                        else if ( preg_match( '@^=\?[^?]+\?[QqBb]\?@', $cd->displayFileName ) )
                        {
                            $cd->displayFileName = ezcMailTools::mimeDecode( $cd->displayFileName );
                        }
 
                        if ( isset( $data['language'] ) )
                        {
                            $cd->fileNameLanguage = $data['language'];
                        }
                        break;
                    case 'creation-date':
                        $cd->creationDate = $data['value'];
                        break;
                    case 'modification-date':
                        $cd->modificationDate = $data['value'];
                        break;
                    case 'read-date':
                        $cd->readDate = $data['value'];
                        break;
                    case 'size':
                        $cd->size = $data['value'];
                        break;
                    default:
                        $cd->additionalParameters[$paramName] = $data['value'];
                        if ( isset( $data['charset'] ) )
                        {
                            $cd->additionalParametersMetaData[$paramName]['charSet'] = $data['charset'];
                        }
                        if ( isset( $data['language'] ) )
                        {
                            $cd->additionalParametersMetaData[$paramName]['language'] = $data['language'];
                        }
                        break;
                }
            }
        }
        return $cd;
    }
}
?>
