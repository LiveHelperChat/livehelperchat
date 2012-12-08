<?php
/**
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.1
 * @filesource
 * @package SignalSlot
 */

/**
 * ezcSignalStaticConnections makes it possible to connect to signals through the signals identifier.
 *
 * The static connections allow you to:
 * - connect to a signal sent by any object signal collection with the same identifier. Usually the
 *   identifier is set to the name of the class holding the collection. Using the static connections
 *   you can connect to a signal sent by any object of that class.
 *
 * - connect to a signal that does not yet exist. This allows you to delay initialization of the
 *   emitting object until it is needed.
 *
 * @property array $connections Holds the internal structure of signals. The format is
 *                 array( identifier => array( signalName => array(priority=>array(slots)) ) ).
 *                 It can be both read and set in order
 *                 to provide easy setup of the static connections from disk.
 *
 * @version 1.1.1
 * @mainclass
 * @package SignalSlot
 */
class ezcSignalStaticConnections implements ezcSignalStaticConnectionsBase
{
    /**
     * Holds the properties of this class.
     *
     * @var array(string=>string)
     */
    private $properties = array();

    /**
     * ezcSignalStaticConnections singleton instance.
     *
     * @var ezcConfigurationManager
     */
    private static $instance = null;

    /**
     * Returns the instance of the ezcSignalStaticConnections..
     *
     * @return ezcConfigurationManager
     */
    public static function getInstance()
    {
        if ( self::$instance === null )
        {
            self::$instance = new ezcSignalStaticConnections();
            ezcBaseInit::fetchConfig( 'ezcInitSignalStaticConnections', self::$instance );
        }
        return self::$instance;
    }

    /**
     * Constructs a new empty static connections class.
     */
    private function __construct()
    {
        $this->properties['connections'] = array();
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'connections':
                $this->properties[$name] = $value;
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $name );
                break;
        }

    }

    /**
     * Returns the property $name.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @return mixed
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'connections':
                return (array) $this->properties[$name];
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
                break;
        }
    }

    /**
     * Returns all the connections for signals $signal in signal collections
     * with the identifier $identifier.
     *
     * @param string $identifier
     * @param string $signal
     * @return array(int=>array(callback))
     */
    public function getConnections( $identifier, $signal )
    {
        if ( isset( $this->connections[$identifier] ) &&
            isset( $this->connections[$identifier][$signal] ) )
        {
            return $this->connections[$identifier][$signal];
        }
        return array();
    }

    /**
     * Connects the signal $signal emited by any ezcSignalCollection with the identifier
     * $identifier to the slot $slot.
     *
     * To control the order in which slots are called you can set a priority
     * from 1 - 65 536. The lower the number the higher the priority. The default
     * priority is 1000.
     * Slots with the same priority may be called with in any order.
     *
     * A slot will be called once for every time it is connected. It is possible
     * to connect a slot more than once.
     *
     * See the PHP documentation for examples on the callback type.
     * http://php.net/callback
     *
     * We reccommend avoiding excessive usage of the $priority parameter
     * since it makes it much harder to track how your program works.
     *
     * @param string $identifier
     * @param string $signal
     * @param callback $slot
     * @param int $priority
     * @return void
     */
    public function connect( $identifier, $signal, $slot, $priority = 1000 )
    {
        $this->properties['connections'][$identifier][$signal][$priority][] = $slot;
        sort( $this->properties['connections'][$identifier][$signal][$priority], SORT_NUMERIC );
    }

    /**
     * Disconnects the $slot from the $signal with identifier $identifier..
     *
     * If the priority is given it will try to disconnect a slot with that priority.
     * If no such slot is found no slot will be disconnected.
     *
     * If no priority is given it will disconnect the matching slot with the lowest priority.
     *
     * @param string $identifier
     * @param string $signal
     * @param callback $slot
     * @param int $priority
     * @return void
     */
    public function disconnect( $identifier, $signal, $slot, $priority = null )
    {
        if ( !isset( $this->connections[$identifier] ) ||
            !isset( $this->connections[$identifier][$signal] ) )
        {
            return;
        }

        if ( $priority === null ) // delete first found, searched from back
        {
            $allKeys = array_keys( $this->connections[$identifier][$signal] );
            rsort( $allKeys, SORT_NUMERIC );
            foreach ( $allKeys as $priority )
            {
                foreach ( $this->connections[$identifier][$signal][$priority] as $key => $callback )
                {
                    if ( ezcSignalCallbackComparer::compareCallbacks( $slot, $callback ) )
                    {
                        unset( $this->properties['connections'][$identifier][$signal][$priority][$key] );
                        // if the priority is empty now it should be unset
                        if ( count( $this->properties['connections'][$identifier][$signal][$priority] ) == 0 )
                        {
                            unset( $this->properties['connections'][$identifier][$signal][$priority] );
                        }
                        return;
                    }
                }
            }

        }
        else // only delete from priority connections
        {
            if ( isset( $this->connections[$identifier][$signal][$priority] ) )
            {
                foreach ( $this->connections[$identifier][$signal][$priority] as $key => $callback )
                {
                    if ( ezcSignalCallbackComparer::compareCallbacks( $slot, $callback ) )
                    {
                        unset( $this->properties['connections'][$identifier][$signal][$priority][$key] );
                        // if the priority is empty now it should be unset
                        if ( count( $this->properties['connections'][$identifier][$signal][$priority] ) == 0 )
                        {
                            unset( $this->properties['connections'][$identifier][$signal][$priority] );
                        }
                        return;
                    }
                }
            }
        }
    }
}

?>
