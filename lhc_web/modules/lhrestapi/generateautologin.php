<?php

try
{
    erLhcoreClassRestAPIHandler::validateRequest();

    $requestBody = json_decode(file_get_contents('php://input'),true);

    if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhrestapi', 'generateautologinall')) {
        if (isset($requestBody['l'])) {
            unset($requestBody['l']);
        }
        $requestBody['u'] = erLhcoreClassRestAPIHandler::getUserId();
    }

    $data = erLhcoreClassModelChatConfig::fetch('autologin_data')->data;

    if ($data['enabled'] != 1) {
        throw new Exception('Auto login module is disabled!');
    }

    if ($data['secret_hash'] == '') {
        throw new Exception('Secret hash value is empty. Please set it in back office!');
    }

    if (!erLhcoreClassRestAPIHandler::hasAccessTo('lhuser', 'userautologin')) {
        throw new Exception('You do not have permission to generate auto login link. `lhuser`, `userautologin` is required.');
    }

    function generateAutoLoginLink($params){

        $dataRequest = array();
        $dataRequestAppend = array();

        // Destination ID
        if (isset($params['r'])){
            $dataRequest['r'] = $params['r'];
            $dataRequestAppend[] = '/(r)/'.rawurlencode(base64_encode($params['r']));
        }

        // User ID
        if (isset($params['u']) && is_numeric($params['u'])){
            $dataRequest['u'] = $params['u'];
            $dataRequestAppend[] = '/(u)/'.rawurlencode($params['u']);
        }

        // Username
        if (isset($params['l'])){
            $dataRequest['l'] = $params['l'];
            $dataRequestAppend[] = '/(l)/'.rawurlencode($params['l']);
        }

        if (!isset($params['l']) && !isset($params['u'])) {
            throw new Exception('Username or User ID has to be provided');
        }

        $ts = time() + $params['t'];

        // Expire time for link
        if (isset($params['t'])) {
            $dataRequest['t'] = $ts;
            $dataRequestAppend[] = '/(t)/'.rawurlencode($ts);
        }

        $hashValidation = sha1($params['secret_hash'].sha1($params['secret_hash'].implode(',', $dataRequest)));

        $configInstance = erConfigClassLhConfig::getInstance();

        $possibleLoginSiteAccess = array();

        $adminSiteAccess = $configInstance->getSetting('site', 'default_admin_site_access', false);

        if (is_array($adminSiteAccess)) {
            $possibleLoginSiteAccess = $adminSiteAccess;
        } else {
            $possibleLoginSiteAccess[] = 'site_admin';
        }

        erLhcoreClassChatEventDispatcher::getInstance()->dispatch('user.login_site_access', array('loginSiteAccess' => & $possibleLoginSiteAccess));

        return erLhcoreClassSystem::getHost() . erLhcoreClassDesign::baseurldirect("") . $possibleLoginSiteAccess[0] . '/user/autologin' . "/{$hashValidation}".implode('', $dataRequestAppend);
    }

    $requestBody['secret_hash'] = $data['secret_hash'];

    erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => false,
        'result' => generateAutoLoginLink($requestBody)
    ));

} catch (Exception $e) {
    http_response_code(400);
    echo erLhcoreClassRestAPIHandler::outputResponse(array(
        'error' => true,
        'result' => $e->getMessage()
    ));
}

exit();

