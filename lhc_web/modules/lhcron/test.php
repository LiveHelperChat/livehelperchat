<?php
/**
 * php cron.php -s site_admin -c cron/test
 *
 * For various testing purposes
 *
 * */
/*'found' => true,
            'typos_used' => 0,*/



$lhcinsultOptions = erLhcoreClassChat::getSession()->load( 'erLhcoreClassModelChatConfig', 'lhcinsult_options' );
$data = (array)$lhcinsultOptions->data;

if (isset($data['safe_comb']) && trim($data['safe_comb']) != '') {
    $rulesCheck = explode("\n",trim(str_replace(array("\r\n"),"\n",$data['safe_comb'])));
    foreach ($rulesCheck as $ruleCheck) {
        $presenceOutcome = erLhcoreClassGenericBotWorkflow::checkPresenceMessage(array(
            'pattern' => $ruleCheck,
            'msg' => mb_strtolower('please close my duper'),
        ));
        // check is it safe combination
        if ($presenceOutcome['found']) {
            echo "asdasd";
        }
    }
}


?>