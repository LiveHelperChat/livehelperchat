<?php

class erLhcoreClassMailconv {

    public static function getSession() {
        if (! isset ( self::$persistentSession )) {
            self::$persistentSession = new ezcPersistentSession ( ezcDbInstance::get (), new ezcPersistentCodeManager ( './pos/lhmailconv' ) );
        }
        return self::$persistentSession;
    }

    private static $persistentSession;
}

?>