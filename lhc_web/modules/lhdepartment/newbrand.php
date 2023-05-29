<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhdepartment/newbrand.tpl.php');
$brand = new \LiveHelperChat\Models\Brand\Brand();

if ( isset($_POST['Cancel_departament']) ) {
    erLhcoreClassModule::redirect('department/brands');
    exit;
}

if (isset($_POST['Save_departament']))
{
    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
        $Errors[] = 'Invalid CSRF token!';
    }

    $members = [];
    $Errors = erLhcoreClassDepartament::validateDepartmentBrand($brand, $members);

    if (count($Errors) == 0)
    {
        $brand->saveThis();
        $brand->saveMembers($members);

        erLhcoreClassModule::redirect('department/brands');
        exit ;

    } else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('item',$brand);
$Result['additional_footer_js'] = '<script src="'.erLhcoreClassDesign::designJS('js/angular.lhc.brand.js').'"></script>';

$Result['content'] = $tpl->fetch();
$Result['path'] = array(
    array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','System configuration')),
    array('url' => erLhcoreClassDesign::baseurl('department/index'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/departments','Departments')),
    array('url' => erLhcoreClassDesign::baseurl('department/brands'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Brands')),
    array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/new','New brand')));

?>