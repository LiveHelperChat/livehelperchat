<?php

$tpl = erLhcoreClassTemplate::getInstance('lhdepartment/editbrand.tpl.php');

$brand = \LiveHelperChat\Models\Brand\Brand::fetch((int)$Params['user_parameters']['id']);

if ( isset($_POST['Cancel_departament']) ) {
    erLhcoreClassModule::redirect('department/brands');
    exit;
}

if ( isset($_POST['Delete_departament']) ) {

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('department/brands');
        exit;
    }

    $brand->removeThis();
    erLhcoreClassModule::redirect('department/brands');
    exit;
}

if (isset($_POST['Update_departament']) || isset($_POST['Save_departament'])  )
{
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        erLhcoreClassModule::redirect('department/brands');
        exit;
    }

    $members = [];
    $Errors = erLhcoreClassDepartament::validateDepartmentBrand($brand, $members);

    if (count($Errors) == 0)
    {
        $brand->updateThis();
        $brand->saveMembers($members);

        if (isset($_POST['Save_departament'])) {
            erLhcoreClassModule::redirect('department/brands');
            exit;
        } else {
            $tpl->set('updated',true);
        }

    }  else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item',$brand);
$tpl->set('currentUser',$currentUser);
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.lhc.brand.js').'"></script>';

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('department/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Departments')),
    array('url' => erLhcoreClassDesign::baseurl('department/brands'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Brands')),
    array('title' => $brand->name));

?>