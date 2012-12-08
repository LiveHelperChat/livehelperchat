<?php
/**
 * File containing the ezcWebdavLockHeaderHandler class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Header handler for the lock plugin.
 *
 * An object of this class can be used to parse the following headers:
 *
 * <ul>
 *  <li>Timeout - {@link parseTimeoutHeader()}</li>
 *  <li>If - {@link parseIfHeader()}</li>
 *  <li>Lock-Token - {@link parseLockTokenHeader()}</li>
 * </ul>
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @access private
 */
class ezcWebdavLockHeaderHandler
{
    /**
     * Parses the Timeout header content.
     *
     * Parses the Timeout header. Might return an empty array, in case no
     * Timeout header is present or none of the values in the header could be
     * parsed. The values of the returned array are seconds and indicate the
     * number of seconds, that the client wishes the lock to disappear after it
     * was created or used the last time.
     * 
     * @return array(int)
     */
    public function parseTimeoutHeader()
    {
        if ( !isset( $_SERVER['HTTP_TIMEOUT'] ) )
        {
            return null;
        }
        
        $result = array();

        $content = explode( ', ', $_SERVER['HTTP_TIMEOUT'] );
        foreach ( $content as $timeVal )
        {
            // Sanitize
            $timeVal = trim( $timeVal );

            // We only react on 'Second-' values for now
            if ( substr( $timeVal, 0, 7 ) === 'Second-' )
            {
                $result[] = (int) substr( $timeVal, 7 );
                continue;
            }
            
            // Ignore all other for now.
            // @TODO: Let's see, what clients send.
        }

        return $result;
    }

    /**
     * Parses either type of If header content.
     *
     * Returns an {@link ezcWebdavLockIfHeaderList} or null, if the header was
     * not submitted.
     * 
     * @return ezcWebdavLockIfHeaderList
     *
     * @throws ezcWebdavInvalidHeaderException
     *         if any unexpected character occurs while parsing.
     */
    public function parseIfHeader()
    {
        if ( !isset( $_SERVER['HTTP_IF'] ) )
        {
            return null;
        }
        
        $headerContent = $_SERVER['HTTP_IF'];

        if ( $headerContent[0] === '<' )
        {
            return $this->parseTaggedList( $headerContent );
        }
        else
        {
            return $this->parseNonTagList( $headerContent );
        }
    }

    /**
     * Parses the Lock-Token header.
     *
     * Trims the < and > from a given lock token in the Lock-Token header.
     * 
     * @return string|null
     */
    public function parseLockTokenHeader()
    {
        if ( !isset( $_SERVER['HTTP_LOCK_TOKEN'] ) )
        {
            return null;
        }

        // Strip < and >
        return substr( trim( $_SERVER['HTTP_LOCK_TOKEN'] ), 1, -1 );
    }

    /**
     * Parses tagged If header content.
     *
     * This method parses content of an If header that is tagged. The content has the format:
     *
     * <code>
     * &lthttp://www.foo.bar/resource1&gt (&lt;locktoken:a-write-lock-token&gt; [W/"A weak ETag"]) (["strong ETag"]) &lt;http://www.bar.bar/random&gt: (["another strong ETag"])
     * </code>
     * 
     * @param string $content 
     * @return void
     *
     * @throws ezcWebdavInvalidHeaderException
     *         if any unexpected character occurs while parsing.
     */
    protected function parseTaggedList( $content )
    {
        $len = strlen( $content );
        $i   = 0;
    
        $list = new ezcWebdavLockIfHeaderTaggedList();
        $items = array();

        while ( $i < $len )
        {
            switch ( $content[$i] )
            {
                case '<':
                    if ( $items !== array() )
                    {
                        // Store last parsed list
                        $list[$currentPath] = $items;
                        $items = array();
                    }
                    ++$i;
                    $currentPath = $this->parseTagPath( $content, $len, $i );
                    break;

                case '(':
                    ++$i;
                    $items[] = $this->parseConditionList( $content, $len, $i );
                    break;
                    
                case ' ':
                case "\t":
                case "\n":
                    // skip
                    break;

                default:
                    throw new ezcWebdavInvalidHeaderException(
                        'If',
                        $content,
                        "Headers without invalid character '{$content[$i]}' in tagged list, position $i"
                    );
            }
            ++$i;
        }
        $list[$currentPath] = $items;

        return $list;
    }

    /**
     * Parses the tag of a tagged list.
     * 
     * @param string $content 
     * @param int $len 
     * @param int $i 
     * @return string
     *
     * @throws ezcWebdavInvalidHeaderException
     *         if any unexpected character occurs while parsing.
     */
    protected function parseTagPath( $content, $len, &$i )
    {
        $uri = '';
        
        while ( $i < $len && $content[$i] !== '>' )
        {
            $uri .= $content[$i++];
        }
        ++$i;

        return ezcWebdavServer::getInstance()->pathFactory->parseUriToPath( $uri );
    }

    /**
     * Parses not-tagged If header content.
     *
     * This method parses content of an If header that is tagged. The content has the format:
     *
     * <code>
     * (&lt;locktoken:a-write-lock-token&gt; [W/"A weak ETag"]) (["strong ETag"])(["another strong ETag"])
     * </code>
     * 
     * @param string $content 
     * @return ezcWebdavLockIfHeaderNoTagList
     *
     * @throws ezcWebdavInvalidHeaderException
     *         if any unexpected character occurs while parsing.
     */
    protected function parseNonTagList( $content )
    {
        $len = strlen( $content );
        $i   = 0;
    
        $items = array();

        while ( $i < $len )
        {
            switch ( $content[$i] )
            {
                case '(':
                    ++$i;
                    $items[] = $this->parseConditionList( $content, $len, $i );
                    break;
                case ' ':
                case "\t":
                case "\n":
                    // ignore
                    break;

                default:
                    throw new ezcWebdavInvalidHeaderException(
                        'If',
                        $content,
                        "Headers without invalid character '{$content[$id]}' in non-tagged list, position $i"
                    );
            }
            ++$i;
        }

        return new ezcWebdavLockIfHeaderNoTagList( $items );
    }

    /**
     * Parses a list of lock tokens and etags into a list item.
     * 
     * @param string $content 
     * @param int $len 
     * @param int $i 
     * @return ezcWebdavLockIfHeaderListItem
     *
     * @throws ezcWebdavInvalidHeaderException
     *         if any unexpected character occurs while parsing.
     */
    protected function parseConditionList( $content, $len, &$i )
    {
        $lockTokens = array();
        $eTags      = array();

        $negated = false;

        // Walk complete list and scan tokens/etags
        while ( $i < $len && $content[$i] !== ')' )
        {
            switch ( $content[$i] )
            {
                case '<':
                    ++$i;
                    $lockToken = $this->parseLockToken( $content, $len, $i );
                    if ( $negated )
                    {
                        $lockToken->negated = true;
                    }
                    $lockTokens[] = $lockToken;
                    $negated = false;
                    break;

                case '[':
                    ++$i;
                    $eTag = $this->parseEtag( $content, $len, $i );
                    if ( $negated )
                    {
                        $eTag->negated = true;
                    }
                    $eTags[] = $eTag;
                    $negated = false;
                    break;

                case 'N':
                case 'n':
                    if ( strtolower( substr( $content, $i, 3 ) ) === 'not' )
                    {
                        $negated = true;
                    }
                    $i += 3;
                    break;

                case ' ':
                case "\t":
                case "\n":
                    // ignore
                    break;

                default:
                    throw new ezcWebdavInvalidHeaderException(
                        'If',
                        $content,
                        "Headers without invalid character '{$content[$i]}' in etag/lock-token list, position $i"
                    );
            }
            ++$i;
        }

        return new ezcWebdavLockIfHeaderListItem( $lockTokens, $eTags, $negated );
    }

    /**
     * Parses a single lock token.
     * 
     * @param string $content 
     * @param int $len 
     * @param int $i 
     * @return string
     *
     * @throws ezcWebdavInvalidHeaderException
     *         if any unexpected character occurs while parsing.
     */
    protected function parseLockToken( $content, $len, &$i )
    {
        $token = '';
        
        while ( $i < $len && $content[$i] !== '>' )
        {
            $token .= $content[$i++];
        }

        return new ezcWebdavLockIfHeaderCondition( $token );
    }

    /**
     * Parses a single ETag.
     *
     * Attention, this method ignores weak tags, since the whole Webdav
     * component does not support those.
     * 
     * @param string $content 
     * @param int $len 
     * @param int $i 
     * @return string
     *
     * @throws ezcWebdavInvalidHeaderException
     *         if any unexpected character occurs while parsing.
     */
    protected function parseEtag( $content, $len, &$i )
    {
        $etag = '';

        while ( $i < $len && $content[$i] !== ']' )
        {
            switch ( $content[$i] )
            {
                case '/':
                    // ETag marked as weak, ignore parts before
                    $etag = '';
                    break;

                case '"':
                    // Etag wrapped in "", ignore
                    break;

                default:
                    $etag .= $content[$i];
                    break;
            }
            ++$i;
        }

        return new ezcWebdavLockIfHeaderCondition( $etag );
    }
}

?>
