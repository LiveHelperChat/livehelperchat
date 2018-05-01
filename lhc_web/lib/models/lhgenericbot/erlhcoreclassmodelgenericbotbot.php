<?php

class erLhcoreClassModelGenericBotBot {

    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_generic_bot_bot';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassGenericBot::getSession';

    public static $dbSortOrder = 'DESC';

    public function getState()
    {
        $stateArray = array(
            'id' => $this->id,
            'name' => $this->name
        );

        return $stateArray;
    }

    public function beforeRemove() {
        $q = ezcDbInstance::get()->createDeleteQuery();

        // Bot groups
        $q->deleteFrom( 'lh_generic_bot_group' )->where( $q->expr->eq( 'bot_id', $this->id ) );
        $stmt = $q->prepare();
        $stmt->execute();

        // Bot payloads
        $q->deleteFrom( 'lh_generic_bot_payload' )->where( $q->expr->eq( 'bot_id', $this->id ) );
        $stmt = $q->prepare();
        $stmt->execute();

        // Bot triggers
        $q->deleteFrom( 'lh_generic_bot_trigger' )->where( $q->expr->eq( 'bot_id', $this->id ) );
        $stmt = $q->prepare();
        $stmt->execute();

        // Bot trigger event
        $q->deleteFrom( 'lh_generic_bot_trigger_event' )->where( $q->expr->eq( 'bot_id', $this->id ) );
        $stmt = $q->prepare();
        $stmt->execute();
    }

    public function __toString()
    {
        return $this->name;
    }

    public $id = null;
    public $name = '';
}