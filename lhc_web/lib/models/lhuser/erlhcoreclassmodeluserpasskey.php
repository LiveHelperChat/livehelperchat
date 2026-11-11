<?php

#[\AllowDynamicProperties]
class erLhcoreClassModelUserPasskey
{
    use erLhcoreClassDBTrait;

    public static $dbTable = 'lh_users_passkey';

    public static $dbTableId = 'id';

    public static $dbSessionHandler = 'erLhcoreClassUser::getSession';

    public static $dbSortOrder = 'ASC';

    public $id = null;

    public $user_id = 0;

    /**
     * base64url encoded credential ID. Used for lookup during authentication
     */
    public $credential_id = '';

    /**
     * JSON representation of \Webauthn\CredentialRecord
     */
    public $credential = '';

    public $name = '';

    public $aaguid = '';

    public $counter = 0;

    public $created_at = 0;

    public $last_used_at = 0;

    public function getState()
    {
        return array(
            'id' => $this->id,
            'user_id' => $this->user_id,
            'credential_id' => $this->credential_id,
            'credential' => $this->credential,
            'name' => $this->name,
            'aaguid' => $this->aaguid,
            'counter' => $this->counter,
            'created_at' => $this->created_at,
            'last_used_at' => $this->last_used_at,
        );
    }

    public function __get($var)
    {
        switch ($var) {

            case 'user':
                $this->user = erLhcoreClassModelUser::fetch($this->user_id);
                return $this->user;

            case 'created_at_front':
            case 'last_used_at_front':
                $varReplaced = str_replace('_front','',$var);
                $this->{$var} = $this->{$varReplaced} > 0 ? date(erLhcoreClassModule::$dateDateHourFormat, $this->{$varReplaced}) : '';
                return $this->{$var};

            default:
                break;
        }
    }

    /**
     * Credential record stored in database
     *
     * @return \Webauthn\CredentialRecord
     */
    public function getCredentialRecord()
    {
        return erLhcoreClassUserPasskey::getSerializer()->deserialize($this->credential, \Webauthn\CredentialRecord::class, 'json');
    }

    /**
     * Updates stored credential data from the library object
     *
     * @param \Webauthn\CredentialRecord $credentialRecord
     */
    public function setFromCredentialRecord($credentialRecord)
    {
        $this->credential = erLhcoreClassUserPasskey::getSerializer()->serialize($credentialRecord, 'json');
        $this->credential_id = erLhcoreClassUserPasskey::base64UrlEncode($credentialRecord->publicKeyCredentialId);
        $this->aaguid = (string)$credentialRecord->aaguid;
        $this->counter = (int)$credentialRecord->counter;
    }

    public function getPublicKeyCredentialDescriptor()
    {
        return $this->getCredentialRecord()->getPublicKeyCredentialDescriptor();
    }

    /**
     * Removes all passkeys assigned to the user
     *
     * @param int $userId
     */
    public static function removeForUser($userId)
    {
        $db = ezcDbInstance::get();

        $stmt = $db->prepare('DELETE FROM ' . self::$dbTable . ' WHERE user_id = :user_id');
        $stmt->bindValue(':user_id', (int)$userId, PDO::PARAM_INT);
        $stmt->execute();
    }
}

?>
