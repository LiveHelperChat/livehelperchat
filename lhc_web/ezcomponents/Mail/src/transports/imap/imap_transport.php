<?php
/**
 * File containing the ezcMailImapTransport class.
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The class ezcMailImapTransport implements functionality for handling IMAP
 * mail servers.
 *
 * The implementation supports most of the commands specified in:
 *  - {@link http://www.faqs.org/rfcs/rfc1730.html} (IMAP4)
 *  - {@link http://www.faqs.org/rfcs/rfc2060.html} (IMAP4rev1)
 *
 * Each user account on the IMAP server has it's own folders (mailboxes).
 * Mailboxes can be created, renamed or deleted. All accounts have a special
 * mailbox called Inbox which cannot be deleted or renamed.
 *
 * Messages are organized in mailboxes, and are identified by a message number
 * (which can change over time) and a unique ID (which does not change under
 * normal circumstances). The commands operating on messages can handle both
 * modes (message numbers or unique IDs).
 *
 * Messages are marked by certain flags (SEEN, DRAFT, etc). Deleting a message
 * actually sets it's DELETED flag, and a later call to {@link expunge()} will
 * delete all the messages marked with the DELETED flag.
 *
 * The IMAP server can be in different states. Most IMAP commands require
 * that a connection is established and a user is authenticated. Certain
 * commands require in addition that a mailbox is selected.
 *
 * The IMAP transport class allows developers to interface with an IMAP server.
 * The commands which support unique IDs to refer to messages are marked with
 * [*] (see {@link ezcMailImapTransportOptions} to find out how to enable
 * unique IDs referencing):
 *
 * Basic commands:
 *  - connect to an IMAP server ({@link __construct()})
 *  - authenticate a user with a username and password ({@link authenticate()})
 *  - select a mailbox ({@link selectMailbox()})
 *  - disconnect from the IMAP server ({@link disconnect()})
 *
 * Work with mailboxes:
 *  - get the list of mailboxes of the user ({@link listMailboxes()})
 *  - create a mailbox ({@link createMailbox()})
 *  - rename a mailbox ({@link renameMailbox()})
 *  - delete a mailbox ({@link deleteMailbox()})
 *  - append a message to a mailbox ({@link append()})
 *  - select a mailbox ({@link selectMailbox()})
 *  - get the status of messages in the current mailbox ({@link status()})
 *  - get the number of messages with a certain flag ({@link countByFlag()})
 *
 * Work with message numbers (on the currently selected mailbox):
 *  - get the message numbers and sizes of all the messages ({@link listMessages()})
 *  - get the message numbers and IDs of all the messages ({@link listUniqueIdentifiers()})
 *  - [*] get the headers of a certain message ({@link top()})
 *  - [*] delete a message ({@link delete()} and {@link expunge()})
 *  - [*] copy messages to another mailbox ({@link copyMessages()})
 *  - [*] get the sizes of the specified messages ({@link fetchSizes()})
 *
 * Work with flags (on the currently selected mailbox):
 *  - [*] get the flags of the specified messages ({@link fetchFlags()})
 *  - [*] set a flag on the specified messages ({@link setFlag()})
 *  - [*] clear a flag from the specified messages ({@link clearFlag()})
 *
 * Work with {@link ezcMailImapSet} sets (parseable with {@link ezcMailParser})
 * (on the currently selected mailbox):
 *  - [*] create a set from all messages ({@link fetchAll()})
 *  - [*] create a set from a certain message ({@link fetchByMessageNr()})
 *  - [*] create a set from a range of messages ({@link fetchFromOffset()})
 *  - [*] create a set from messages with a certain flag ({@link fetchByFlag()})
 *  - [*] create a set from a sorted range of messages ({@link sortFromOffset()})
 *  - [*] create a set from a sorted list of messages ({@link sortMessages()})
 *  - [*] create a set from a free-form search ({@link searchMailbox()})
 *
 * Miscellaneous commands:
 *  - get the capabilities of the IMAP server ({@link capability()})
 *  - get the hierarchy delimiter (useful for nested mailboxes) ({@link getHierarchyDelimiter()})
 *  - issue a NOOP command to keep the connection alive ({@link noop()})
 *
 * The usual operation with an IMAP server is illustrated by this example:
 * <code>
 * // create a new IMAP transport object by specifying the server name, optional port
 * // and optional SSL mode
 * $options = new ezcMailImapTransportOptions();
 * $options->ssl = true;
 * $imap = new ezcMailImapTransport( 'imap.example.com', null, $options );
 *
 * // Authenticate to the IMAP server
 * $imap->authenticate( 'username', 'password' );
 *
 * // Select a mailbox (here 'Inbox')
 * $imap->selectMailbox( 'Inbox' );
 *
 * // issue commands to the IMAP server
 * // for example get the number of RECENT messages
 * $recent = $imap->countByFlag( 'RECENT' );
 *
 * // see the above list of commands or consult the online documentation for
 * // the full list of commands you can issue to an IMAP server and examples
 *
 * // disconnect from the IMAP server
 * $imap->disconnect();
 * </code>
 *
 * See {@link ezcMailImapTransportOptions} for other options you can specify
 * for IMAP.
 *
 * @todo ignore messages of a certain size?
 * @todo // support for signing?
 * @todo listUniqueIdentifiers(): add UIVALIDITY value to UID (like in POP3).
 *       (if necessary).
 *
 * @property ezcMailImapTransportOptions $options
 *           Holds the options you can set to the IMAP transport.
 *
 * @package Mail
 * @version 1.7.1
 * @mainclass
 */
class ezcMailImapTransport
{
    /**
     * Internal state when the IMAP transport is not connected to a server.
     *
     * @access private
     */
    const STATE_NOT_CONNECTED = 1;

    /**
     * Internal state when the IMAP transport is connected to a server,
     * but no successful authentication has been performed.
     *
     * @access private
     */
    const STATE_NOT_AUTHENTICATED = 2;

    /**
     * Internal state when the IMAP transport is connected to a server
     * and authenticated, but no mailbox is selected yet.
     *
     * @access private
     */
    const STATE_AUTHENTICATED = 3;

    /**
     * Internal state when the IMAP transport is connected to a server,
     * authenticated, and a mailbox is selected.
     *
     * @access private
     */
    const STATE_SELECTED = 4;

    /**
     * Internal state when the IMAP transport is connected to a server,
     * authenticated, and a mailbox is selected read only.
     *
     * @access private
     */
    const STATE_SELECTED_READONLY = 5;

    /**
     * Internal state when the LOGOUT command has been issued to the IMAP
     * server, but before the disconnect has taken place.
     *
     * @access private
     */
    const STATE_LOGOUT = 6;

    /**
     * The response sent from the IMAP server is "OK".
     *
     * @access private
     */
    const RESPONSE_OK = 1;

    /**
     * The response sent from the IMAP server is "NO".
     *
     * @access private
     */
    const RESPONSE_NO = 2;

    /**
     * The response sent from the IMAP server is "BAD".
     *
     * @access private
     */
    const RESPONSE_BAD = 3;

    /**
     * The response sent from the IMAP server is untagged (starts with "*").
     *
     * @access private
     */
    const RESPONSE_UNTAGGED = 4;

    /**
     * The response sent from the IMAP server requires the client to send
     * information (starts with "+").
     *
     * @access private
     */
    const RESPONSE_FEEDBACK = 5;

    /**
     * Use UID commands (access messages by their unique ID).
     *
     * @access private
     */
    const UID = 'UID ';

    /**
     * Use message number commands (access messages by their message numbers).
     *
     * @access private
     */
    const NO_UID = '';

    /**
     * The string returned by Google IMAP servers when at connection time.
     *
     * @access private
     */
    const SERVER_GIMAP = 'Gimap';

    /**
     * Basic flags are used by {@link setFlag()} and {@link clearFlag()}
     *
     * Basic flags:
     *  - ANSWERED   - message has been answered
     *  - DELETED    - message is marked to be deleted by later EXPUNGE
     *  - DRAFT      - message is marked as a draft
     *  - FLAGGED    - message is "flagged" for urgent/special attention
     *  - SEEN       - message has been read
     *
     * @var array(string)
     */
    protected static $basicFlags = array( 'ANSWERED', 'DELETED', 'DRAFT', 'FLAGGED', 'SEEN' );

    /**
     * Extended flags are used by {@link searchByFlag()}
     *
     * Basic flags:
     *  - ANSWERED   - message has been answered
     *  - DELETED    - message is marked to be deleted by later EXPUNGE
     *  - DRAFT      - message is marked as a draft
     *  - FLAGGED    - message is "flagged" for urgent/special attention
     *  - RECENT     - message is recent
     *  - SEEN       - message has been read
     *
     * Opposites of the above flags:
     *  - UNANSWERED
     *  - UNDELETED
     *  - UNDRAFT
     *  - UNFLAGGED
     *  - OLD
     *  - UNSEEN
     *
     * Composite flags:
     *  - NEW        - equivalent to RECENT + UNSEEN
     *  - ALL        - all the messages
     *
     * @var array(string)
     */
    protected static $extendedFlags = array( 'ALL', 'ANSWERED', 'DELETED', 'DRAFT', 'FLAGGED', 'NEW', 'OLD', 'RECENT', 'SEEN', 'UNANSWERED', 'UNDELETED', 'UNDRAFT', 'UNFLAGGED', 'UNRECENT', 'UNSEEN' );

    /**
     * Used to generate a tag for sending commands to the IMAP server.
     *
     * @var string
     */
    protected $currentTag = 'A0000';

    /**
     * Holds the connection state.
     *
     * @var int {@link STATE_NOT_CONNECTED},
     *          {@link STATE_NOT_AUTHENTICATED},
     *          {@link STATE_AUTHENTICATED},
     *          {@link STATE_SELECTED},
     *          {@link STATE_SELECTED_READONLY} or
     *          {@link STATE_LOGOUT}.
     */
    protected $state = self::STATE_NOT_CONNECTED;

    /**
     * Holds the currently selected mailbox.
     *
     * @var string
     */
    protected $selectedMailbox = null;

    /**
     * Holds the connection to the IMAP server.
     *
     * @var ezcMailTransportConnection
     */
    protected $connection = null;

    /**
     * Holds the string which identifies the IMAP server type.
     *
     * Used for fixing problems with Google IMAP (see issue #14360). Possible
     * values are {@link self::SERVER_GIMAP} or null for all other servers.
     *
     * @todo Add identification strings for each existing IMAP server?
     *
     * @var string
     */
    protected $serverType = null;

    /**
     * Holds the options for an IMAP transport connection.
     *
     * @var ezcMailImapTransportOptions
     */
    private $options;

    /**
     * Creates a new IMAP transport and connects to the $server at $port.
     *
     * You can specify the $port if the IMAP server is not on the default port
     * 993 (for SSL connections) or 143 (for plain connections). Use the $options
     * parameter to specify an SSL connection.
     *
     * See {@link ezcMailImapTransportOptions} for options you can specify for
     * IMAP.
     *
     * Example of creating an IMAP transport:
     * <code>
     * // replace with your IMAP server address
     * $imap = new ezcMailImapTransport( 'imap.example.com' );
     *
     * // if you want to use SSL:
     * $options = new ezcMailImapTransportOptions();
     * $options->ssl = true;
     *
     * $imap = new ezcMailImapTransport( 'imap.example.com', null, $options );
     * </code>
     *
     * @throws ezcMailTransportException
     *         if it was not possible to connect to the server
     * @throws ezcBaseExtensionNotFoundException
     *         if trying to use SSL and the extension openssl is not installed
     * @throws ezcBasePropertyNotFoundException
     *         if $options contains a property not defined
     * @throws ezcBaseValueException
     *         if $options contains a property with a value not allowed
     * @param string $server
     * @param int $port
     * @param ezcMailImapTransportOptions|array(string=>mixed) $options
     */
    public function __construct( $server, $port = null, $options = array() )
    {
        if ( $options instanceof ezcMailImapTransportOptions )
        {
            $this->options = $options;
        }
        else if ( is_array( $options ) )
        {
            $this->options = new ezcMailImapTransportOptions( $options );
        }
        else
        {
            throw new ezcBaseValueException( "options", $options, "ezcMailImapTransportOptions|array" );
        }

        if ( $port === null )
        {
            $port = ( $this->options->ssl === true ) ? 993 : 143;
        }
        $this->connection = new ezcMailTransportConnection( $server, $port, $this->options );
        // get the server greeting
        $response = $this->connection->getLine();
        if ( strpos( $response, "* OK" ) === false )
        {
            throw new ezcMailTransportException( "The connection to the IMAP server is ok, but a negative response from server was received. Try again later." );
        }
        if ( strpos( $response, self::SERVER_GIMAP ) !== false )
        {
            $this->serverType = self::SERVER_GIMAP; // otherwise it is null
        }
        $this->state = self::STATE_NOT_AUTHENTICATED;
    }

    /**
     * Destructs the IMAP transport.
     *
     * If there is an open connection to the IMAP server it is closed.
     */
    public function __destruct()
    {
        try 
        {
            $this->disconnect();
        }
        catch ( ezcMailTransportException $e )
        {
            // Ignore occuring transport exceptions.
        }
    }

    /**
     * Sets the value of the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     * @throws ezcBaseValueException
     *         if $value is not accepted for the property $name
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'options':
                if ( !( $value instanceof ezcMailImapTransportOptions ) )
                {
                    throw new ezcBaseValueException( 'options', $value, 'instanceof ezcMailImapTransportOptions' );
                }
                $this->options = $value;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     * @param string $name
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'options':
                return $this->options;
            
            default:
                throw new ezcBasePropertyNotFoundException( $name );
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
            case 'options':
                return true;

            default:
                return false;
        }
    }

    /**
     * Disconnects the transport from the IMAP server.
     */
    public function disconnect()
    {
        if ( $this->state !== self::STATE_NOT_CONNECTED
             && $this->connection->isConnected() === true )
        {
            $tag = $this->getNextTag();
            $this->connection->sendData( "{$tag} LOGOUT" );
            // discard the "bye bye" message ("{$tag} OK Logout completed.")
            $this->getResponse( $tag );
            $this->state = self::STATE_LOGOUT;
            $this->selectedMailbox = null;

            $this->connection->close();
            $this->connection = null;
            $this->state = self::STATE_NOT_CONNECTED;
        }
    }

    /**
     * Authenticates the user to the IMAP server with $user and $password.
     *
     * This method should be called directly after the construction of this
     * object.
     *
     * If the server is waiting for the authentication process to respond, the
     * connection with the IMAP server will be closed, and false is returned,
     * and it is the application's task to reconnect and reauthenticate.
     *
     * Example of creating an IMAP transport and authenticating:
     * <code>
     * // replace with your IMAP server address
     * $imap = new ezcMailImapTransport( 'imap.example.com' );
     *
     * // replace the values with your username and password for the IMAP server
     * $imap->authenticate( 'username', 'password' );
     * </code>
     *
     * @throws ezcMailTransportException
     *         if already authenticated
     *         or if the provided username/password combination did not work
     * @param string $user
     * @param string $password
     * @return bool
     */
    public function authenticate( $user, $password )
    {
        if ( $this->state != self::STATE_NOT_AUTHENTICATED )
        {
            throw new ezcMailTransportException( "Tried to authenticate when there was no connection or when already authenticated." );
        }

        $tag = $this->getNextTag();
        $this->connection->sendData( "{$tag} LOGIN {$user} {$password}" );
        $response = trim( $this->connection->getLine() );
        // hack for gmail, to fix issue #15837: imap.google.com (google gmail) changed IMAP response
        if ( $this->serverType === self::SERVER_GIMAP && strpos( $response, "* CAPABILITY" ) === 0 )
        {
            $response = trim( $this->connection->getLine() );
        }
        if ( strpos( $response, '* OK' ) !== false )
        {
            // the server is busy waiting for authentication process to
            // respond, so it is a good idea to just close the connection,
            // otherwise the application will be halted until the server
            // recovers
            $this->connection->close();
            $this->connection = null;
            $this->state = self::STATE_NOT_CONNECTED;
            return false;
        }
        if ( $this->responseType( $response ) != self::RESPONSE_OK )
        {
            throw new ezcMailTransportException( "The IMAP server did not accept the username and/or password: {$response}." );
        }
        else
        {
            $this->state = self::STATE_AUTHENTICATED;
            $this->selectedMailbox = null;
        }
        return true;
    }

    /**
     * Returns an array with the names of the available mailboxes for the user
     * currently authenticated on the IMAP server.
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully.
     *
     * For more information about $reference and $mailbox, consult
     * the IMAP RFCs documents ({@link http://www.faqs.org/rfcs/rfc1730.html}
     * or {@link http://www.faqs.org/rfcs/rfc2060.html}, section 7.2.2.).
     *
     * By default, $reference is "" and $mailbox is "*".
     *
     * The array returned contains the mailboxes available for the connected
     * user on this IMAP server. Inbox is a special mailbox, and it can be
     * specified upper-case or lower-case or mixed-case. The other mailboxes
     * should be specified as they are (to the {@link selectMailbox()} method).
     *
     * Example of listing mailboxes:
     * <code>
     * $imap = new ezcMailImapTransport( 'imap.example.com' );
     * $imap->authenticate( 'username', 'password' );
     *
     * $mailboxes = $imap->listMailboxes();
     * </code>
     *
     * @throws ezcMailMailTransportException
     *         if the current server state is not accepted
     *         or if the server sent a negative response
     * @param string $reference
     * @param string $mailbox
     * @return array(string)
     */
    public function listMailboxes( $reference = '', $mailbox = '*' )
    {
        if ( $this->state != self::STATE_AUTHENTICATED &&
             $this->state != self::STATE_SELECTED &&
             $this->state != self::STATE_SELECTED_READONLY )
        {
            throw new ezcMailTransportException( "Can't call listMailboxes() when not successfully logged in." );
        }

        $result = array();
        $tag = $this->getNextTag();
        $this->connection->sendData( "{$tag} LIST \"{$reference}\" \"{$mailbox}\"" );
        $response = trim( $this->connection->getLine() );
        while ( strpos( $response, '* LIST (' ) !== false )
        {
            // only consider the selectable mailboxes
            if ( strpos( $response, "\\Noselect" ) === false )
            {
                $response = substr( $response, strpos( $response, "\" " ) + 2 );
                $response = trim( $response );
                $response = trim( $response, "\"" );
                $result[] = $response;

            }
            $response = $this->connection->getLine();
        }

        $response = $this->getResponse( $tag, $response );
        if ( $this->responseType( $response ) != self::RESPONSE_OK )
        {
            throw new ezcMailTransportException( "Could not list mailboxes with the parameters '\"{$reference}\"' and '\"{$mailbox}\"': {$response}." );
        }
        return $result;
    }

    /**
     * Returns the hierarchy delimiter of the IMAP server, useful for handling
     * nested IMAP folders.
     *
     * For more information about the hierarchy delimiter, consult the IMAP RFCs
     * {@link http://www.faqs.org/rfcs/rfc1730.html} or
     * {@link http://www.faqs.org/rfcs/rfc2060.html}, section 6.3.8.
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully.
     *
     * Example of returning the hierarchy delimiter:
     * <code>
     * $imap = new ezcMailImapTransport( 'imap.example.com' );
     * $imap->authenticate( 'username', 'password' );
     *
     * $delimiter = $imap->getDelimiter();
     * </code>
     *
     * After running the above code, $delimiter should be something like "/".
     *
     * @throws ezcMailMailTransportException
     *         if the current server state is not accepted
     *         or if the server sent a negative response
     * @return string
     */
    public function getHierarchyDelimiter()
    {
        if ( $this->state != self::STATE_AUTHENTICATED &&
             $this->state != self::STATE_SELECTED &&
             $this->state != self::STATE_SELECTED_READONLY )
        {
            throw new ezcMailTransportException( "Can't call getDelimiter() when not successfully logged in." );
        }

        $tag = $this->getNextTag();
        $this->connection->sendData( "{$tag} LIST \"\" \"\"" );

        // there should be only one * LIST response line from IMAP
        $response = trim( $this->getResponse( '* LIST' ) );
        $parts = explode( '"', $response );

        if ( count( $parts ) >= 2 )
        {
            $result = $parts[1];
        }
        else
        {
            throw new ezcMailTransportException( "Could not retrieve the hierarchy delimiter: {$response}." );
        }

        $response = $this->getResponse( $tag, $response );
        if ( $this->responseType( $response ) != self::RESPONSE_OK )
        {
            throw new ezcMailTransportException( "Could not retrieve the hierarchy delimiter: {$response}." );
        }
        return $result;
    }

    /**
     * Selects the mailbox $mailbox, which will be the active mailbox for the
     * subsequent commands until it is changed.
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully.
     *
     * Inbox is a special mailbox and can be specified with any case.
     *
     * This method should be called after authentication, and before fetching
     * any messages.
     *
     * Example of selecting a mailbox:
     * <code>
     * $imap = new ezcMailImapTransport( 'imap.example.com' );
     * $imap->authenticate( 'username', 'password' );
     *
     * $imap->selectMailbox( 'Reports 2006' );
     * </code>
     *
     * @throws ezcMailMailTransportException
     *         if the current server state is not accepted
     *         or if the server sent a negative response
     * @param string $mailbox
     * @param bool $readOnly
     */
    public function selectMailbox( $mailbox, $readOnly = false )
    {
        if ( $this->state != self::STATE_AUTHENTICATED &&
             $this->state != self::STATE_SELECTED &&
             $this->state != self::STATE_SELECTED_READONLY )
        {
            throw new ezcMailTransportException( "Can't call selectMailbox() when not successfully logged in." );
        }

        $tag = $this->getNextTag();

        // if the mailbox selection will be successful, $state will be STATE_SELECTED
        // or STATE_SELECTED_READONLY, depending on the $readOnly parameter
        if ( $readOnly !== true ) 
        {
            $this->connection->sendData( "{$tag} SELECT \"{$mailbox}\"" );
            $state = self::STATE_SELECTED;
        }
        else
        {
            $this->connection->sendData( "{$tag} EXAMINE \"{$mailbox}\"" );
            $state = self::STATE_SELECTED_READONLY;
        }

        // if the selecting of the mailbox fails (with "NO" or "BAD" response
        // from the server), $state reverts to STATE_AUTHENTICATED
        $response = trim( $this->getResponse( $tag ) );
        if ( $this->responseType( $response ) == self::RESPONSE_OK )
        {
            $this->state = $state;
            $this->selectedMailbox = $mailbox;
        }
        else
        {
            $this->state = self::STATE_AUTHENTICATED;
            $this->selectedMailbox = null;
            throw new ezcMailTransportException( "Could not select mailbox '{$mailbox}': {$response}." );
        }
    }

    /**
     * Creates the mailbox $mailbox.
     *
     * Inbox cannot be created.
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully.
     *
     * @throws ezcMailTransportException
     *         if the current server state is not accepted
     *         or if the server sent a negative response
     * @param string $mailbox
     * @return bool
     */
    public function createMailbox( $mailbox )
    {
        if ( $this->state != self::STATE_AUTHENTICATED &&
             $this->state != self::STATE_SELECTED &&
             $this->state != self::STATE_SELECTED_READONLY )
        {
            throw new ezcMailTransportException( "Can't call createMailbox() when not successfully logged in." );
        }

        $tag = $this->getNextTag();
        $this->connection->sendData( "{$tag} CREATE \"{$mailbox}\"" );
        $response = trim( $this->getResponse( $tag ) );
        if ( $this->responseType( $response ) != self::RESPONSE_OK )
        {
            throw new ezcMailTransportException( "The IMAP server could not create mailbox '{$mailbox}': {$response}." );
        }
        return true;
    }

    /**
     * Renames the mailbox $mailbox to $newName.
     *
     * Inbox cannot be renamed.
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully.
     *
     * @throws ezcMailTransportException
     *         if the current server state is not accepted
     *         or if trying to rename the currently selected mailbox
     *         or if the server sent a negative response
     * @param string $mailbox
     * @param string $newName
     * @return bool
     */
    public function renameMailbox( $mailbox, $newName )
    {
        if ( $this->state != self::STATE_AUTHENTICATED &&
             $this->state != self::STATE_SELECTED &&
             $this->state != self::STATE_SELECTED_READONLY )
        {
            throw new ezcMailTransportException( "Can't call renameMailbox() when not successfully logged in." );
        }

        if ( strtolower( $this->selectedMailbox ) == strtolower( $mailbox ) )
        {
            throw new ezcMailTransportException( "Can't rename the currently selected mailbox." );
        }

        $tag = $this->getNextTag();
        $this->connection->sendData( "{$tag} RENAME \"{$mailbox}\" \"{$newName}\"" );
        $response = trim( $this->getResponse( $tag ) );
        if ( $this->responseType( $response ) != self::RESPONSE_OK )
        {
            throw new ezcMailTransportException( "The IMAP server could not rename the mailbox '{$mailbox}' to '{$newName}': {$response}." );
        }
        return true;
    }

    /**
     * Deletes the mailbox $mailbox.
     *
     * Inbox and the the currently selected mailbox cannot be deleted.
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully.
     *
     * @throws ezcMailTransportException
     *         if the current server state is not accepted
     *         or if trying to delete the currently selected mailbox
     *         or if the server sent a negative response
     * @param string $mailbox
     * @return bool
     */
    public function deleteMailbox( $mailbox )
    {
        if ( $this->state != self::STATE_AUTHENTICATED &&
             $this->state != self::STATE_SELECTED &&
             $this->state != self::STATE_SELECTED_READONLY )
        {
            throw new ezcMailTransportException( "Can't call deleteMailbox() when not successfully logged in." );
        }

        if ( strtolower( $this->selectedMailbox ) == strtolower( $mailbox ) )
        {
            throw new ezcMailTransportException( "Can't delete the currently selected mailbox." );
        }

        $tag = $this->getNextTag();
        $this->connection->sendData( "{$tag} DELETE \"{$mailbox}\"" );
        $response = trim( $this->getResponse( $tag ) );
        if ( $this->responseType( $response ) != self::RESPONSE_OK )
        {
            throw new ezcMailTransportException( "The IMAP server could not delete the mailbox '{$mailbox}': {$response}." );
        }
        return true;
    }

    /** 
     * Copies message(s) from the currently selected mailbox to mailbox
     * $destination.
     *
     * This method supports unique IDs instead of message numbers. See
     * {@link ezcMailImapTransportOptions} for how to enable unique IDs
     * referencing.
     *
     * Warning! When using unique IDs referencing and trying to copy a message
     * with an ID that does not exist, this method will not throw an exception.
     *
     * @todo Find out if it is possible to catch this IMAP bug.
     *
     * $messages can be:
     *  - a single message number (eg: '1')
     *  - a message range (eg. '1:4')
     *  - a message list (eg. '1,2,4')
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully, and a mailbox
     * must be selected (the mailbox from which messages will be copied).
     *
     * Example of copying 3 messages to a mailbox:
     * <code>
     * $imap = new ezcMailImapTransport( 'imap.example.com' );
     * $imap->authenticate( 'username', 'password' );
     * $imap->selectMailbox( 'Inbox' );
     *
     * $imap->copyMessages( '1,2,4', 'Reports 2006' );
     * </code>
     *
     * The above code will copy the messages with numbers 1, 2 and 4 from Inbox
     * to Reports 2006.
     *
     * @throws ezcMailTransportException
     *         if the current server state is not accepted
     *         or if the server sent a negative response
     * @param string $messages
     * @param string $destination
     * @return bool
     */
    public function copyMessages( $messages, $destination )
    {
        $uid = ( $this->options->uidReferencing ) ? self::UID : self::NO_UID;

        if ( $this->state != self::STATE_SELECTED &&
             $this->state != self::STATE_SELECTED_READONLY )
        {
            throw new ezcMailTransportException( "Can't call copyMessages() on the IMAP transport when a mailbox is not selected." );
        }
    
        $tag = $this->getNextTag();
        $this->connection->sendData( "{$tag} {$uid}COPY {$messages} \"{$destination}\"" );
        
        $response = trim( $this->getResponse( $tag ) );
        if ( $this->responseType( $response ) != self::RESPONSE_OK )
        {
            throw new ezcMailTransportException( "The IMAP server could not copy '{$messages}' to '{$destination}': {$response}." );
        }
        return true;
    }

    /**
     * Returns a list of the not deleted messages in the current mailbox.
     *
     * It returns only the messages with the flag DELETED not set.
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully, and a mailbox
     * must be selected.
     *
     * The format of the returned array is
     * <code>
     *   array( message_id => size );
     * </code>
     *
     * Example:
     * <code>
     *   array( 2 => 1700, 5 => 1450, 6 => 21043 );
     * </code>
     *
     * If $contentType is set, it returns only the messages with
     * $contentType in the Content-Type header.
     *
     * For example $contentType can be "multipart/mixed" to return only the
     * messages with attachments.
     *
     * @throws ezcMailTransportException
     *         if a mailbox is not selected
     *         or if the server sent a negative response
     * @param string $contentType
     * @return array(int)
     */
    public function listMessages( $contentType = null )
    {
        if ( $this->state != self::STATE_SELECTED &&
             $this->state != self::STATE_SELECTED_READONLY )
        {
            throw new ezcMailTransportException( "Can't call listMessages() on the IMAP transport when a mailbox is not selected." );
        }

        $messageList = array();
        $messages = array();
 
        // get the numbers of the existing messages
        $tag = $this->getNextTag();
        $command = "{$tag} SEARCH UNDELETED";
        if ( !is_null( $contentType ) )
        {
            $command .= " HEADER \"Content-Type\" \"{$contentType}\"";
        }
        $this->connection->sendData( $command );
        $response = trim( $this->getResponse( '* SEARCH' ) );
        if ( strpos( $response, '* SEARCH' ) !== false )
        {
            $ids = trim( substr( $response, 9 ) );
            if ( $ids !== "" )
            {
                $messageList = explode( ' ', $ids );
            }
        }
        // skip the OK response ("{$tag} OK Search completed.")
        $response = trim( $this->getResponse( $tag, $response ) );
        if ( $this->responseType( $response ) != self::RESPONSE_OK )
        {
            throw new ezcMailTransportException( "The IMAP server could not list messages: {$response}." );
        }

        if ( !empty( $messageList ) )
        {
            // get the sizes of the messages
            $tag = $this->getNextTag();
            $query = trim( implode( ',', $messageList ) );
            $this->connection->sendData( "{$tag} FETCH {$query} RFC822.SIZE" );
            $response = $this->getResponse( 'FETCH (' );
            $currentMessage = trim( reset( $messageList ) );
            while ( strpos( $response, 'FETCH (' ) !== false )
            {
                $line = $response;
                $line = explode( ' ', $line );
                $line = trim( $line[count( $line ) - 1] );
                $line = substr( $line, 0, strlen( $line ) - 1 );
                $messages[$currentMessage] = intval( $line );
                $currentMessage = next( $messageList );
                $response = $this->connection->getLine();
            }
            // skip the OK response ("{$tag} OK Fetch completed.")
            $response = trim( $this->getResponse( $tag, $response ) );
            if ( $this->responseType( $response ) != self::RESPONSE_OK )
            {
                throw new ezcMailTransportException( "The IMAP server could not list messages: {$response}." );
            }
        }
        return $messages;
    }

    /**
     * Fetches the sizes in bytes for messages $messages.
     *
     * This method supports unique IDs instead of message numbers. See
     * {@link ezcMailImapTransportOptions} for how to enable unique IDs
     * referencing.
     *
     * $messages is an array of message numbers, for example:
     * <code>
     *   array( 1, 2, 4 );
     * </code>
     *
     * The format of the returned array is:
     * <code>
     *   array( message_number => size )
     * </code>
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully, and a mailbox
     * must be selected.
     *
     * Example:
     * <code>
     * $imap = new ezcMailImapTransport( 'imap.example.com' );
     * $imap->authenticate( 'username', 'password' );
     * $imap->selectMailbox( 'mailbox' ); // Inbox or another mailbox
     *
     * $sizes = $imap->fetchSizes( array( 1, 2, 4 ) );
     * </code>
     *
     * The returned array $sizes will be something like:
     * <code>
     *   array( 1 => 1043,
     *          2 => 203901,
     *          4 => 14277
     *        );
     * </code>
     *
     * @throws ezcMailTransportException
     *         if a mailbox is not selected
     *         or if the server sent a negative response
     * @param array $messages
     * @return array(int)
     */
    public function fetchSizes( $messages )
    {
        $uid = ( $this->options->uidReferencing ) ? self::UID : self::NO_UID;

        if ( $this->state != self::STATE_SELECTED &&
             $this->state != self::STATE_SELECTED_READONLY )
        {
            throw new ezcMailTransportException( "Can't call fetchSizes() on the IMAP transport when a mailbox is not selected." );
        }

        $sizes = array();
        $ids = implode( $messages, ',' );

        $tag = $this->getNextTag();
        $this->connection->sendData( "{$tag} {$uid}FETCH {$ids} (RFC822.SIZE)" );

        $response = trim( $this->connection->getLine() );
        while ( strpos( $response, $tag ) === false )
        {
            if ( strpos( $response, ' FETCH (' ) !== false )
            {
                if ( $this->options->uidReferencing )
                {
                    preg_match( '/\*\s.*\sFETCH\s\(RFC822\.SIZE\s(.*)\sUID\s(.*)\)/U', $response, $matches );
                    $sizes[intval( $matches[2] )] = (int) $matches[1];
                }
                else
                {
                    preg_match( '/\*\s(.*)\sFETCH\s\(RFC822\.SIZE\s(.*)\)/U', $response, $matches );
                    $sizes[intval( $matches[1] )] = (int) $matches[2];
                }

            }
            $response = trim( $this->connection->getLine() );
        }

        if ( $this->responseType( $response ) != self::RESPONSE_OK )
        {
            throw new ezcMailTransportException( "The IMAP server could not fetch flags for the messages '{$messages}': {$response}." );
        }
        return $sizes;
    }

    /**
     * Returns information about the messages in the current mailbox.
     *
     * The information returned through the parameters is:
     *  - $numMessages = number of not deleted messages in the selected mailbox
     *  - $sizeMessages = sum of the not deleted messages sizes
     *  - $recent = number of recent and not deleted messages
     *  - $unseen = number of unseen and not deleted messages
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully, and a mailbox
     * must be selected.
     *
     * Example of returning the status of the currently selected mailbox:
     * <code>
     * $imap = new ezcMailImapTransport( 'imap.example.com' );
     * $imap->authenticate( 'username', 'password' );
     * $imap->selectMailbox( 'Inbox' );
     *
     * $imap->status( $numMessages, $sizeMessages, $recent, $unseen );
     * </code>
     *
     * After running the above code, $numMessages, $sizeMessages, $recent
     * and $unseen will be populated with values.
     *
     * @throws ezcMailTransportException
     *         if a mailbox is not selected
     *         or if the server sent a negative response
     * @param int &$numMessages
     * @param int &$sizeMessages
     * @param int &$recent
     * @param int &$unseen
     * @return bool
     */
    public function status( &$numMessages, &$sizeMessages, &$recent = 0, &$unseen = 0 )
    {
        if ( $this->state != self::STATE_SELECTED &&
             $this->state != self::STATE_SELECTED_READONLY )
        {
            throw new ezcMailTransportException( "Can't call status() on the IMAP transport when a mailbox is not selected." );
        }
        $messages = $this->listMessages();
        $numMessages = count( $messages );
        $sizeMessages = array_sum( $messages );
        $messages = array_keys( $messages );
        $recentMessages = array_intersect( $this->searchByFlag( "RECENT" ), $messages );
        $unseenMessages = array_intersect( $this->searchByFlag( "UNSEEN" ), $messages );
        $recent = count( $recentMessages );
        $unseen = count( $unseenMessages );
        return true;
    }

    /**
     * Deletes the message with the message number $msgNum from the current mailbox.
     *
     * This method supports unique IDs instead of message numbers. See
     * {@link ezcMailImapTransportOptions} for how to enable unique IDs
     * referencing.
     *
     * The message number $msgNum must be a valid identifier fetched with e.g.
     * {@link listMessages()}.
     *
     * The message is not physically deleted, but has its DELETED flag set,
     * and can be later undeleted by clearing its DELETED flag with
     * {@link clearFlag()}.
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully, and a mailbox
     * must be selected.
     *
     * @throws ezcMailTransportException
     *         if a mailbox is not selected
     *         or if the server sent a negative response
     * @param int $msgNum
     * @return bool
     */
    public function delete( $msgNum )
    {
        $uid = ( $this->options->uidReferencing ) ? self::UID : self::NO_UID;

        if ( $this->state != self::STATE_SELECTED )
        {
            throw new ezcMailTransportException( "Can't call delete() when a mailbox is not selected." );
        }
        $tag = $this->getNextTag();
        $this->connection->sendData( "{$tag} {$uid}STORE {$msgNum} +FLAGS (\\Deleted)" );

        // get the response (should be "{$tag} OK Store completed.")
        $response = trim( $this->getResponse( $tag ) );
        if ( $this->responseType( $response ) != self::RESPONSE_OK )
        {
            throw new ezcMailTransportException( "The IMAP server could not delete the message '{$msgNum}': {$response}." );
        }
        return true;
    }

    /**
     * Returns the headers and the first characters from message $msgNum,
     * without setting the SEEN flag.
     *
     * This method supports unique IDs instead of message numbers. See
     * {@link ezcMailImapTransportOptions} for how to enable unique IDs
     * referencing.
     *
     * If the command failed or if it was not supported by the server an empty
     * string is returned.
     *
     * This method is useful for retrieving the headers of messages from the
     * mailbox as strings, which can be later parsed with {@link ezcMailParser}
     * and {@link ezcMailVariableSet}. In this way the retrieval of the full
     * messages from the server is avoided when building a list of messages.
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully, and a mailbox
     * must be selected.
     *
     * Example of listing the mail headers of all the messages in the current
     * mailbox:
     * <code>
     * $imap = new ezcMailImapTransport( 'imap.example.com' );
     * $imap->authenticate( 'username', 'password' );
     * $imap->selectMailbox( 'Inbox' );
     *
     * $parser = new ezcMailParser();
     * $messages = $imap->listMessages();
     * foreach ( $messages as $messageNr => $size )
     * {
     *     $set = new ezcMailVariableSet( $imap->top( $messageNr ) );
     *     $mail = $parser->parseMail( $set );
     *     $mail = $mail[0];
     *     echo "From: {$mail->from}, Subject: {$mail->subject}, Size: {$size}\n";
     * }
     * </code>
     *
     * For a more advanced example see the "Mail listing example" in the online
     * documentation.
     *
     * @throws ezcMailTransportException
     *         if a mailbox is not selected
     *         or if the server sent a negative response
     * @param int $msgNum
     * @param int $chars
     * @return string
     */
    public function top( $msgNum, $chars = 0 )
    {
        $uid = ( $this->options->uidReferencing ) ? self::UID : self::NO_UID;

        if ( $this->state != self::STATE_SELECTED &&
             $this->state != self::STATE_SELECTED_READONLY )
        {
            throw new ezcMailTransportException( "Can't call top() on the IMAP transport when a mailbox is not selected." );
        }

        $tag = $this->getNextTag();

        if ( $chars === 0 )
        {
            $command = "{$tag} {$uid}FETCH {$msgNum} (BODY.PEEK[HEADER] BODY.PEEK[TEXT])";
        }
        else
        {
            $command = "{$tag} {$uid}FETCH {$msgNum} (BODY.PEEK[HEADER] BODY.PEEK[TEXT]<0.{$chars}>)";
        }
        $this->connection->sendData( $command );
        if ( $this->options->uidReferencing )
        {
            // special case (BUG?) where "UID FETCH {$msgNum}" returns nothing
            $response = trim( $this->connection->getLine() );
            if ( $this->responseType( $response ) === self::RESPONSE_OK )
            {
                throw new ezcMailTransportException( "The IMAP server could not fetch the message '{$msgNum}': {$response}." );
            }
        }
        else
        {
            $response = $this->getResponse( 'FETCH (' );
        }
        $message = "";
        if ( strpos( $response, 'FETCH (' ) !== false )
        {
            // Added hack for issue #14360: problems with $imap->top() command in gmail.
            if ( $this->serverType === self::SERVER_GIMAP )
            {
                // Google IMAP servers return the body first, then the headers (!)
                $bytesToRead = $this->getMessageSectionSize( $response );
                $response = "";
                while ( $bytesToRead >= 0 )
                {
                    $data = $this->connection->getLine();
                    $lastResponse = $data;
                    $bytesToRead -= strlen( $data );

                    // in case reading too much and the string "BODY[HEADER] {size}"
                    // is at the end of the last line
                    if ( $bytesToRead <= 0 )
                    {
                        if ( $bytesToRead < 0 )
                        {
                            $lastResponse = substr( $data, $bytesToRead );
                            $data = substr( $data, 0, strlen( $data ) + $bytesToRead );
                        }
                    }
                    $message .= $data;
                }

                // Read the headers
                $headers = '';
                $response = $this->connection->getLine();
                $bytesToRead = $this->getMessageSectionSize( $lastResponse );

                $response = $this->connection->getLine();
                while ( strpos( $response, $tag ) === false )
                {
                    $headers .= $response;
                    $response = $this->connection->getLine();
                }
                $headers = trim( $headers, ")\r\n" );

                // Append the body AFTER the headers as it should be
                $message = $headers . "\r\n\r\n" . $message;
            }
            else
            {
                // Other IMAP servers return the headers first, then the body
                $response = "";
                while ( strpos( $response, 'BODY[TEXT]' ) === false )
                {
                    $message .= $response;
                    $response = $this->connection->getLine();
                }

                $response = $this->connection->getLine();
                while ( strpos( $response, $tag ) === false )
                {
                    $message .= $response;
                    $response = $this->connection->getLine();
                }
            }
        }
        // skip the OK response ("{$tag} OK Fetch completed.")
        $response = trim( $this->getResponse( $tag, $response ) );
        if ( $this->responseType( $response ) != self::RESPONSE_OK )
        {
            throw new ezcMailTransportException( "The IMAP server could not fetch the message '{$msgNum}': {$response}." );
        }
        return $message;
    }

    /**
     * Returns the unique identifiers for the messages from the current mailbox.
     *
     * You can fetch the unique identifier for a specific message by
     * providing the $msgNum parameter.
     *
     * The unique identifier can be used to recognize mail from servers
     * between requests. In contrast to the message numbers the unique
     * numbers assigned to an email usually never changes.
     *
     * The format of the returned array is:
     * <code>
     *   array( message_num => unique_id );
     * </code>
     *
     * Example:
     * <code>
     *   array( 1 => 216, 2 => 217, 3 => 218, 4 => 219 );
     * </code>
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully, and a mailbox
     * must be selected.
     *
     * @todo add UIVALIDITY value to UID (like in POP3) (if necessary).
     *
     * @throws ezcMailTransportException
     *         if a mailbox is not selected
     *         or if the server sent a negative response
     * @param int $msgNum
     * @return array(string)
     */
    public function listUniqueIdentifiers( $msgNum = null )
    {
        if ( $this->state != self::STATE_SELECTED &&
             $this->state != self::STATE_SELECTED_READONLY )
        {
            throw new ezcMailTransportException( "Can't call listUniqueIdentifiers() on the IMAP transport when a mailbox is not selected." );
        }

        $result = array();
        if ( $msgNum !== null )
        {
            $tag = $this->getNextTag();
            $this->connection->sendData( "{$tag} UID SEARCH {$msgNum}" );
            $response = $this->getResponse( '* SEARCH' );
            if ( strpos( $response, '* SEARCH' ) !== false )
            {
                $result[(int)$msgNum] = trim( substr( $response, 9 ) );
            }
            $response = trim( $this->getResponse( $tag, $response ) );
        }
        else
        {
            $uids = array();
            $messages = array_keys( $this->listMessages() );
            $tag = $this->getNextTag();
            $this->connection->sendData( "{$tag} UID SEARCH UNDELETED" );
            $response = $this->getResponse( '* SEARCH' );
            if ( strpos( $response, '* SEARCH' ) !== false )
            {
                $response = trim( substr( $response, 9 ) );
                if ( $response !== "" )
                {
                    $uids = explode( ' ', $response );
                }
                for ( $i = 0; $i < count( $messages ); $i++ )
                {
                    $result[trim( $messages[$i] )] = $uids[$i];
                }
            }
            $response = trim( $this->getResponse( $tag ) );
        }
        if ( $this->responseType( $response ) != self::RESPONSE_OK )
        {
            throw new ezcMailTransportException( "The IMAP server could not fetch the unique identifiers: {$response}." );
        }
        return $result;
    }

    /**
     * Returns an {@link ezcMailImapSet} with all the messages from the current mailbox.
     *
     * This method supports unique IDs instead of message numbers. See
     * {@link ezcMailImapTransportOptions} for how to enable unique IDs
     * referencing.
     *
     * If $deleteFromServer is set to true the mail will be marked for deletion
     * after retrieval. If not it will be left intact.
     *
     * The set returned can be parsed with {@link ezcMailParser}.
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully, and a mailbox
     * must be selected.
     *
     * Example:
     * <code>
     * $imap = new ezcMailImapTransport( 'imap.example.com' );
     * $imap->authenticate( 'username', 'password' );
     * $imap->selectMailbox( 'Inbox' );
     *
     * $set = $imap->fetchAll();
     *
     * // parse $set with ezcMailParser
     * $parser = new ezcMailParser();
     * $mails = $parser->parseMail( $set );
     * foreach ( $mails as $mail )
     * {
     *     // process $mail which is an ezcMail object
     * }
     * </code>
     *
     * @throws ezcMailTransportException
     *         if a mailbox is not selected
     *         or if the server sent a negative response
     * @param bool $deleteFromServer
     * @return ezcMailParserSet
     */
    public function fetchAll( $deleteFromServer = false )
    {
        if ( $this->options->uidReferencing )
        {
            $messages = array_values( $this->listUniqueIdentifiers() );
        }
        else
        {
            $messages = array_keys( $this->listMessages() );
        }

        return new ezcMailImapSet( $this->connection, $messages, $deleteFromServer, array( 'uidReferencing' => $this->options->uidReferencing ) );
    }

    /**
     * Returns an {@link ezcMailImapSet} containing only the $number -th message in
     * the current mailbox.
     *
     * This method supports unique IDs instead of message numbers. See
     * {@link ezcMailImapTransportOptions} for how to enable unique IDs
     * referencing.
     *
     * If $deleteFromServer is set to true the mail will be marked for deletion
     * after retrieval. If not it will be left intact.
     *
     * Note: for IMAP the first message is 1 (so for $number = 0 an exception
     * will be thrown).
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully, and a mailbox
     * must be selected.
     *
     * Example:
     * <code>
     * $imap = new ezcMailImapTransport( 'imap.example.com' );
     * $imap->authenticate( 'username', 'password' );
     * $imap->selectMailbox( 'Inbox' );
     *
     * $set = $imap->fetchByMessageNr( 1 );
     *
     * // $set can be parsed with ezcMailParser
     * </code>
     *
     * @throws ezcMailTransportException
     *         if a mailbox is not selected
     *         or if the server sent a negative response
     * @throws ezcMailNoSuchMessageException
     *         if the message $number is out of range
     * @param int $number
     * @param bool $deleteFromServer
     * @return ezcMailImapSet
     */
    public function fetchByMessageNr( $number, $deleteFromServer = false )
    {
        if ( $this->options->uidReferencing )
        {
            $messages = array_flip( $this->listUniqueIdentifiers() );
        }
        else
        {
            $messages = $this->listMessages();
        }

        if ( !isset( $messages[$number] ) )
        {
            throw new ezcMailNoSuchMessageException( $number );
        }

        return new ezcMailImapSet( $this->connection, array( 0 => $number ), $deleteFromServer, array( 'uidReferencing' => $this->options->uidReferencing ) );
    }

    /**
     * Returns an {@link ezcMailImapSet} with $count messages starting from $offset from
     * the current mailbox.
     *
     * This method supports unique IDs instead of message numbers. See
     * {@link ezcMailImapTransportOptions} for how to enable unique IDs
     * referencing.
     *
     * Fetches $count messages starting from the $offset and returns them as a
     * {@link ezcMailImapSet}. If $count is not specified or if it is 0, it fetches
     * all messages starting from the $offset.
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully, and a mailbox
     * must be selected.
     *
     * Example:
     * <code>
     * $imap = new ezcMailImapTransport( 'imap.example.com' );
     * $imap->authenticate( 'username', 'password' );
     * $imap->selectMailbox( 'Inbox' );
     *
     * $set = $imap->fetchFromOffset( 1, 10 );
     *
     * // $set can be parsed with ezcMailParser
     * </code>
     *
     * @throws ezcMailTransportException
     *         if a mailbox is not selected
     *         or if the server sent a negative response
     * @throws ezcMailInvalidLimitException
     *         if $count is negative
     * @throws ezcMailOffsetOutOfRangeException
     *         if $offset is outside of the existing range of messages
     * @param int $offset
     * @param int $count
     * @param bool $deleteFromServer
     * @return ezcMailImapSet
     */
    public function fetchFromOffset( $offset, $count = 0, $deleteFromServer = false )
    {
        if ( $count < 0 )
        {
            throw new ezcMailInvalidLimitException( $offset, $count );
        }

        if ( $this->options->uidReferencing )
        {
            $messages = array_values( $this->listUniqueIdentifiers() );
            $ids = array_flip( $messages );

            if ( $count === 0 )
            {
                $count = count( $messages );
            }

            if ( !isset( $ids[$offset] ) )
            {
                throw new ezcMailOffsetOutOfRangeException( $offset, $count );
            }

            $range = array();
            for ( $i = $ids[$offset]; $i < min( $count, count( $messages ) ); $i++ )
            {
                $range[] = $messages[$i];
            }
        }
        else
        {
            $messages = array_keys( $this->listMessages() );

            if ( $count === 0 )
            {
                $count = count( $messages );
            }

            $range = array_slice( $messages, $offset - 1, $count, true );

            if ( !isset( $range[$offset - 1] ) )
            {
                throw new ezcMailOffsetOutOfRangeException( $offset, $count );
            }
        }

        return new ezcMailImapSet( $this->connection, $range, $deleteFromServer, array( 'uidReferencing' => $this->options->uidReferencing ) );
    }

    /**
     * Returns an {@link ezcMailImapSet} containing the messages which match the
     * provided $criteria from the current mailbox.
     *
     * This method supports unique IDs instead of message numbers. See
     * {@link ezcMailImapTransportOptions} for how to enable unique IDs
     * referencing.
     *
     * See {@link http://www.faqs.org/rfcs/rfc1730.html} - 6.4.4. (or
     * {@link http://www.faqs.org/rfcs/rfc1730.html} - 6.4.4.) for criterias
     * which can be used for searching. The criterias can be combined in the
     * same search string (separate the criterias with spaces).
     *
     * If $criteria is null or empty then it will default to 'ALL' (returns all
     * messages in the mailbox).
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully, and a mailbox
     * must be selected.
     *
     * Examples:
     * <code>
     * $imap = new ezcMailImapTransport( 'imap.example.com' );
     * $imap->authenticate( 'username', 'password' );
     * $imap->selectMailbox( 'mailbox' ); // Inbox or another mailbox
     *
     * // return an ezcMailImapSet containing all messages flagged as 'SEEN'
     * $set = $imap->searchMailbox( 'SEEN' );
     *
     * // return an ezcMailImapSet containing messages with 'release' in their Subject
     * $set = $imap->searchMailbox( 'SUBJECT "release"' );
     *
     * // criterias can be combined:
     * // return an ezcMailImapSet containing messages flagged as 'SEEN' and
     * // with 'release' in their Subject
     * $set = $imap->searchMailbox( 'SEEN SUBJECT "release"' );
     *
     * // $set can be parsed with ezcMailParser
     * </code>
     *
     * @throws ezcMailTransportException
     *         if a mailbox is not selected
     *         or if the server sent a negative response
     * @param string $criteria
     * @return ezcMailImapSet
     */
    public function searchMailbox( $criteria = null )
    {
        $uid = ( $this->options->uidReferencing ) ? self::UID : self::NO_UID;

        if ( $this->state != self::STATE_SELECTED &&
             $this->state != self::STATE_SELECTED_READONLY )
        {
            throw new ezcMailTransportException( "Can't call searchMailbox() on the IMAP transport when a mailbox is not selected." );
        }

        $criteria = trim( $criteria );
        if ( empty( $criteria ) )
        {
            $criteria = 'ALL';
        }

        $matchingMessages = array();
        $tag = $this->getNextTag();
        $this->connection->sendData( "{$tag} {$uid}SEARCH {$criteria}" );

        $response = $this->getResponse( '* SEARCH' );
        if ( strpos( $response, '* SEARCH' ) !== false )
        {
            $ids = substr( trim( $response ), 9 );
            if ( trim( $ids ) !== "" )
            {
                $matchingMessages = explode( ' ', $ids );
            }
        }

        $response = trim( $this->getResponse( $tag, $response ) );
        if ( $this->responseType( $response ) != self::RESPONSE_OK )
        {
            throw new ezcMailTransportException( "The IMAP server could not search the messages by the specified criteria: {$response}." );
        }

        return new ezcMailImapSet( $this->connection, array_values( $matchingMessages ), false, array( 'uidReferencing' => $this->options->uidReferencing ) );
    }

    /**
     * Returns an {@link ezcMailImapSet} containing $count messages starting
     * from $offset sorted by $sortCriteria from the current mailbox.
     *
     * This method supports unique IDs instead of message numbers. See
     * {@link ezcMailImapTransportOptions} for how to enable unique IDs
     * referencing.
     *
     * It is useful for paging through a mailbox.
     *
     * Fetches $count messages starting from the $offset and returns them as a
     * {@link ezcMailImapSet}. If $count is is 0, it fetches all messages
     * starting from the $offset.
     *
     * $sortCriteria is an email header like: Subject, To, From, Date, Sender, etc.
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully, and a mailbox
     * must be selected.
     *
     * Example:
     * <code>
     * $imap = new ezcMailImapTransport( 'imap.example.com' );
     * $imap->authenticate( 'username', 'password' );
     * $imap->selectMailbox( 'mailbox' ); // Inbox or another mailbox
     *
     * // Fetch a range of messages sorted by Date
     * $set = $imap->sortFromOffset( 1, 10, "Date" );
     *
     * // $set can be parsed with ezcMailParser
     * </code>
     *
     * @throws ezcMailTransportException
     *         if a mailbox is not selected
     *         or if the server sent a negative response
     * @throws ezcMailInvalidLimitException
     *         if $count is negative
     * @throws ezcMailOffsetOutOfRangeException
     *         if $offset is outside of the existing range of messages
     * @param int $offset
     * @param int $count
     * @param string $sortCriteria
     * @param bool $reverse
     * @return ezcMailImapSet
     */
    public function sortFromOffset( $offset, $count = 0, $sortCriteria, $reverse = false )
    {
        if ( $count < 0 )
        {
            throw new ezcMailInvalidLimitException( $offset, $count );
        }

        $range = array();
        if ( $this->options->uidReferencing )
        {
            $uids = array_values( $this->listUniqueIdentifiers() );

            $flip = array_flip( $uids );
            if ( !isset( $flip[$offset] ) )
            {
                throw new ezcMailOffsetOutOfRangeException( $offset, $count );
            }

            $start = $flip[$offset];

            $messages = $this->sort( $uids, $sortCriteria, $reverse );

            if ( $count === 0 )
            {
                $count = count( $messages );
            }

            $ids = array_keys( $messages );

            for ( $i = $start; $i < $count; $i++ )
            {
                $range[] = $ids[$i];
            }
        }
        else
        {
            $messageCount = $this->countByFlag( 'ALL' );
            $messages = array_keys( $this->sort( range( 1, $messageCount ), $sortCriteria, $reverse ) );

            if ( $count === 0 )
            {
                $count = count( $messages );
            }

            $range = array_slice( $messages, $offset - 1, $count, true );

            if ( !isset( $range[$offset - 1] ) )
            {
                throw new ezcMailOffsetOutOfRangeException( $offset, $count );
            }
        }

        return new ezcMailImapSet( $this->connection, $range, false, array( 'uidReferencing' => $this->options->uidReferencing ) );
    }

    /**
     * Returns an {@link ezcMailImapSet} containing messages $messages sorted by
     * $sortCriteria from the current mailbox.
     *
     * This method supports unique IDs instead of message numbers. See
     * {@link ezcMailImapTransportOptions} for how to enable unique IDs
     * referencing.
     *
     * $messages is an array of message numbers, for example:
     * <code>
     *   array( 1, 2, 4 );
     * </code>
     *
     * $sortCriteria is an email header like: Subject, To, From, Date, Sender, etc.
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully, and a mailbox
     * must be selected.
     *
     * Example:
     * <code>
     * $imap = new ezcMailImapTransport( 'imap.example.com' );
     * $imap->authenticate( 'username', 'password' );
     * $imap->selectMailbox( 'mailbox' ); // Inbox or another mailbox
     *
     * // Fetch the list of messages sorted by Date
     * $set = $imap->sortMessages( 1, 10, "Date" );
     *
     * // $set can be parsed with ezcMailParser
     * </code>
     *
     * @throws ezcMailTransportException
     *         if a mailbox is not selected
     *         or if the server sent a negative response
     *         or if array $messages is empty
     * @param array(int) $messages
     * @param string $sortCriteria
     * @param bool $reverse
     * @return ezcMailImapSet
     */
    public function sortMessages( $messages, $sortCriteria, $reverse = false )
    {
        $messages = $this->sort( $messages, $sortCriteria, $reverse );
        return new ezcMailImapSet( $this->connection, array_keys ( $messages ), false, array( 'uidReferencing' => $this->options->uidReferencing ) );
    }

    /**
     * Returns an {@link ezcMailImapSet} containing messages with a certain flag from
     * the current mailbox.
     *
     * This method supports unique IDs instead of message numbers. See
     * {@link ezcMailImapTransportOptions} for how to enable unique IDs
     * referencing.
     *
     * $flag can be one of:
     *
     * Basic flags:
     *  - ANSWERED   - message has been answered
     *  - DELETED    - message is marked to be deleted by later EXPUNGE
     *  - DRAFT      - message is marked as a draft
     *  - FLAGGED    - message is "flagged" for urgent/special attention
     *  - RECENT     - message is recent
     *  - SEEN       - message has been read
     *
     * Opposites of the above flags:
     *  - UNANSWERED
     *  - UNDELETED
     *  - UNDRAFT
     *  - UNFLAGGED
     *  - OLD
     *  - UNSEEN
     *
     * Composite flags:
     *  - NEW        - equivalent to RECENT + UNSEEN
     *  - ALL        - all the messages
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully, and a mailbox
     * must be selected.
     *
     * Example:
     * <code>
     * $imap = new ezcMailImapTransport( 'imap.example.com' );
     * $imap->authenticate( 'username', 'password' );
     * $imap->selectMailbox( 'mailbox' ); // Inbox or another mailbox
     *
     * // Fetch the messages marked with the RECENT flag
     * $set = $imap->fetchByFlag( 'RECENT' );
     *
     * // $set can be parsed with ezcMailParser
     * </code>
     *
     * @throws ezcMailTransportException
     *         if a mailbox is not selected
     *         or if the server sent a negative response
     *         or if $flag is not valid
     * @param string $flag
     * @return ezcMailImapSet
     */
    public function fetchByFlag( $flag )
    {
        $messages = $this->searchByFlag( $flag );
        return new ezcMailImapSet( $this->connection, $messages, false, array( 'uidReferencing' => $this->options->uidReferencing ) );
    }

    /**
     * Wrapper function to fetch count of messages by a certain flag.
     *
     * $flag can be one of:
     *
     * Basic flags:
     *  - ANSWERED   - message has been answered
     *  - DELETED    - message is marked to be deleted by later EXPUNGE
     *  - DRAFT      - message is marked as a draft
     *  - FLAGGED    - message is "flagged" for urgent/special attention
     *  - RECENT     - message is recent
     *  - SEEN       - message has been read
     *
     * Opposites of the above flags:
     *  - UNANSWERED
     *  - UNDELETED
     *  - UNDRAFT
     *  - UNFLAGGED
     *  - OLD
     *  - UNSEEN
     *
     * Composite flags:
     *  - NEW        - equivalent to RECENT + UNSEEN
     *  - ALL        - all the messages
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully, and a mailbox
     * must be selected.
     *
     * @throws ezcMailTransportException
     *         if a mailbox is not selected
     *         or if the server sent a negative response
     *         or if $flag is not valid
     * @param string $flag
     * @return int
     */
    public function countByFlag( $flag )
    {
        $flag = $this->normalizeFlag( $flag );
        $messages = $this->searchByFlag( $flag );
        return count( $messages );
    }

    /**
     * Fetches IMAP flags for messages $messages.
     *
     * This method supports unique IDs instead of message numbers. See
     * {@link ezcMailImapTransportOptions} for how to enable unique IDs
     * referencing.
     *
     * $messages is an array of message numbers, for example:
     * <code>
     *   array( 1, 2, 4 );
     * </code>
     *
     * The format of the returned array is:
     * <code>
     *   array( message_number => array( flags ) )
     * </code>
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully, and a mailbox
     * must be selected.
     *
     * Example:
     * <code>
     * $imap = new ezcMailImapTransport( 'imap.example.com' );
     * $imap->authenticate( 'username', 'password' );
     * $imap->selectMailbox( 'mailbox' ); // Inbox or another mailbox
     *
     * $flags = $imap->fetchFlags( array( 1, 2, 4 ) );
     * </code>
     *
     * The returned array $flags will be something like:
     * <code>
     *   array( 1 => array( '\Seen' ),
     *          2 => array( '\Seen' ),
     *          4 => array( '\Seen', 'NonJunk' )
     *        );
     * </code>
     *
     * @throws ezcMailTransportException
     *         if a mailbox is not selected
     *         or if the server sent a negative response
     * @param array $messages
     * @return array(mixed)
     */
    public function fetchFlags( $messages )
    {
        $uid = ( $this->options->uidReferencing ) ? self::UID : self::NO_UID;

        if ( $this->state != self::STATE_SELECTED &&
             $this->state != self::STATE_SELECTED_READONLY )
        {
            throw new ezcMailTransportException( "Can't call fetchFlags() on the IMAP transport when a mailbox is not selected." );
        }

        $flags = array();
        $ids = implode( $messages, ',' );

        $tag = $this->getNextTag();
        $this->connection->sendData( "{$tag} {$uid}FETCH {$ids} (FLAGS)" );

        $response = trim( $this->connection->getLine() );
        while ( strpos( $response, $tag ) === false )
        {
            if ( strpos( $response, ' FETCH (' ) !== false )
            {
                if ( $this->options->uidReferencing )
                {
                    preg_match( '/\*\s.*\sFETCH\s\(FLAGS \((.*)\)\sUID\s(.*)\)/U', $response, $matches );
                    $parts = explode( ' ', $matches[1] );
                    $flags[intval( $matches[2] )] = $parts;
                }
                else
                {
                    preg_match( '/\*\s(.*)\sFETCH\s\(FLAGS \((.*)\)/U', $response, $matches );
                    $parts = explode( ' ', $matches[2] );
                    $flags[intval( $matches[1] )] = $parts;
                }
            }
            $response = trim( $this->connection->getLine() );
        }

        if ( $this->responseType( $response ) != self::RESPONSE_OK )
        {
            throw new ezcMailTransportException( "The IMAP server could not fetch flags for the messages '{$messages}': {$response}." );
        }
        return $flags;
    }

    /**
     * Sets $flag on $messages.
     *
     * This method supports unique IDs instead of message numbers. See
     * {@link ezcMailImapTransportOptions} for how to enable unique IDs
     * referencing.
     *
     * $messages can be:
     *  - a single message number (eg. 1)
     *  - a message range (eg. 1:4)
     *  - a message list (eg. 1,2,4)
     *
     * $flag can be one of:
     *  - ANSWERED   - message has been answered
     *  - DELETED    - message is marked to be deleted by later EXPUNGE
     *  - DRAFT      - message is marked as a draft
     *  - FLAGGED    - message is "flagged" for urgent/special attention
     *  - SEEN       - message has been read
     *
     * This function automatically adds the '\' in front of the flag when
     * calling the server command.
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully, and a mailbox
     * must be selected.
     *
     * Example:
     * <code>
     * $imap = new ezcMailImapTransport( 'imap.example.com' );
     * $imap->authenticate( 'username', 'password' );
     * $imap->selectMailbox( 'mailbox' ); // Inbox or another mailbox
     *
     * $imap->setFlag( '1:4', 'DRAFT' );
     * </code>
     *
     * @throws ezcMailTransportException
     *         if a mailbox is not selected
     *         or if the server sent a negative response
     *         or if $flag is not valid
     * @param string $messages
     * @param string $flag
     * @return bool
     */
    public function setFlag( $messages, $flag )
    {
        $uid = ( $this->options->uidReferencing ) ? self::UID : self::NO_UID;

        if ( $this->state != self::STATE_SELECTED )
        {
            throw new ezcMailTransportException( "Can't call setFlag() when a mailbox is not selected." );
        }

        $flag = $this->normalizeFlag( $flag );
        if ( in_array( $flag, self::$basicFlags ) )
        {
            $tag = $this->getNextTag();
            $this->connection->sendData( "{$tag} {$uid}STORE {$messages} +FLAGS (\\{$flag})" );
            $response = trim( $this->getResponse( $tag ) );
            if ( $this->responseType( $response ) != self::RESPONSE_OK )
            {
                throw new ezcMailTransportException( "The IMAP server could not set flag '{$flag}' on the messages '{$messages}': {$response}." );
            }
        }
        else
        {
            throw new ezcMailTransportException( "Flag '{$flag}' is not allowed for setting." );
        }
        return true;
    }

    /**
     * Clears $flag from $messages.
     *
     * This method supports unique IDs instead of message numbers. See
     * {@link ezcMailImapTransportOptions} for how to enable unique IDs
     * referencing.
     *
     * $messages can be:
     *  - a single message number (eg. '1')
     *  - a message range (eg. '1:4')
     *  - a message list (eg. '1,2,4')
     *
     * $flag can be one of:
     *  - ANSWERED   - message has been answered
     *  - DELETED    - message is marked to be deleted by later EXPUNGE
     *  - DRAFT      - message is marked as a draft
     *  - FLAGGED    - message is "flagged" for urgent/special attention
     *  - SEEN       - message has been read
     *
     * This function automatically adds the '\' in front of the flag when
     * calling the server command.
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully, and a mailbox
     * must be selected.
     *
     * Example:
     * <code>
     * $imap = new ezcMailImapTransport( 'imap.example.com' );
     * $imap->authenticate( 'username', 'password' );
     * $imap->selectMailbox( 'mailbox' ); // Inbox or another mailbox
     *
     * $imap->clearFlag( '1:4', 'DRAFT' );
     * </code>
     *
     * @throws ezcMailTransportException
     *         if a mailbox is not selected
     *         or if the server sent a negative response
     *         or if $flag is not valid
     * @param string $messages
     * @param string $flag
     * @return bool
     */
    public function clearFlag( $messages, $flag )
    {
        $uid = ( $this->options->uidReferencing ) ? self::UID : self::NO_UID;

        if ( $this->state != self::STATE_SELECTED )
        {
            throw new ezcMailTransportException( "Can't call clearFlag() when a mailbox is not selected." );
        }

        $flag = $this->normalizeFlag( $flag );
        if ( in_array( $flag, self::$basicFlags ) )
        {
            $tag = $this->getNextTag();
            $this->connection->sendData( "{$tag} {$uid}STORE {$messages} -FLAGS (\\{$flag})" );
            $response = trim( $this->getResponse( $tag ) );
            if ( $this->responseType( $response ) != self::RESPONSE_OK )
            {
                throw new ezcMailTransportException( "The IMAP server could not clear flag '{$flag}' on the messages '{$messages}': {$response}." );
            }
        }
        else
        {
            throw new ezcMailTransportException( "Flag '{$flag}' is not allowed for clearing." );
        }
        return true;
    }

    /**
     * Returns an array of message numbers from the selected mailbox which have a
     * certain flag set.
     *
     * This method supports unique IDs instead of message numbers. See
     * {@link ezcMailImapTransportOptions} for how to enable unique IDs
     * referencing.
     *
     * $flag can be one of:
     *
     * Basic flags:
     *  - ANSWERED   - message has been answered
     *  - DELETED    - message is marked to be deleted by later EXPUNGE
     *  - DRAFT      - message is marked as a draft
     *  - FLAGGED    - message is "flagged" for urgent/special attention
     *  - RECENT     - message is recent
     *  - SEEN       - message has been read
     *
     * Opposites of the above flags:
     *  - UNANSWERED
     *  - UNDELETED
     *  - UNDRAFT
     *  - UNFLAGGED
     *  - OLD
     *  - UNSEEN
     *
     * Composite flags:
     *  - NEW        - equivalent to RECENT + UNSEEN
     *  - ALL        - all the messages
     *
     * The returned array is something like this:
     * <code>
     *   array( 0 => 1, 1 => 5 );
     * </code>
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully, and a mailbox
     * must be selected.
     *
     * @throws ezcMailTransportException
     *         if a mailbox is not selected
     *         or if the server sent a negative response
     *         or if $flag is not valid
     * @param string $flag
     * @return array(int)
     */
    protected function searchByFlag( $flag )
    {
        $uid = ( $this->options->uidReferencing ) ? self::UID : self::NO_UID;

        if ( $this->state != self::STATE_SELECTED &&
             $this->state != self::STATE_SELECTED_READONLY )
        {
            throw new ezcMailTransportException( "Can't call searchByFlag() on the IMAP transport when a mailbox is not selected." );
        }

        $matchingMessages = array();
        $flag = $this->normalizeFlag( $flag );
        if ( in_array( $flag, self::$extendedFlags ) )
        {
            $tag = $this->getNextTag();
            $this->connection->sendData( "{$tag} {$uid}SEARCH ({$flag})" );
            $response = $this->getResponse( '* SEARCH' );

            if ( strpos( $response, '* SEARCH' ) !== false )
            {
                $ids = substr( trim( $response ), 9 );
                if ( trim( $ids ) !== "" )
                {
                    $matchingMessages = explode( ' ', $ids );
                }
            }
            $response = trim( $this->getResponse( $tag, $response ) );
            if ( $this->responseType( $response ) != self::RESPONSE_OK )
            {
                throw new ezcMailTransportException( "The IMAP server could not search the messages by flags: {$response}." );
            }
        }
        else
        {
            throw new ezcMailTransportException( "Flag '{$flag}' is not allowed for searching." );
        }
        return $matchingMessages;
    }

    /**
     * Sends a NOOP command to the server, use it to keep the connection alive.
     *
     * Before calling this method, a connection to the IMAP server must be
     * established.
     *
     * @throws ezcMailTransportException
     *         if there was no connection to the server
     *         or if the server sent a negative response
     */
    public function noop()
    {
        if ( $this->state != self::STATE_NOT_AUTHENTICATED &&
             $this->state != self::STATE_AUTHENTICATED &&
             $this->state != self::STATE_SELECTED &&
             $this->state != self::STATE_SELECTED_READONLY )
        {
            throw new ezcMailTransportException( "Can not issue NOOP command if not connected." );
        }

        $tag = $this->getNextTag();
        $this->connection->sendData( "{$tag} NOOP" );
        $response = trim( $this->getResponse( $tag ) );
        if ( $this->responseType( $response ) != self::RESPONSE_OK )
        {
            throw new ezcMailTransportException( "NOOP failed: {$response}." );
        }
    }

    /**
     * Returns an array with the capabilities of the IMAP server.
     *
     * The returned array will be something like this:
     * <code>
     *   array( 'IMAP4rev1', 'SASL-IR SORT', 'THREAD=REFERENCES', 'MULTIAPPEND',
     *          'UNSELECT', 'LITERAL+', 'IDLE', 'CHILDREN', 'NAMESPACE',
     *          'LOGIN-REFERRALS'
     *        );
     * </code>
     *
     * Before calling this method, a connection to the IMAP server must be
     * established.
     *
     * @throws ezcMailTransportException
     *         if there was no connection to the server
     *         or if the server sent a negative response
     * @return array(string)
     */
    public function capability()
    {
        if ( $this->state != self::STATE_NOT_AUTHENTICATED &&
             $this->state != self::STATE_AUTHENTICATED &&
             $this->state != self::STATE_SELECTED &&
             $this->state != self::STATE_SELECTED_READONLY )
        {
            throw new ezcMailTransportException( "Trying to request capability when not connected to server." );
        }

        $tag = $this->getNextTag();
        $this->connection->sendData( "{$tag} CAPABILITY" );

        $response = $this->connection->getLine();
        while ( $this->responseType( $response ) != self::RESPONSE_UNTAGGED &&
                strpos( $response, '* CAPABILITY ' ) === false )
        {
            $response = $this->connection->getLine();
        }
        $result = trim( $response );

        $response = trim( $this->getResponse( $tag ) );
        if ( $this->responseType( $response ) != self::RESPONSE_OK )
        {
            throw new ezcMailTransportException( "The IMAP server responded negative to the CAPABILITY command: {$response}." );
        }

        return explode( ' ', str_replace( '* CAPABILITY ', '', $result ) );
    }

    /**
     * Sends an EXPUNGE command to the server.
     *
     * This method permanently deletes the messages marked for deletion by
     * the method {@link delete()}.
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully, and a mailbox
     * must be selected.
     *
     * @throws ezcMailTransportException
     *         if a mailbox was not selected
     *         or if the server sent a negative response
     */
    public function expunge()
    {
        if ( $this->state != self::STATE_SELECTED )
        {
            throw new ezcMailTransportException( "Can not issue EXPUNGE command if a mailbox is not selected." );
        }

        $tag = $this->getNextTag();
        $this->connection->sendData( "{$tag} EXPUNGE" );
        $response = trim( $this->getResponse( $tag ) );
        if ( $this->responseType( $response ) != self::RESPONSE_OK )
        {
            throw new ezcMailTransportException( "EXPUNGE failed: {$response}." );
        }
    }

    /**
     * Appends $mail to the $mailbox mailbox.
     *
     * Use this method to create email messages in a mailbox such as Sent or
     * Draft.
     *
     * $flags is an array of flags to be set to the $mail (if provided):
     *
     * $flag can be one of:
     *  - ANSWERED   - message has been answered
     *  - DELETED    - message is marked to be deleted by later EXPUNGE
     *  - DRAFT      - message is marked as a draft
     *  - FLAGGED    - message is "flagged" for urgent/special attention
     *  - SEEN       - message has been read
     *
     * This function automatically adds the '\' in front of each flag when
     * calling the server command.
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully.
     *
     * @throws ezcMailTransportException
     *         if user is not authenticated
     *         or if the server sent a negative response
     *         or if $mailbox does not exists
     * @param string $mailbox
     * @param string $mail
     * @param array(string) $flags
     */
    public function append( $mailbox, $mail, $flags = null )
    {
        if ( $this->state != self::STATE_AUTHENTICATED &&
             $this->state != self::STATE_SELECTED &&
             $this->state != self::STATE_SELECTED_READONLY )
        {
            throw new ezcMailTransportException( "Can't call append() if not authenticated." );
        }

        $tag = $this->getNextTag();
        $mailSize = strlen( $mail );
        if ( !is_null( $flags ) )
        {
            for ( $i = 0; $i < count( $flags ); $i++ )
            {
                $flags[$i] = '\\' . $this->normalizeFlag( $flags[$i] );
            }
            $flagList = implode( ' ', $flags );
            $command = "{$tag} APPEND {$mailbox} ({$flagList}) {{$mailSize}}";
        }
        else
        {
            $command = "{$tag} APPEND {$mailbox} {{$mailSize}}";
        }

        $this->connection->sendData( $command );
        $response = trim( $this->connection->getLine() );

        if ( strpos( $response, 'TRYCREATE' ) !== false )
        {
            throw new ezcMailTransportException( "Mailbox does not exist: {$response}." );
        }

        if ( $this->responseType( $response ) == self::RESPONSE_FEEDBACK )
        {
            $this->connection->sendData( $mail );
            $response = trim( $this->getResponse( $tag ) );
            if ( $this->responseType( $response ) != self::RESPONSE_OK )
            {
                throw new ezcMailTransportException( "The IMAP server could not append message to mailbox '{$mailbox}': {$response}." );
            }
        }
        elseif ( $this->responseType( $response ) != self::RESPONSE_OK )
        {
            throw new ezcMailTransportException( "The IMAP server could not append message to mailbox '{$mailbox}': {$response}." );
        }
    }

    /**
     * Clears $flag of unwanted characters and makes it uppercase.
     *
     * @param string $flag
     * @return string
     */ 
    protected function normalizeFlag( $flag )
    {
        $flag = strtoupper( $flag );
        $flag = str_replace( '\\', '', $flag );
        return trim( $flag );
    }

    /**
     * Sorts message numbers array $messages by the specified $sortCriteria.
     *
     * This method supports unique IDs instead of message numbers. See
     * {@link ezcMailImapTransportOptions} for how to enable unique IDs
     * referencing.
     *
     * $messages is an array of message numbers, for example:
     * <code>
     *   array( 1, 2, 4 );
     * </code>
     *
     * $sortCriteria is an email header like: Subject, To, From, Date, Sender.
     *
     * The sorting is done with the php function natcasesort().
     *
     * Before calling this method, a connection to the IMAP server must be
     * established and a user must be authenticated successfully, and a mailbox
     * must be selected.
     *
     * @throws ezcMailTransportException
     *         if a mailbox is not selected
     *         or if the server sent a negative response
     *         or if the array $messages is empty
     * @param array(int) $messages
     * @param string $sortCriteria
     * @param bool $reverse
     * @return array(string)
     */
    protected function sort( $messages, $sortCriteria, $reverse = false )
    {
        $uid = ( $this->options->uidReferencing ) ? self::UID : self::NO_UID;

        if ( $this->state != self::STATE_SELECTED &&
             $this->state != self::STATE_SELECTED_READONLY )
        {
            throw new ezcMailTransportException( "Can't call sort() on the IMAP transport when a mailbox is not selected." );
        }

        $result = array();
        $query = ucfirst( strtolower( $sortCriteria ) );
        $messageNumbers = implode( ',', $messages );

        $tag = $this->getNextTag();
        $this->connection->sendData( "{$tag} {$uid}FETCH {$messageNumbers} (BODY.PEEK[HEADER.FIELDS ({$query})])" );

        $response = trim( $this->connection->getLine() );
        while ( strpos( $response, $tag ) === false )
        {
            if ( strpos( $response, ' FETCH (' ) !== false )
            {
                if ( $this->options->uidReferencing )
                {
                    preg_match('/^\* [0-9]+ FETCH \(UID ([0-9]+)/', $response, $matches );
                }
                else
                {
                    preg_match('/^\* ([0-9]+) FETCH/', $response, $matches );
                }
                $messageNumber = $matches[1];
            }

            if ( strpos( $response, $query ) !== false )
            {
                $strippedResponse = trim( trim( str_replace( "{$query}: ", '', $response ) ), '"' );
                switch ( $query )
                {
                    case 'Date':
                        $strippedResponse = strtotime( $strippedResponse );
                        break;
                    case 'Subject':
                    case 'From':
                    case 'Sender':
                    case 'To':
                        $strippedResponse = ezcMailTools::mimeDecode( $strippedResponse );
                        break;
                    default:
                        break;
                }
                $result[$messageNumber] = $strippedResponse;
            }

            // in case the mail doesn't have the $sortCriteria header (like junk mail missing Subject header)
            if ( strpos( $response, ')' ) !== false && !isset( $result[$messageNumber] ) )
            {
                $result[$messageNumber] = '';
            }

            $response = trim( $this->connection->getLine() );
        }

        if ( $this->responseType( $response ) != self::RESPONSE_OK )
        {
            throw new ezcMailTransportException( "The IMAP server could not sort the messages: {$response}." );
        }

        if ( $reverse === true )
        {
            natcasesort( $result );
            $result = array_reverse( $result, true );
        }
        else
        {
            natcasesort( $result );
        }
        return $result;
    }

    /**
     * Parses $line to return the response code.
     *
     * Returns one of the following:
     *  - {@link RESPONSE_OK}
     *  - {@link RESPONSE_NO}
     *  - {@link RESPONSE_BAD}
     *  - {@link RESPONSE_UNTAGGED}
     *  - {@link RESPONSE_FEEDBACK}
     *
     * @throws ezcMailTransportException
     *         if the IMAP response ($line) is not recognized
     * @param string $line
     * @return int
     */
    protected function responseType( $line )
    {
        if ( strpos( $line, 'OK ' ) !== false && strpos( $line, 'OK ' ) == 6 )
        {
            return self::RESPONSE_OK;
        }
        if ( strpos( $line, 'NO ' ) !== false && strpos( $line, 'NO ' ) == 6 )
        {
            return self::RESPONSE_NO;
        }
        if ( strpos( $line, 'BAD ' ) !== false && strpos( $line, 'BAD ' ) == 6 )
        {
            return self::RESPONSE_BAD;
        }
        if ( strpos( $line, '* ' ) !== false && strpos( $line, '* ' ) == 0 )
        {
            return self::RESPONSE_UNTAGGED;
        }
        if ( strpos( $line, '+ ' ) !== false && strpos( $line, '+ ' ) == 0 )
        {
            return self::RESPONSE_FEEDBACK;
        }
        throw new ezcMailTransportException( "Unrecognized IMAP response in line: {$line}" );
    }

    /**
     * Reads the responses from the server until encountering $tag.
     *
     * In IMAP, each command sent by the client is prepended with a
     * alphanumeric tag like 'A1234'. The server sends the response
     * to the client command as lines, and the last line in the response
     * is prepended with the same tag, and it contains the status of
     * the command completion ('OK', 'NO' or 'BAD').
     *
     * Sometimes the server sends alerts and response lines from other
     * commands before sending the tagged line, so this method just
     * reads all the responses until it encounters $tag.
     *
     * It returns the tagged line to be processed by the calling method.
     *
     * If $response is specified, then it will not read the response
     * from the server before searching for $tag in $response.
     *
     * Before calling this method, a connection to the IMAP server must be
     * established.
     *
     * @param string $tag
     * @param string $response
     * @return string
     */
    protected function getResponse( $tag, $response = null )
    {
        if ( is_null( $response ) )
        {
            $response = $this->connection->getLine();
        }
        while ( strpos( $response, $tag ) === false )
        {
            if ( strpos( $response, ' BAD ' ) !== false ||
                 strpos( $response, ' NO ' ) !== false )
            {
                break;
            }
            $response = $this->connection->getLine();
        }
        return $response;
    }

    /**
     * Generates the next IMAP tag to prepend to client commands.
     *
     * The structure of the IMAP tag is Axxxx, where:
     *  - A is a letter (uppercase for conformity)
     *  - x is a digit from 0 to 9
     *
     * example of generated tag: T5439
     *
     * It uses the class variable $this->currentTag.
     *
     * Everytime it is called, the tag increases by 1.
     *
     * If it reaches the last tag, it wraps around to the first tag.
     *
     * By default, the first generated tag is A0001.
     *
     * @return string
     */
    protected function getNextTag()
    {
        $tagLetter = substr( $this->currentTag, 0, 1 );
        $tagNumber = intval( substr( $this->currentTag, 1 ) );
        $tagNumber++;
        if ( $tagLetter == 'Z' && $tagNumber == 10000 )
        {
            $tagLetter = 'A';
            $tagNumber = 1;
        }
        if ( $tagNumber == 10000 )
        {
            $tagLetter++;
            $tagNumber = 0;
        }
        $this->currentTag = $tagLetter . sprintf( "%04s", $tagNumber );
        return $this->currentTag;
    }

    /**
     * Returns the size of a FETCH section in bytes.
     *
     * The section header looks like: * id FETCH (BODY[TEXT] {size}
     * where size is the size in bytes and id is the message number or ID.
     *
     * Example: for " * 2 FETCH (BODY[TEXT] {377}" this function returns 377.
     *
     * @return int
     */
    protected function getMessageSectionSize( $response )
    {
        $size = 0;
        preg_match( '/\{(.*)\}/', $response, $matches );
        if ( count( $matches ) > 0 )
        {
            $size = (int) $matches[1];
        }
        return $size;
    }
}
?>
