<?php
/**
 * File containing the ezcMailPop3Set class.
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcMailPop3Set is an internal class that fetches a series of mail
 * from the pop3 server.
 *
 * The POP3 set works on an existing connection and a list of the messages that
 * the user wants to fetch. The user must accept all the data for each mail for
 * correct behaviour.
 *
 * The set can be parsed with ezcMailParser.
 *
 * @package Mail
 * @version 1.7.1
 */
class ezcMailPop3Set implements ezcMailParserSet
{
    /**
     * Holds the list of messages that the user wants to retrieve from the server.
     *
     * @var array(int)
     */
    private $messages;

    /**
     * Holds the current message the user is fetching.
     *
     * The variable is null before the first message and false after
     * the last message has been fetched.
     *
     * @var int
     */
    private $currentMessage = null;

    /**
     * This variable is true if there is more data in the mail that is being
     * fetched.
     *
     * It is false if there is no mail being fetched currently or if all the
     * data of the current mail has been fetched.
     *
     * @var bool
     */
    private $hasMoreMailData = false;

    /**
     * Holds if mail should be deleted from the server after retrieval.
     *
     * @var bool
     */
    private $deleteFromServer = false;

    /**
     * Constructs a new POP3 parser set that will fetch the messages $messages.
     *
     * $connection must hold a valid connection to a POP3 server that is ready
     * to retrieve the messages.
     *
     * If $deleteFromServer is set to true the messages will be deleted after
     * retrieval.
     *
     * @throws ezcMailTransportException
     *         if the server sent a negative response
     * @param ezcMailTransportConnection $connection
     * @param array(ezcMail) $messages
     * @param bool $deleteFromServer
     */
    public function __construct( ezcMailTransportConnection $connection, array $messages, $deleteFromServer = false )
    {
        $this->connection = $connection;
        $this->messages = $messages;
        $this->deleteFromServer = $deleteFromServer;
    }

    /**
     * Returns true if all the data has been fetched from this set.
     *
     * @return bool
     */
    public function isFinished()
    {
        return $this->currentMessage === false ? true : false;
    }

    /**
     * Returns one line of data from the current mail in the set.
     *
     * Null is returned if there is no current mail in the set or the end of the
     * mail is reached.
     *
     * @return string
     */
    public function getNextLine()
    {
        if ( $this->currentMessage === null )
        {
            $this->nextMail();
        }
        if ( $this->hasMoreMailData )
        {
            $data = $this->connection->getLine();
            if ( rtrim( $data ) === "." )
            {
                $this->hasMoreMailData = false;
                // remove the mail if required by the user.
                if ( $this->deleteFromServer == true )
                {
                    $this->connection->sendData( "DELE {$this->currentMessage}" );
                    $response = $this->connection->getLine(); // ignore response
                }
                return null;
            }
            return $data;
        }
        return null;
    }

    /**
     * Moves the set to the next mail and returns true upon success.
     *
     * False is returned if there are no more mail in the set.
     *
     * @throws ezcMailTransportException
     *         if the server sent a negative response
     * @return bool
     */
    public function nextMail()
    {
        if ( $this->currentMessage === null )
        {
            $this->currentMessage = reset( $this->messages );
        }
        else
        {
            $this->currentMessage = next( $this->messages );
        }
        if ( $this->currentMessage !== false )
        {
            $this->connection->sendData( "RETR {$this->currentMessage}" );
            $response = $this->connection->getLine();
            if ( strpos( $response, "+OK" ) === 0 )
            {
                $this->hasMoreMailData = true;
                return true;
            }
            else
            {
                throw new ezcMailTransportException( "The POP3 server sent a negative reply when requesting mail." );
            }
        }
        return false;
    }

    /**
     * Returns whether the set has mails.
     *
     * @return bool
     */
    public function hasData()
    {
        return count( $this->messages );
    }

    /**
     * Returns message numbers from the current set.
     *
     * @return array(int)
     */
    public function getMessageNumbers()
    {
        return $this->messages;
    }
}
?>
