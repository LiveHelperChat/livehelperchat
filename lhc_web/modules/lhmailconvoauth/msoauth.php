<?php

$oAuth = new \LiveHelperChat\mailConv\OAuth\OAuthMSRequest();

if (isset($_GET['error'])) {
    echo $oAuth->errorMessage($_GET['error_description']);
    exit;
}

$tOptions = erLhcoreClassModelChatConfig::fetch('mailconv_oauth_options');
$data = (array)$tOptions->data;

$sessionData = \LiveHelperChat\Models\mailConv\OAuthMS::findOne(['filter' => ['txtSessionKey' => $_GET['state']]]);

if ($sessionData) {

    $mailbox = erLhcoreClassModelMailconvMailbox::fetch($sessionData->mailbox_id);

    // Request token from Azure AD
    $oauthRequest = $oAuth->generateRequest('login_hint=' . rawurlencode($mailbox->mail) . '&state=' . $sessionData->txtSessionKey . '&grant_type=authorization_code&client_id=' . $data['ms_client_id'] . '&redirect_uri=' . urlencode(erLhcoreClassXMP::getBaseHost() . $_SERVER['HTTP_HOST'] . erLhcoreClassDesign::baseurl('mailconvoauth/msoauth') ) . '&code=' . $_GET['code'] . '&code_verifier=' . $sessionData->txtCodeVerifier);

    $response = $oAuth->postRequest('token', $oauthRequest);

    // Decode response from Azure AD. Extract JWT data from supplied access_token and id_token and update database.
    if (!$response) {
        echo $oAuth->errorMessage('Unknown error acquiring token');
        exit;
    }

    $reply = json_decode($response);

    if (isset($reply->error)) {
        echo $oAuth->errorMessage($reply->error_description);
        exit;
    }

    $idToken = base64_decode(explode('.', $reply->id_token)[1]);
    $sessionData->txtToken = $reply->access_token;
    $sessionData->txtRefreshToken = $reply->refresh_token;
    $sessionData->txtIDToken = $idToken;
    $sessionData->dtExpires = time() + $reply->expires_in;
    $sessionData->email = $mailbox->mail;
    $sessionData->completed = 1;
    $sessionData->saveThis();

    erLhcoreClassModule::redirect('mailconv/editmailbox','/' . $sessionData->mailbox_id);

} else {
    erLhcoreClassModule::redirect('mailconv/mailbox');
    exit;
}

exit;
?>