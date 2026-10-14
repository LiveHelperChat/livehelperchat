<?php

namespace LiveHelperChat\Models\LHCAbstract;
#[\AllowDynamicProperties]
class OfflineReason
{
    use \erLhcoreClassDBTrait;

    /** Reason keeps the operator offline (default). */
    const MODE_OFFLINE = 0;

    /** Reason is selected while the operator stays online (e.g. Training). */
    const MODE_ONLINE = 1;

    public static $dbTable = 'lh_abstract_offline_reason';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassAbstract::getSession';

    public static $dbSortOrder = 'DESC';

    public static $dbDefaultSort = '`pos` DESC, `name` ASC';

    public $has_filter = false;

    public $hide_add = false;

    public $hide_delete = false;

    public function getState()
    {
        return array(
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'icon'        => $this->icon,
            'pos'         => $this->pos,
            'mode'        => $this->mode,
            'disable_auto_assign' => $this->disable_auto_assign,
            'disable_transfer'    => $this->disable_transfer,
        );
    }

    public function __toString()
    {
        return $this->name;
    }

    public function __get($var)
    {
        switch ($var) {
            case 'mode_frontend':
                return $this->mode_frontend = $this->isOnlineMode()
                    ? \erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/offlinereason', 'Online mode')
                    : \erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/offlinereason', 'Offline Mode');
                break;

            default:
                ;
                break;
        }
    }

    /** Is this reason meant to be used while the operator remains online. */
    public function isOnlineMode()
    {
        return (int)$this->mode === self::MODE_ONLINE;
    }

    /**
     * Fetch a reason by id, returns false when the id is empty or the reason does not exist.
     */
    public static function getReason($reasonId)
    {
        $reasonId = (int)$reasonId;

        if ($reasonId <= 0) {
            return false;
        }

        try {
            $reason = self::fetch($reasonId);
        } catch (\Exception $e) {
            return false;
        }

        return $reason instanceof self ? $reason : false;
    }

    /**
     * Keep the `lh_userdep` auto assignment exclusion flags in sync with the currently selected reason.
     *
     * Only online mode reasons take part - an offline mode reason means the operator is offline anyway, so
     * "Disable auto assigned" is applied only while the operator stays online and switching away restores
     * the operator's own preference stored on `lh_users`.
     */
    public static function syncAutoAssignStatus($userData)
    {
        if (!is_object($userData) || (int)$userData->id <= 0) {
            return;
        }

        $excludeAutoAssign = (int)$userData->exclude_autoasign;
        $excludeAutoAssignMails = (int)$userData->exclude_autoasign_mails;

        $reason = self::getReason($userData->offline_reason_id);

        if ($reason !== false && $reason->isOnlineMode() && (int)$reason->disable_auto_assign === 1) {
            $excludeAutoAssign = 1;
            $excludeAutoAssignMails = 1;
        }

        \erLhcoreClassUserDep::setExcludeAutoAssignStatus($userData->id, $excludeAutoAssign, $excludeAutoAssignMails);
    }

    /**
     * Should chats/mails be blocked from being transferred manually to this operator.
     *
     * Only applies while the operator is online with an online mode reason - an offline operator does not
     * accept chats through the online assignment anyway.
     */
    public static function isTransferDisabled($userId)
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            return false;
        }

        $user = \erLhcoreClassModelUser::fetch($userId);

        if (!($user instanceof \erLhcoreClassModelUser) || (int)$user->offline_reason_id <= 0) {
            return false;
        }

        $reason = self::getReason($user->offline_reason_id);

        return $reason !== false && $reason->isOnlineMode() && (int)$reason->disable_transfer === 1;
    }

    public function updateThis()
    {
        $this->saveThis();
    }

    public function getFields()
    {
        return include('lib/core/lhabstract/fields/erlhabstractmodelofflinereason.php');
    }

    public function getModuleTranslations()
    {
        return array(
            'permission_delete' => array('module' => 'lhabstract', 'function' => 'use'),
            'permission'        => array('module' => 'lhuser', 'function' => 'offlinereasons'),
            'name'              => \erTranslationClassLhTranslation::getInstance()->getTranslation('abstract/offlinereason', 'Offline Reason'),
        );
    }

    public $id          = null;
    public $name        = '';
    public $description = '';
    public $icon        = '';
    public $pos         = 0;
    public $mode        = 0;
    public $disable_auto_assign = 0;
    public $disable_transfer    = 0;
}
