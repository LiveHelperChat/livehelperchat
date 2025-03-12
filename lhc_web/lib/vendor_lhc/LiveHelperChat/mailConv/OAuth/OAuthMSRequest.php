<?php

namespace LiveHelperChat\mailConv\OAuth;

/* oauth.php Azure AD oAuth class
 *
 * Katy Nicholson, last updated 17/11/2021
 *
 * https://github.com/CoasterKaty
 * https://katytech.blog/
 * https://twitter.com/coaster_katy
 *
 */

class OAuthMSRequest
{
    public $settings = null;
    public $scope = null;

    public $oAuthChallenge;
    public $oAuthChallengeMethod;
    public $oAuthVerifier;

    public function __construct()
    {
        $tOptions = \erLhcoreClassModelChatConfig::fetch('mailconv_oauth_options');
        $this->settings = (array)$tOptions->data;
        $this->scope = 'https%3A%2F%2Foutlook.office365.com%2FIMAP.AccessAsUser.All+https%3A%2F%2Foutlook.office365.com%2FPOP.AccessAsUser.All+https%3A%2F%2Foutlook.office365.com%2FSMTP.Send+offline_access';
    }

    public function loginAction($mailbox)
    {
        $this->oAuthChallenge();

        // Generate a session key and store in cookie, then populate database
        $sessionKey = $this->uuid();

        $sessionData = \LiveHelperChat\Models\mailConv\OAuthMS::findOne(['filter' => ['mailbox_id' => $mailbox->id]]);

        if (!($sessionData instanceof \LiveHelperChat\Models\mailConv\OAuthMS)) {
            $sessionData = \LiveHelperChat\Models\mailConv\OAuthMS::findOne(['filter' => ['txtSessionKey' => $sessionKey]]);
        }

        if (!($sessionData instanceof \LiveHelperChat\Models\mailConv\OAuthMS)) {
            $sessionData = new \LiveHelperChat\Models\mailConv\OAuthMS();
        }

        $sessionData->txtSessionKey = $sessionKey;
        $sessionData->txtCodeVerifier = $this->oAuthVerifier;
        $sessionData->dtExpires = time() + 300;
        $sessionData->mailbox_id = $mailbox->id;
        $sessionData->saveThis();

        // Redirect to Azure AD login page
        $oAuthURL = 'https://login.microsoftonline.com/' . $this->settings['ms_tenant_id'] . '/oauth2/v2.0/' . 'authorize?state=' . $sessionData->txtSessionKey . '&access_type=offline&include_granted_scores=true&response_type=code&client_id=' . $this->settings['ms_client_id'] . '&redirect_uri=' . urlencode(\erLhcoreClassXMP::getBaseHost() . $_SERVER['HTTP_HOST'] . \erLhcoreClassDesign::baseurl('mailconvoauth/msoauth') ) . '&scope=' .  $this->scope . '&code_challenge=' . $this->oAuthChallenge . '&code_challenge_method=' . $this->oAuthChallengeMethod . '&login_hint=' . rawurlencode($mailbox->mail);
        header('Location: ' . $oAuthURL);
        exit;
    }

    function oAuthChallenge()
    {
        // Function to generate code verifier and code challenge for oAuth login. See RFC7636 for details. 
        $verifier = $this->oAuthVerifier;
        if (!$this->oAuthVerifier) {
            $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-._~';
            $charLen = strlen($chars) - 1;
            $verifier = '';
            for ($i = 0; $i < 128; $i++) {
                $verifier .= $chars[mt_rand(0, $charLen)];
            }
            $this->oAuthVerifier = $verifier;
        }
        // Challenge = Base64 Url Encode ( SHA256 ( Verifier ) )
        // Pack (H) to convert 64 char hash into 32 byte hex
        // As there is no B64UrlEncode we use strtr to swap +/ for -_ and then strip off the =
        $this->oAuthChallenge = str_replace('=', '', strtr(base64_encode(pack('H*', hash('sha256', $verifier))), '+/', '-_'));
        $this->oAuthChallengeMethod = 'S256';
    }
    
    function errorMessage($message)
    {
        return '<!DOCTYPE html>
                        <html lang="en">
                        <head>
                                <meta name="viewport" content="width=device-width, initial-scale=1">
                                <title>Error</title>
                                <link rel="stylesheet" type="text/css" href="style.css" />
                        </head>
                        <body>
                        <div id="fatalError"><div id="fatalErrorInner"><span>Something\'s gone wrong!</span>' . htmlspecialchars($message) . '</div></div>
                        </body>
                        </html>';
    }

    function generateRequest($data)
    {
        // Use the client secret instead
        return $data . '&client_secret=' . urlencode($this->settings['ms_secret']);
    }

    function postRequest($endpoint, $data)
    {
        $ch = curl_init('https://login.microsoftonline.com/' . $this->settings['ms_tenant_id'] . '/oauth2/v2.0/' . $endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if ($cError = curl_error($ch)) {
            echo $this->errorMessage($cError);
            exit;
        }
        curl_close($ch);
        return $response;
    }

    function base64UrlEncode($toEncode)
    {
        return str_replace('=', '', strtr(base64_encode($toEncode), '+/', '-_'));
    }

    function uuid()
    {
        //uuid function is not my code, but unsure who the original author is. KN
        //uuid version 4
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

}

?>
