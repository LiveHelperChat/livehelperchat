<?php

$tpl = erLhcoreClassTemplate::getInstance( 'lhfaq/faqwidget.tpl.php');

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('faq/faqwidget');
$pages->items_total = erLhcoreClassModelFaq::getCount();
$pages->setItemsPerPage(100);
$pages->paginate();

$edittab;
$success;

$items = erLhcoreClassModelFaq::getList(array('offset' => $pages->low, 'limit' => $pages->items_per_page));
$item_new = new erLhcoreClassModelFaq();

if ( isset($_POST['send']) )
{
	$definition = array(
			'answer' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw' ),
			'question' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			),
			'url' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
			)
	);
	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();

	if ( !$form->hasValidData( 'question' ) || $form->question == '')
	{
		$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('chat/faqwidget','Please enter question');
	}

	if (count($Errors) == 0)
	{
		$success="1";
		$item_new->answer = "";
		$item_new->question = $form->question;
		$item_new->url = $form->url;
		erLhcoreClassFaq::getSession()->SaveOrUpdate($item_new);
	} else {
		$tpl->set('errors',$Errors);
	}
	$edittab="1";
}

$tpl->set('items',$items);
$tpl->set('pages',$pages);
$tpl->set('success',$success);
$tpl->set('edittab',$edittab);

$Result['content'] = $tpl->fetch();
$Result['pagelayout'] = 'widget';


?>