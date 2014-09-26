<?php
/**
 * File containing the ezcMailDeliveryStatus class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Mail part used for sending delivery status message.
 *
 * Multipart/Report: RFC 3462 {@link http://tools.ietf.org/html/rfc3462}
 * Delivery Status Notifications: RFC 3464 {@link http://tools.ietf.org/html/rfc3464}
 *
 * This mail part consists of only headers. The headers are organized into section.
 * There is a per-message section ($message), and several per-recipient sections ($recipients).
 *
 * To access the headers of this part, look at the following example:
 * <code>
 * // $delivery is an object of type ezcMailDeliveryStatus
 * $reportingMta = $delivery->message["Reporting-MTA"];
 * $date = $delivery->message["Arrival-Date"];
 * // get the status received from the first recipient
 * $status1 = $delivery->recipients[0]["Status"];
 * // get the status received from the second recipient
 * $status2 = $delivery->recipients[1]["Status"];
 * </code>
 *
 * @property ezcMailHeadersHolder $message
 *           Holds the per-message headers of the delivery-status message.
 * @property ArrayObject(ezcMailHeadersHolder) $recipients
 *           Holds the recipients of the delivery-status message.
 *
 * @package Mail
 * @version 1.7.1
 */
class ezcMailDeliveryStatus extends ezcMailPart
{
    /**
     * Constructs a new DeliveryStatus part.
     */
    public function __construct()
    {
        $this->message = new ezcMailHeadersHolder();
        $this->recipients = new ArrayObject();
        parent::__construct();
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
            case 'message':
            case 'recipients':
                $this->properties[$name] = $value;
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
            case 'message':
            case 'recipients':
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
            case 'message':
            case 'recipients':
                return isset( $this->properties[$name] );

            default:
                return parent::__isset( $name );
        }
    }

    /**
     * Returns the headers set for this part as a RFC822 compliant string.
     *
     * This method does not add the required two lines of space
     * to separate the headers from the body of the part.
     *
     * @see setHeader()
     * @return string
     */
    public function generateHeaders()
    {
        $this->setHeader( "Content-Type", "message/delivery-status" );
        return parent::generateHeaders();
    }

    /**
     * Returns the generated text body of this part as a string.
     *
     * @return string
     */
    public function generateBody()
    {
        $result = $this->addHeadersSection( $this->message ) . ezcMailTools::lineBreak();
        for ( $i = 0; $i < count( $this->recipients ); $i++ )
        {
            $result .= $this->addHeadersSection( $this->recipients[$i] ) . ezcMailTools::lineBreak();
        }
        return $result;
    }

    /**
     * Returns the generated text for a section of the delivery-status part.
     *
     * @param ezcMailHeadersHolder $headers
     * @return string
     */
    private function addHeadersSection( ezcMailHeadersHolder $headers )
    {
        $result = "";
        foreach ( $headers->getCaseSensitiveArray() as $header => $value )
        {
            $result .= $header . ": " . $value . ezcMailTools::lineBreak();
        }
        return $result;
    }

    /**
     * Adds a new recipient to this delivery-status message and returns the index
     * of the last added recipient.
     *
     * @return int
     */
    public function createRecipient()
    {
        $result = count( $this->recipients );
        $this->recipients[$result] = new ezcMailHeadersHolder();
        return $result;
    }
}
?>
