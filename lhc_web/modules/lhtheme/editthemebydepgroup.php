<?php

$Departament_group = erLhcoreClassModelDepartamentGroup::fetch((int)$Params['user_parameters']['id']);

$tpl = erLhcoreClassTemplate::getInstance( 'lhtheme/editthemebydepgroup.tpl.php');
$tpl->set('department_group', $Departament_group);

$themes = [];
foreach (erLhcoreClassModelDepartamentGroupMember::getList(['filter' => ['dep_group_id' => $Departament_group->id]]) as $groupMember) {
    $department = erLhcoreClassModelDepartament::fetch($groupMember->dep_id);
    if (isset($department->bot_configuration_array['theme_ind']) && !empty($department->bot_configuration_array['theme_ind'])) {
        $themes[] = ['department' => $department, 'themes' => erLhAbstractModelWidgetTheme::getList(['filterin' => ['id' => explode(',',$department->bot_configuration_array['theme_ind'])]])];
    }
}

if (!empty($themes)) {
    $object = null;
    foreach ($themes as $themeList) {
        foreach ($themeList['themes'] as $theme) {
            if ($theme->noonline_operators_offline != '') {
                $object = $theme;
                break 2;
            }
        }
    }

    if ($object === null) {
        $object = new erLhAbstractModelWidgetTheme();
    }

    if (ezcInputForm::hasPostData()) {
        if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
            erLhcoreClassModule::redirect('/');
            exit;
        }

        $Errors = erLhcoreClassAbstract::validateInput($object, ['noonline_operators_offline']);

        if (count($Errors) == 0) {
            $tpl->set('updated', true);

            // Update all related themes
            foreach ($themes as $themeList) {
                foreach ($themeList['themes'] as $theme) {
                    erLhcoreClassAbstract::validateInput($theme, ['noonline_operators_offline']);
                    $theme->updateThis();
                }
            }

        } else {
            $tpl->set('errors', $Errors);
        }
    }

    $tpl->set('object',$object);
    $tpl->set('themes',$themes);
    $tpl->set('depGroup',$Departament_group);
    $Result['additional_footer_js'] = $object->dependFooterJs();
}

$Result['pagelayout'] = 'chattabs';
$Result['content'] = $tpl->fetch();

?>