<?php
/**
 * File containing the ezcMailHeaderFolder class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Internal class folding headers according to RFC 2822.
 *
 * RFC 2822 specifies two line length restrictions:
 *
 * "There are two limits that this standard places on the number of
 *  characters in a line. Each line of characters MUST be no more than
 *  998 characters, and SHOULD be no more than 78 characters, excluding
 *  the CRLF."
 *
 * The 76 character limit is because of readability. The 998 character limit
 * is a result of SMTP limitations.
 *
 * The rule for folding is:
 * "wherever this standard allows for folding white space (not
 *  simply WSP characters), a CRLF may be inserted before any WSP."
 *
 * This is described in more detail in section 3.2.3.
 *
 * @package Mail
 * @version 1.7.1
 * @access private
 */
class ezcMailHeaderFolder
{
    /**
     * The soft limit of 76 characters per line.
     */
    const SOFT_LIMIT = 76;

    /**
     * The soft limit of 998 characters per line.
     */
    const HARD_LIMIT = 998;

    /**
     * The default folding limit.
     *
     * @var int
     */
    static private $limit = 76;

    /**
     * Sets the number of allowed characters before folding to $numCharacters.
     *
     * $numCharacters must be one of:
     * - ezcMailHeaderFolder::SOFT_LIMIT (76 characters)
     * - ezcMailHeaderFolder::HARD_LIMIT (998 characters)
     *
     * @param int $numCharacters
     */
    static public function setLimit( $numCharacters )
    {
        self::$limit = $numCharacters;
    }

    /**
     * Returns the maximum number of characters allowed per line.
     *
     * @return int
     */
    static public function getLimit()
    {
        return self::$limit;
    }

    /**
     * Returns $text folded to the 998 character limit on any whitespace.
     *
     * The algorithm tries to minimize the number of comparisons by searching
     * backwards from the maximum number of allowed characters on a line.
     *
     * @param string $text
     * @return string
     */
    static public function foldAny( $text )
    {
        // Don't fold unless we have to.
        if ( strlen( $text ) <= self::$limit )
        {
            return $text;
        }

        // go to 998'th char.
        // search back to whitespace
        // fold

        $length = strlen( $text );
        $folded = "";
        // find first occurence of whitespace searching backwards
        $search = 0;
        $previousFold = 0;

        while ( ( $search + self::$limit ) < $length )
        {
            // search from the max possible length of the substring
            $search += self::$limit;
            while ( $text[$search] != " " && $text[$search] != "\t" && $search > $previousFold )
            {
                $search--;
            }

            if ( $search == $previousFold )
            {
                // continuous string of more than limit chars.
                // We will just have to continue searching forwards to the next whitespace instead
                // This is not confirming to standard.. but what can we do?
                $search += self::$limit; // back to where we started
                while ( $search < $length && $text[$search] != " " && $text[$search] != "\t" )
                {
                    $search++;
                }
            }

            // lets fold
            if ( $folded === "" )
            {
                $folded = substr( $text, $previousFold, $search - $previousFold );
            }
            else
            {
                $folded .= ezcMailTools::lineBreak() .
                           substr( $text, $previousFold, $search - $previousFold );
            }
            $previousFold = $search;
        }
        // we need to append the rest if there is any
        if ( $search < $length )
        {
            $folded .= ezcMailTools::lineBreak() . substr( $text, $search );
        }
        return $folded;
    }
}
?>
