<?php
/**
 * File containing the ezcMailMultipartReport class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Mail part multipart/report used primarily to send delivery status notification messages.
 *
 * Multipart/Report: RFC 3462 {@link http://tools.ietf.org/html/rfc3462}
 * Delivery Status Notifications: RFC 3464 {@link http://tools.ietf.org/html/rfc3464}
 *
 * The subparts of this mail part are according to RFC 3462:
 *
 * 1. A human readable part. The purpose of this part is to provide an easily understood
 *    description of the condition(s) that caused the report to be generated.
 *      Use the methods getReadablePart() and setReadablePart() to work with this part.
 *
 * 2. A machine parsable body part containing an account of
 *    the reported message handling event. The purpose of this body part is
 *    to provide a machine-readable description of the condition(s) that
 *    caused the report to be generated, along with details not present in
 *    the first body part that may be useful to human experts.
 *      Use the methods getMachinePart() and setMachinePart() to work with this part.
 *
 * 3. Optional. A body part containing the returned message or a
 *    portion thereof. This information may be useful to aid human experts
 *    in diagnosing problems.
 *      Use the methods getOriginalPart() and setOriginalPart() to work with this part.
 *
 * @property string $reportType
 *           The report type of the multipart report. Default is "delivery-status".
 *
 * @package Mail
 * @version 1.7.1
 */
class ezcMailMultipartReport extends ezcMailMultipart
{
    /**
     * Constructs a new ezcMailMultipartReport.
     *
     * @param ezcMailPart|array(ezcMailPart) $...
     */
    public function __construct()
    {
        $args = func_get_args();
        parent::__construct( $args );
        $this->reportType = "delivery-status";
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property does not exist
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'reportType':
                $this->properties[$name] = $value;
                $this->setHeader( 'Content-Type', 'multipart/' . $this->multipartType() . '; ' .
                                  'report-type=' . $this->reportType . '; ' .
                                  'boundary="' . $this->boundary . '"' );
                break;

            default:
                return parent::__set( $name, $value );
                break;
        }
    }

    /**
     * Returns the property $name.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property does not exist
     * @param string $name
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'reportType':
                return $this->properties[$name];
                break;

            default:
                return parent::__get( $name );
                break;
        }
    }

    /**
     * Returns true if the property $name is set, otherwise false.
     *
     * @param string $name
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        switch ( $name )
        {
            case 'reportType':
                return isset( $this->properties[$name] );

            default:
                return parent::__isset( $name );
        }
    }

    /**
     * Appends a part to the list of parts.
     *
     * @param ezcMailPart $part
     */
    public function appendPart( ezcMailPart $part )
    {
        $this->parts[] = $part;
    }

    /**
     * Returns the mail parts associated with this multipart.
     *
     * @return array(ezcMailPart)
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * Sets the readable $part of this report multipart.
     *
     * @param ezcMailPart $part
     */
    public function setReadablePart( ezcMailPart $part )
    {
        $this->parts[0] = $part;
    }

    /**
     * Returns the readable part of this multipart or null if there is no such part.
     *
     * @return ezcMailPart
     */
    public function getReadablePart()
    {
        if ( isset( $this->parts[0] ) )
        {
            return $this->parts[0];
        }
        return null;
    }

    /**
     * Sets the machine $part of this report multipart.
     *
     * @param ezcMailPart $part
     */
    public function setMachinePart( ezcMailPart $part )
    {
        $this->parts[1] = $part;
    }

    /**
     * Returns the machine part of this multipart or null if there is no such part.
     *
     * @return ezcMailPart
     */
    public function getMachinePart()
    {
        if ( isset( $this->parts[1] ) )
        {
            return $this->parts[1];
        }
        return null;
    }

    /**
     * Sets the original content $part of this report multipart.
     *
     * @param ezcMailPart $part
     */
    public function setOriginalPart( ezcMailPart $part )
    {
        $this->parts[2] = $part;
    }

    /**
     * Returns the original content part of this multipart or null if there is no such part.
     *
     * @return ezcMailPart
     */
    public function getOriginalPart()
    {
        if ( isset( $this->parts[2] ) )
        {
            return $this->parts[2];
        }
        return null;
    }

    /**
     * Returns "report".
     *
     * @return string
     */
    public function multipartType()
    {
        return "report";
    }
}
?>
