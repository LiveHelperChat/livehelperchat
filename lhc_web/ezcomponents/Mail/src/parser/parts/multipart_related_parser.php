<?php
/**
 * File containing the ezcMailMultipartRelatedParser class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Parses multipart/related mail parts.
 *
 * @package Mail
 * @version 1.7.1
 * @access private
 */
class ezcMailMultipartRelatedParser extends ezcMailMultipartParser
{
    /**
     * Holds the ezcMailMultipartRelated part corresponding to the data parsed with this parser.
     *
     * @var ezcMailMultipartRelated
     */
    private $part = null;

    /**
     * Constructs a new ezcMailMultipartRelatedParser.
     *
     * @param ezcMailHeadersHolder $headers
     */
    public function __construct( ezcMailHeadersHolder $headers )
    {
        parent::__construct( $headers );
        $this->part = new ezcMailMultipartRelated();
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
        // TODO: support Content-Type: start= as specified by RFC 2387
        if ( !$this->part->getMainPart() )
        {
            $this->part->setMainPart( $part );
            return;
        }
        $this->part->addRelatedPart( $part );
    }

    /**
     * Returns the parts parsed for this multipart.
     *
     * @return ezcMailMultipartRelated
     */
    public function finishMultipart()
    {
        $size = 0;
        if ( $this->part->getMainPart() )
        {
            $size = $this->part->getMainPart()->size;
        }
        foreach ( $this->part->getRelatedParts() as $part )
        {
            $size += $part->size;
        }
        $this->part->size = $size;
        return $this->part;
    }
}

?>
