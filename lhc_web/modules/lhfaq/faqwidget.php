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

if (isset($_GET['URLModule']))
{
	$dynamic_url = $_GET['URLModule'] == 'replace_me_with_dynamic_url' ? '' : (string) $_GET['URLModule'];
	$tpl->set('dynamic_url',$dynamic_url);
}

if ($dynamic_url == '') {
	$dynamic_url = base64_decode(rawurldecode((string)$Params['user_parameters_unordered']['url']));
	if (empty($dynamic_url)){
		$dynamic_url = $referer;
	} else {
		$tpl->set('referer',$dynamic_url);
	}
}

// For filter we use string without domain etc.
$matchStringURL = '';
if ($dynamic_url != '') {
	$parts = parse_url($dynamic_url);
	if (isset($parts['path'])) {
		$matchStringURL = $parts['path'];
	}

	if (isset($parts['query'])) {
		$matchStringURL .= '?'.$parts['query'];
	}
}

$dynamic_url_append = '';
if ($dynamic_url != ''){
	$dynamic_url_append .= '/(url)/'.rawurlencode(base64_encode($dynamic_url));
}

$embedMode = false;
if ((string)$Params['user_parameters_unordered']['mode'] == 'embed') {
	$dynamic_url_append .= '/(mode)/embed';
	$embedMode = true;
}

if (!empty($dynamic_url_append)) {
	$tpl->set('dynamic_url_append',$dynamic_url_append);
}

// We use direct queries in this file, because of its complexity
$session = erLhcoreClassFaq::getSession();
$q = $session->database->createSelectQuery();
$q->select( "COUNT(id)" )->from( "lh_faq" );
$q->where(
		$q->expr->eq( 'active', 1 ),
		$q->expr->lOr(
		$q->expr->eq( 'url', $q->bindValue('') ),
		$q->expr->eq( 'url', $q->bindValue( trim($matchStringURL) ) ) )

);
$stmt = $q->prepare();
$stmt->execute();
$result = $stmt->fetchColumn();

$pages = new lhPaginator();
$pages->serverURL = erLhcoreClassDesign::baseurl('faq/faqwidget').$dynamic_url_append;
$pages->items_total = $result;
$pages->setItemsPerPage(5);
$pages->paginate();
$items = array();

if ($pages->items_total > 0) {
	$q = $session->createFindQuery( 'erLhcoreClassModelFaq' );
	$q->where(
			$q->expr->eq( 'active', 1 ),
			$q->expr->lOr(
					$q->expr->eq( 'url', $q->bindValue('') ),
					$q->expr->eq( 'url', $q->bindValue( trim($matchStringURL) ) ) )

	);
	$q->limit($pages->items_per_page, $pages->low);
	$q->orderBy('has_url DESC, id DESC' ); // Questions with matched URL has higher priority
	$items = $session->find( $q );
}

$item_new = new erLhcoreClassModelFaq();

if ( isset($_POST['send']) )
{

	$definition = array(
			'question' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'),
			'url' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
	);

	// Captcha stuff
	$hashCaptcha = $_SESSION[$_SERVER['REMOTE_ADDR']]['form'];
	$nameField = 'captcha_'.$_SESSION[$_SERVER['REMOTE_ADDR']]['form'];
	$definition[$nameField] = new ezcInputFormDefinitionElement( ezcInputFormDefinitionElement::OPTIONAL, 'string' );

	$form = new ezcInputForm( INPUT_POST, $definition );
	$Errors = array();

	if ( !$form->hasValidData( 'question' ) || $form->question == '') {
		$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Please enter a question!');
	} else {
		$item_new->question = $form->question;
	}

	if ( $form->hasValidData( 'url' ) )
	{
		$item_new->url = $form->url;
	}

	// Captcha validation
	if ( !$form->hasValidData( $nameField ) || $form->$nameField == '' || $form->$nameField < time()-600 || $hashCaptcha != sha1($_SERVER['REMOTE_ADDR'].$form->$nameField.erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' )))
	{
		$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation('chat/startchat','Invalid captcha code, please enable Javascript!');
	}

	// Dynamic URL has higher priority
	if ($dynamic_url != '') {
		$item_new->url = $dynamic_url;
	}



	if (count($Errors) == 0) {
		$item_new->active = 0;
		$item_new->saveThis();
		$item_new = new erLhcoreClassFaq();
		$tpl->set('success',true);

		if (isset($_SESSION[$_SERVER['REMOTE_ADDR']]['form'])) {
			unset($_SESSION[$_SERVER['REMOTE_ADDR']]['form']);
		}

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
$Result['dynamic_height'] = true;
$Result['dynamic_height_message'] = 'lhc_sizing_faq';
$Result['dynamic_height_append'] = 10;
if ($embedMode == true) {
	$Result['dynamic_height_message'] = 'lhc_sizing_faq_embed';
	$Result['pagelayout_css_append'] = 'embed-widget';
}

