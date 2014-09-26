<?php
/**
 * File containing the ezcMailMultipartReportParser class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Parses multipart/report mail parts.
 *
 * @package Mail
 * @version 1.7.1
 * @access private
 */
class ezcMailMultipartReportParser extends ezcMailMultipartParser
{
    /**
     * Holds the ezcMailMultipartReport part corresponding to the data parsed with this parser.
     *
     * @var ezcMailMultipartReport
     */
    private $report;

    /**
     * Holds the mail parts which will be part of the returned multipart report.
     *
     * @var array(ezcMailPart)
     */
    private $parts;

    /**
     * Constructs a new ezcMailMultipartReportParser.
     *
     * @param ezcMailHeadersHolder $headers
     */
    public function __construct( ezcMailHeadersHolder $headers )
    {
        parent::__construct( $headers );
        $this->report = new ezcMailMultipartReport();
        $this->parts = array();
        preg_match( '/\s*report-type="?([^;"]*);?/i',
                    $this->headers['Content-Type'],
                    $parameters );
        if ( count( $parameters ) > 0 )
        {
            $this->report->reportType = trim( $parameters[1], '"' );
        }
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
        $this->parts[] = $part;
    }

    /**
     * Returns the parts parsed for this multipart.
     *
     * @return ezcMailMultipartReport
     */
    public function finishMultipart()
    {
        if ( isset( $this->parts[0] ) )
        {
            $this->report->setReadablePart( $this->parts[0] );
        }
        if ( isset( $this->parts[1] ) )
        {
            $this->report->setMachinePart( $this->parts[1] );
        }
        if ( isset( $this->parts[2] ) )
        {
            $this->report->setOriginalPart( $this->parts[2] );
        }
        $size = 0;
        foreach ( $this->report->getParts() as $part )
        {
            $size += $part->size;
        }
        $this->report->size = $size;
        return $this->report;
    }
}
?>
