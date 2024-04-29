<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhgenericbot/newcommand.tpl.php');
$command = new erLhcoreClassModelGenericBotCommand();

if ( isset($_POST['Cancel_bot']) ) {
    erLhcoreClassModule::redirect('genericbot/commands');
    exit;
}

if (isset($_POST['Save_bot']) || isset($_POST['Update_bot']))
{
    $Errors = erLhcoreClassGenericBot::validateBotCommand($command);

    if (count($Errors) == 0)
    {
        $command->saveThis();

        if (isset($_POST['Update_bot'])) {
            erLhcoreClassModule::redirect('genericbot/editcommand','/' . $command->id);
        } else {
            erLhcoreClassModule::redirect('genericbot/commands');
        }
        exit ;

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item',$command);
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/lhc.botcommand.js').'"></script>';

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('genericbot/commands'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','Commands')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('genericbot/new','New')),
)

?>