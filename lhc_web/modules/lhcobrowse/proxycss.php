<?php
header("Content-type: text/css", true);

/**
 * Refetches CSS as it was hosted on our server.
 * I can swear there is some bugs regarding relative CSS and base URL path.
 * This script is required if LHC is hosted on HTTPS but site itself is hosted on HTTP, because browser forbids downloading content from insecure site except images
 * themself
 * */
if ($Params['user_parameters_unordered']['cobrowsemode'] == 'onlineuser'){
    $ouser = erLhcoreClassModelChatOnlineUser::fetch($Params['user_parameters']['chat_id']);
    $browse = erLhcoreClassCoBrowse::getBrowseInstanceByOnlineUser($ouser);
} else {
    $chat = erLhcoreClassChat::getSession()->load('erLhcoreClassModelChat', $Params['user_parameters']['chat_id']);
    $browse = erLhcoreClassCoBrowse::getBrowseInstance($chat);
}

$url = parse_url($_GET['base']);

// Some basic validation
if (isset($url['host']) && $url['host'] != '' && strpos($_GET['css'], $_SERVER['HTTP_HOST']) === false) {
    $ch = curl_init();
        
    $urlCSS = parse_url($_GET['css']);
   
    // Just our attempt to fix CSS, BaseURL, Relative path madness
    if (! isset($urlCSS['host']) || $urlCSS['host'] == '') {
        if (strpos($_GET['css'], '../') !== false) {            
            $numberOfTime = substr_count($_GET['css'], '../');
            $match = str_replace('../', '', $_GET['css']);  
                      
            $parts = explode('/', rtrim($_GET['base'],'/'));            
            $imagePathToCss = array_slice($parts, 0, count($parts) - $numberOfTime);
            
            $urlCSSDownload = implode('/', $imagePathToCss) . $match;                      
        } else {
            $urlCSSDownload = $url['scheme'] . '://' . $url['host'] . '/' . ltrim($_GET['css'], '/');
        }
    } else { 
        $urlCSSDownload = $_GET['css'];
    }
    
    curl_setopt($ch, CURLOPT_URL, $urlCSSDownload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_REFERER, $_GET['base']);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
    $cssContent = curl_exec($ch);

    if ($cssContent !== false) {        
        if (preg_match_all("/url\(\s*[\'|\"]?([A-Za-z0-9_\-\/\.\\%?&#]+)[\'|\"]?\s*\)/ix", $cssContent, $urlMatches)) {
            $urlMatches = array_unique($urlMatches[1]);
            foreach ($urlMatches as $match) {
                $match = str_replace('\\', '/', $match);
                // Replace path if it is realtive
                if (strpos($match, 'http') === false) {
                    $appendMatch = '';
                    $matchOriginal = $match;
                    
                    if (strpos($match, '?') !== false) {
                        $matchParts = explode('?', $match);
                        $match = $matchParts[0];
                        $appendMatch = '?' . $matchParts[1];
                    }
                    
                    // Fix relative path
                    if (strpos($match, '../') !== false) {
                        $numberOfTime = substr_count($match, '../');
                        $match = str_replace('../', '', $match);
                        
                        $parts = explode('/', $_GET['css']);
                        array_pop($parts);
                        
                        $imagePathToCss = array_slice($parts, 0, count($parts) - $numberOfTime);
                        
                        $cssContent = str_replace($matchOriginal, $url['scheme'] . '://' . str_replace('//', '/', $url['host'] . '/' . implode('/', $imagePathToCss) . '/' . $match . $appendMatch), $cssContent);
                    } else { // Absolute path
                        $cssContent = str_replace($matchOriginal, $url['scheme'] . '://' . str_replace('//', '/', $url['host'] . '/' . $match . $appendMatch), $cssContent);
                    }
                }
            }
        }        
    }
    echo $cssContent;
}

exit();
?>