<?php
/**
 * File containing the ezcMailMultipartAlternativeParser class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Parses multipart/mixed mail parts.
 *
 * @package Mail
 * @version 1.7.1
 * @access private
 */
class ezcMailMultipartAlternativeParser extends ezcMailMultipartParser
{
    /**
     * Holds the ezcMailMultipartAlternative part corresponding to the data parsed with this parser.
     *
     * @var ezcMailMultipartAlternative
     */
    private $part = null;

    /**
     * Constructs a new ezcMailMultipartAlternativeParser.
     *
     * @param ezcMailHeadersHolder $headers
     */
    public function __construct( ezcMailHeadersHolder $headers )
    {
        parent::__construct( $headers );
        $this->part = new ezcMailMultipartAlternative();
    }

    /**
     * Adds the part $part to the list of multipart messages.
     *
     * This method is called automatically by ezcMailMultipartParser
     * each time a part is parsed.
     *
     * @param ezcMailPart $part
     */
    public function partDone( ezcMailPart $part )
    {
        $this->part->appendPart( $part );
    }

    /**
     * Returns the parts parsed for this multipart.
     *
     * @return ezcMailMultipartAlternative
     */
    public function finishMultipart()
    {
        $size = 0;
        foreach ( $this->part->getParts() as $part )
        {
            $size += $part->size;
        }
        $this->part->size = $size;
        return $this->part;
    }
}

?>
