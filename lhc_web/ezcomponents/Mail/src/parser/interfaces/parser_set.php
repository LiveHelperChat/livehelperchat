<?php
/**
 * File containing the ezcMailParserSet interface
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Interface common to all parser sets.
 *
 * A parser set provides a simple interface to fetch mail data line by
 * line from a set of mail.
 *
 * @package Mail
 * @version 1.7.1
 */
interface ezcMailParserSet
{
    /**
     * Returns one line of data from the current mail in the set
     * including the ending linebreak.
     *
     * Null is returned if there is no current mail in the set or
     * the end of the mail is reached,
     *
     * @return string
     */
    public function getNextLine();

    /**
     * Moves the set to the next mail and returns true upon success.
     *
     * False is returned if there are no more mail in the set.
     *
     * @return bool
     */
    public function nextMail();

    /**
     * Returns true if mail data is available for parsing.
     *
     * @return bool
     */
    public function hasData();
}
?>
