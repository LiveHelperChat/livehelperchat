<?php

// For IE to support headers if chat is installed on different domain
header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');

$referer = '';
$dynamic_url = '';

$tpl = erLhcoreClassTemplate::getInstance( 'lhfaq/faqwidget.tpl.php');
$tpl->set('referer',$referer);

if (isset($_GET['URLReferer']))
{
	$referer = $_GET['URLReferer'];
	$tpl->set('referer',$referer);
}


if (isset($_GET['URLReferer']))
{
	$referer = $_GET['URLReferer'];
	$tpl->set('referer',$referer);
}

if (isset($_POST['URLModule']))
{
	$dynamic_url = $_POST['URLModule'] == 'replace_me_with_dynamic_url' ? '' : (string)$_POST['URLModule'];
	$tpl->set('dynamic_url',$dynamic_url);
}

if (isset($_GET['URLModule']))
{
	$dynamic_url = $_GET['URLModule'] == 'replace_me_with_dynamic_url' ? '' : (string) $_GET['URLModule'];
	$tpl->set('dynamic_url',$dynamic_url);
}

if ($dynamic_url == ''){
	$dynamic_url = $referer;
}

$dynamic_url_append = '';
if ($dynamic_url != ''){
	$dynamic_url_append = '/(url)/'.rawurlencode($dynamic_url_append);
}

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('faq/faqwidget').$dynamic_url_append;
$pages->items_total = erLhcoreClassModelFaq::getCount(array('filter' => array('active' => 1)));
$pages->setItemsPerPage(5);
$pages->paginate();

$items = erLhcoreClassModelFaq::getList(array('filter' => array('active' => 1), 'offset' => $pages->low, 'limit' => $pages->items_per_page));
$item_new = new erLhcoreClassModelFaq();

if ( isset($_POST['send']) )
{
	$definition = array(
			'question' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'),
			'url' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
	);

	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();

	if ( !$form->hasValidData( 'question' ) || $form->question == '') {
		$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Please enter question!');
	} else {
		$item_new->question = $form->question;
	}

	if ( $form->hasValidData( 'url' ) )
	{
		$item_new->url = $form->url;
	}

	if (count($Errors) == 0) {
		$item_new->active = 0;
		erLhcoreClassFaq::getSession()->SaveOrUpdate($item_new);
		$item_new = new erLhcoreClassFaq();
		$tpl->set('success',true);
	} else {
		$tpl->set('errors',$Errors);
	}

	$tpl->set('edittab',true);
}

$tpl->set('items',$items);
$tpl->set('item_new',$item_new);
$tpl->set('pages',$pages);

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'widget';