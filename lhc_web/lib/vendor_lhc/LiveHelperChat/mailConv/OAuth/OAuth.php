<?php

namespace LiveHelperChat\mailConv\OAuth
{
    class OAuth
    {
        protected $mailbox = null;

        public function __construct($mailbox) {
            $this->mailbox = $mailbox;
        }

        public static function getPassword($mailbox, $forceRefresh = false) {
            if (strpos($mailbox->imap,'outlook.office365.com') !== false) {
                $oauth = \LiveHelperChat\Models\mailConv\OAuthMS::findOne(['filter' => ['mailbox_id' => $mailbox->id]]);
                if (!is_object($oauth)) {
                    throw new \Exception('OAuth not found for MS');
                }

                if ($oauth->dtExpires < time() + 600 || $forceRefresh === true) {

                    //attempt token refresh
                    if ($oauth->txtRefreshToken) {

                        $oauthRequest = new \LiveHelperChat\mailConv\OAuth\OAuthMSRequest();

                        $oauthRequestData = $oauthRequest->generateRequest('grant_type=refresh_token&refresh_token=' . $oauth->txtRefreshToken . '&client_id=' . $oauthRequest->settings['ms_client_id'] . '&scope=' . $oauthRequest->scope);
                        $response = $oauthRequest->postRequest('token', $oauthRequestData);

                        $reply = json_decode($response, true);

                        if (isset($reply['error'])) {
                            if(substr($reply['error_description'], 0, 12) == 'AADSTS70008:') {
                                throw new \Exception('Refresh token has expired! Please login again.');
                            }

                            throw new \Exception($reply['error_description']);

                        }

                        $idToken = '';

                        if (isset($reply['id_token'])){
                            $idToken = base64_decode(explode('.', $reply['id_token'])[1]);
                        }

                        $oauth->txtToken = $reply['access_token'];
                        $oauth->txtRefreshToken = $reply['refresh_token'];

                        $oauth->txtIDToken = $idToken;

                        $oauth->dtExpires = time() + $reply['expires_in'];
                        $oauth->saveThis();
                    }
                }

                return $oauth->txtToken;
            }

            throw new \Exception('Could not fetch password');
        }

        public static function getClient($mailbox)
        {
            $password = self::getPassword($mailbox);

            $cm = new \Webklex\PHPIMAP\ClientManager();

            $host = parse_url(str_replace(['{','}'],'',$mailbox->imap));

            $pathArguments = explode('/',$host['path']);
            $protocolDefault = 'imap';

            foreach (['imap4', 'imap4rev1','imap'] as $protocolValid) {
                if (in_array($protocolValid,$pathArguments)) {
                    $protocolDefault = $protocolValid;
                    break;
                }
            }

            $connectionArgs = [
                'host' => $host['host'],
                'port' => $host['port'],
                'encryption' => (in_array('tls',$pathArguments) ? 'tls' : 'ssl'),
                'validate_cert' => false,
                'username' => $mailbox->mail,
                'password' => $password,
                'protocol' => $protocolDefault,
                'authentication' => "oauth",
            ];

            try {
                $client = $cm->make($connectionArgs);
                $client->connect();
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), "NO AUTHENTICATE") !== false) {
                    $connectionArgs['password'] = self::getPassword($mailbox,true);
                    $client = $cm->make($connectionArgs);
                    $client->connect();
                } else {
                    throw $e;
                }
            }

            return $client;
        }

        public static function setupFolder($mailbox) {

            if (strpos($mailbox->imap,'outlook.office365.com') !== false) {

                $client = self::getClient($mailbox);

                $folders = $client->getFolders(false);

                $mailboxPresentItems = $mailbox->mailbox_sync_array;

                foreach ($folders as $mailboxItem) {
                    $exists = false;
                    foreach ($mailboxPresentItems as $mailboxPresentItem) {
                        if ($mailboxPresentItem['path'] == $mailboxItem->path) {
                            $exists = true;
                        }
                    }

                    if ($exists == false) {
                        $mailboxPresentItems[] = ['sync' => false, 'path' => $mailboxItem->path];
                    }
                }

                $mailbox->mailbox_sync_array = $mailboxPresentItems;
                $mailbox->mailbox_sync = json_encode($mailbox->mailbox_sync_array);
                $mailbox->saveThis();
            }
        }

    }
}

