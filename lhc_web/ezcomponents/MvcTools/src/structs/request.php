<?php
/**
 * File containing the ezcMvcRequest class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * The request object holds the request data.
 *
 * The request object should be created by the request parser
 * in the first place.
 * It may also be returned by the controller, in the case of an
 * internal redirection.
 *
 * It holds the protocol dependant data in an ezcMvcRawRequest
 * object that is held in property $raw.
 *
 * It holds several structs which contain some protocol abstract
 * data in the following properties:
 * - $files: array of instances of ezcMvcRequestFile.
 * - $cache: instance of ezcMvcRequestCache
 * - $content: instance of ezcMvcRequestContent
 * - $agent: instance of ezcMvcRequestAgent
 * - $authentication: instance of ezcMvcRequestAuthentication
 *
 * It holds request variables like an array. For example, to hold
 * a 'controller' GET variable in $request['controller'].
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcRequest extends ezcBaseStruct
{
    /**
     * Date of the request
     *
     * @var DateTime
     */
    public $date;

    /**
     * Protocol description in a normalized form
     * F.e. http-get, http-post, http-delete, mail, jabber
     *
     * @var string
     */
    public $protocol;

    /**
     * Hostname of the requested server
     *
     * @var string
     */
    public $host;

    /**
     * Uri of the requested resource
     *
     * @var string
     */
    public $uri;

    /**
     * Full Uri - combination of host name and uri in a protocol independent
     * order
     *
     * @var string
     */
    public $requestId;

    /**
     * Request ID of the referring URI in the same format as $requestId
     *
     * @var string
     */
    public $referrer;

    /**
     * Request variables.
     *
     * @var array
     */
    public $variables;

    /**
     * Request body.
     *
     * @var string
     */
    public $body;

    /**
     * Files bundled with the request.
     *
     * @var array(ezcMvcRequestFile)
     */
    public $files;

    /**
     * Request content type informations.
     *
     * @var ezcMvcRequestAccept
     */
    public $accept;

    /**
     * Request user agent informations.
     *
     * @var ezcMvcRequestUserAgent
     */
    public $agent;

    /**
     * Request authentication data.
     *
     * @var ezcMvcRequestAuthentication
     */
    public $authentication;

    /**
     * Raw request data
     *
     * @var ezcMvcRawRequest
     */
    public $raw;

    /**
     * Contains all the cookies to be set
     *
     * @var array(ezcMvcRequestCookie)
     */
    public $cookies;

    /**
     * Whether this is a fatal error request, or a normal one
     *
     * @var boolean
     */
    public $isFatal;

    /**
     * Constructs a new ezcMvcRequest.
     *
     * @param DateTime $date
     * @param string $protocol
     * @param string $host
     * @param string $uri
     * @param string $requestId
     * @param string $referrer
     * @param array $variables
     * @param string $body
     * @param array(ezcMvcRequestFile) $files
     * @param ezcMvcRequestAccept $accept
     * @param ezcMvcRequestUserAgent $agent
     * @param ezcMvcRequestAuthentication $authentication
     * @param ezcMvcRawRequest $raw
     * @param array(ezcMvcRequestCookie) $cookies
     * @param bool $isFatal
     */
    public function __construct( $date = null, $protocol = '',
        $host = '', $uri = '', $requestId = '', $referrer = '',
        $variables = array(), $body = '', $files = null, $accept = null,
        $agent = null, $authentication = null, $raw = null, $cookies = array(), $isFatal = false )
    {
        $this->date = $date;
        $this->protocol = $protocol;
        $this->host = $host;
        $this->uri = $uri;
        $this->requestId = $requestId;
        $this->referrer = $referrer;
        $this->variables = $variables;
        $this->body = $body;
        $this->files = $files;
        $this->accept = $accept;
        $this->agent = $agent;
        $this->authentication = $authentication;
        $this->raw = $raw;
        $this->cookies = $cookies;
    }

    /**
     * Returns a new instance of this class with the data specified by $array.
     *
     * $array contains all the data members of this class in the form:
     * array('member_name'=>value).
     *
     * __set_state makes this class exportable with var_export.
     * var_export() generates code, that calls this method when it
     * is parsed with PHP.
     *
     * @param array(string=>mixed) $array
     * @return ezcMvcRequest
     */
    static public function __set_state( array $array )
    {
        return new ezcMvcRequest( $array['date'], $array['protocol'],
            $array['host'], $array['uri'], $array['requestId'],
            $array['referrer'], $array['variables'], $array['body'],
            $array['files'], $array['accept'], $array['agent'],
            $array['authentication'], $array['raw'], $array['cookies'],
            $array['isFatal'] );
    }
}
?>
