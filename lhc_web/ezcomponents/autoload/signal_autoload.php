<?php
/**
 * Autoloader definition for the SignalSlot component.
 *
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.1
 * @filesource
 * @package SignalSlot
 */

return array(
    'ezcSignalSlotException'         => 'SignalSlot/exceptions/signalslot_exception.php',
    'ezcSignalStaticConnectionsBase' => 'SignalSlot/interfaces/static_connections_base.php',
    'ezcSignalCallbackComparer'      => 'SignalSlot/internal/callback_comparer.php',
    'ezcSignalCollection'            => 'SignalSlot/signal_collection.php',
    'ezcSignalCollectionOptions'     => 'SignalSlot/options/options.php',
    'ezcSignalStaticConnections'     => 'SignalSlot/static_connections.php',
);
?>
