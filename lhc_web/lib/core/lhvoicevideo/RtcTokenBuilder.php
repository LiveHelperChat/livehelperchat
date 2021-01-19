<?php

namespace AgoraIO;

class Message
{
    public $salt;
    public $ts;
    public $privileges;

    public function __construct()
    {
        $this->salt = rand(0, 100000);

        $date = new \DateTime("now", new \DateTimeZone('UTC'));
        $this->ts = $date->getTimestamp() + 24 * 3600;

        $this->privileges = array();
    }

    public function packContent()
    {
        $buffer = unpack("C*", pack("V", $this->salt));
        $buffer = array_merge($buffer, unpack("C*", pack("V", $this->ts)));
        $buffer = array_merge($buffer, unpack("C*", pack("v", sizeof($this->privileges))));
        foreach ($this->privileges as $key => $value) {
            $buffer = array_merge($buffer, unpack("C*", pack("v", $key)));
            $buffer = array_merge($buffer, unpack("C*", pack("V", $value)));
        }
        return $buffer;
    }

    public function unpackContent($msg)
    {
        $pos = 0;
        $salt = unpack("V", substr($msg, $pos, 4))[1];
        $pos += 4;
        $ts = unpack("V", substr($msg, $pos, 4))[1];
        $pos += 4;
        $size = unpack("v", substr($msg, $pos, 2))[1];
        $pos += 2;

        $privileges = array();
        for ($i = 0; $i < $size; $i++) {
            $key = unpack("v", substr($msg, $pos, 2));
            $pos += 2;
            $value = unpack("V", substr($msg, $pos, 4));
            $pos += 4;
            $privileges[$key[1]] = $value[1];
        }
        $this->salt = $salt;
        $this->ts = $ts;
        $this->privileges = $privileges;
    }
}

class AccessToken
{
    const Privileges = array(
        "kJoinChannel" => 1,
        "kPublishAudioStream" => 2,
        "kPublishVideoStream" => 3,
        "kPublishDataStream" => 4,
        "kPublishAudioCdn" => 5,
        "kPublishVideoCdn" => 6,
        "kRequestPublishAudioStream" => 7,
        "kRequestPublishVideoStream" => 8,
        "kRequestPublishDataStream" => 9,
        "kInvitePublishAudioStream" => 10,
        "kInvitePublishVideoStream" => 11,
        "kInvitePublishDataStream" => 12,
        "kAdministrateChannel" => 101,
        "kRtmLogin" => 1000,
    );

    public $appID, $appCertificate, $channelName, $uid;
    public $message;

    function __construct()
    {
        $this->message = new Message();
    }

    function setUid($uid)
    {
        if ($uid === 0) {
            $this->uid = "";
        } else {
            $this->uid = $uid . '';
        }
    }

    function is_nonempty_string($name, $str)
    {
        if (is_string($str) && $str !== "") {
            return true;
        }
        echo $name . " check failed, should be a non-empty string";
        return false;
    }

    static function init($appID, $appCertificate, $channelName, $uid)
    {
        $accessToken = new AccessToken();

        if (!$accessToken->is_nonempty_string("appID", $appID) ||
            !$accessToken->is_nonempty_string("appCertificate", $appCertificate) ||
            !$accessToken->is_nonempty_string("channelName", $channelName)) {
            return null;
        }

        $accessToken->appID = $appID;
        $accessToken->appCertificate = $appCertificate;
        $accessToken->channelName = $channelName;

        $accessToken->setUid($uid);
        $accessToken->message = new Message();
        return $accessToken;
    }

    static function initWithToken($token, $appCertificate, $channel, $uid)
    {
        $accessToken = new AccessToken();
        if (!$accessToken->extract($token, $appCertificate, $channel, $uid)) {
            return null;
        }
        return $accessToken;
    }

    function addPrivilege($key, $expireTimestamp)
    {
        $this->message->privileges[$key] = $expireTimestamp;
        return $this;
    }

    function extract($token, $appCertificate, $channelName, $uid)
    {
        $ver_len = 3;
        $appid_len = 32;
        $version = substr($token, 0, $ver_len);
        if ($version !== "006") {
            echo 'invalid version ' . $version;
            return false;
        }

        if (!$this->is_nonempty_string("token", $token) ||
            !$this->is_nonempty_string("appCertificate", $appCertificate) ||
            !$this->is_nonempty_string("channelName", $channelName)) {
            return false;
        }

        $appid = substr($token, $ver_len, $appid_len);
        $content = (base64_decode(substr($token, $ver_len + $appid_len, strlen($token) - ($ver_len + $appid_len))));

        $pos = 0;
        $len = unpack("v", $content . substr($pos, 2))[1];
        $pos += 2;
        $sig = substr($content, $pos, $len);
        $pos += $len;
        $crc_channel = unpack("V", substr($content, $pos, 4))[1];
        $pos += 4;
        $crc_uid = unpack("V", substr($content, $pos, 4))[1];
        $pos += 4;
        $msgLen = unpack("v", substr($content, $pos, 2))[1];
        $pos += 2;
        $msg = substr($content, $pos, $msgLen);

        $this->appID = $appid;
        $message = new Message();
        $message->unpackContent($msg);
        $this->message = $message;

        //non reversable values
        $this->appCertificate = $appCertificate;
        $this->channelName = $channelName;
        $this->setUid($uid);
        return true;
    }

    function build()
    {
        $msg = $this->message->packContent();
        $val = array_merge(unpack("C*", $this->appID), unpack("C*", $this->channelName), unpack("C*", $this->uid), $msg);

        $sig = hash_hmac('sha256', implode(array_map("chr", $val)), $this->appCertificate, true);

        $crc_channel_name = crc32($this->channelName) & 0xffffffff;
        $crc_uid = crc32($this->uid) & 0xffffffff;

        $content = array_merge(unpack("C*", packString($sig)), unpack("C*", pack("V", $crc_channel_name)), unpack("C*", pack("V", $crc_uid)), unpack("C*", pack("v", count($msg))), $msg);
        $version = "006";
        $ret = $version . $this->appID . base64_encode(implode(array_map("chr", $content)));
        return $ret;
    }
}

function packString($value)
{
    return pack("v", strlen($value)) . $value;
}


class RtcTokenBuilder
{
    const RoleAttendee = 0;
    const RolePublisher = 1;
    const RoleSubscriber = 2;
    const RoleAdmin = 101;

    # appID: The App ID issued to you by Agora. Apply for a new App ID from
    #        Agora Dashboard if it is missing from your kit. See Get an App ID.
    # appCertificate:	Certificate of the application that you registered in
    #                  the Agora Dashboard. See Get an App Certificate.
    # channelName:Unique channel name for the AgoraRTC session in the string format
    # uid: User ID. A 32-bit unsigned integer with a value ranging from
    #      1 to (232-1). optionalUid must be unique.
    # role: Role_Publisher = 1: A broadcaster (host) in a live-broadcast profile.
    #       Role_Subscriber = 2: (Default) A audience in a live-broadcast profile.
    # privilegeExpireTs: represented by the number of seconds elapsed since
    #                    1/1/1970. If, for example, you want to access the
    #                    Agora Service within 10 minutes after the token is
    #                    generated, set expireTimestamp as the current
    #                    timestamp + 600 (seconds)./
    public static function buildTokenWithUid($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpireTs){
        return RtcTokenBuilder::buildTokenWithUserAccount($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpireTs);
    }

    # appID: The App ID issued to you by Agora. Apply for a new App ID from
    #        Agora Dashboard if it is missing from your kit. See Get an App ID.
    # appCertificate:	Certificate of the application that you registered in
    #                  the Agora Dashboard. See Get an App Certificate.
    # channelName:Unique channel name for the AgoraRTC session in the string format
    # userAccount: The user account.
    # role: Role_Publisher = 1: A broadcaster (host) in a live-broadcast profile.
    #       Role_Subscriber = 2: (Default) A audience in a live-broadcast profile.
    # privilegeExpireTs: represented by the number of seconds elapsed since
    #                    1/1/1970. If, for example, you want to access the
    #                    Agora Service within 10 minutes after the token is
    #                    generated, set expireTimestamp as the current
    public static function buildTokenWithUserAccount($appID, $appCertificate, $channelName, $userAccount, $role, $privilegeExpireTs){
        $token = AccessToken::init($appID, $appCertificate, $channelName, $userAccount);
        $Privileges = AccessToken::Privileges;
        $token->addPrivilege($Privileges["kJoinChannel"], $privilegeExpireTs);
        if(($role == RtcTokenBuilder::RoleAttendee) ||
            ($role == RtcTokenBuilder::RolePublisher) ||
            ($role == RtcTokenBuilder::RoleAdmin))
        {
            $token->addPrivilege($Privileges["kPublishVideoStream"], $privilegeExpireTs);
            $token->addPrivilege($Privileges["kPublishAudioStream"], $privilegeExpireTs);
            $token->addPrivilege($Privileges["kPublishDataStream"], $privilegeExpireTs);
        }
        return $token->build();
    }
}


?>