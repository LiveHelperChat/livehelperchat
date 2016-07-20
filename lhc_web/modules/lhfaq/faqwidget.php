<?php

// For IE to support headers if chat is installed on different domain
header('P3P: CP="NOI ADM DEV COM NAV OUR STP"');

$referer = '';
$dynamic_url = '';
$identifier = '';

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

if (isset($_GET['identifier']))
{
	$identifier = ($_GET['identifier'] != '' && $_GET['identifier'] != 'undefined') ? (string) $_GET['identifier'] : '';
	$tpl->set('identifier',$identifier);
}

if ($dynamic_url == '') {
	$dynamic_url = base64_decode(rawurldecode((string)$Params['user_parameters_unordered']['url']));
	if (empty($dynamic_url)){
		$dynamic_url = $referer;
	} else {
		$tpl->set('referer',$dynamic_url);
	}
}

if ($identifier == '') {
	$identifier = rawurldecode((string)$Params['user_parameters_unordered']['identifier']);
	if (!empty($identifier)){
		$tpl->set('identifier',$identifier);
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

if ($identifier != ''){
	$dynamic_url_append .= '/(identifier)/'.rawurlencode($identifier);
}

$embedMode = false;
if ((string)$Params['user_parameters_unordered']['mode'] == 'embed') {
	$dynamic_url_append .= '/(mode)/embed';
	$embedMode = true;
}

if (isset($Params['user_parameters_unordered']['theme']) && (int)$Params['user_parameters_unordered']['theme'] > 0){
	try {
		$theme = erLhAbstractModelWidgetTheme::fetch($Params['user_parameters_unordered']['theme']);
		$Result['theme'] = $theme;
		$dynamic_url_append .= '/(theme)/'.$theme->id;
	} catch (Exception $e) {

	}
} else {
	$defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
	if ($defaultTheme > 0) {
		try {
			$theme = erLhAbstractModelWidgetTheme::fetch($defaultTheme);
			$Result['theme'] = $theme;
			$dynamic_url_append .= '/(theme)/'.$theme->id;
		} catch (Exception $e) {
			
		}
	}
}



if (!empty($dynamic_url_append)) {
	$tpl->set('dynamic_url_append',$dynamic_url_append);
}

// We use direct queries in this file, because of its complexity
$session = erLhcoreClassFaq::getSession();
$q = $session->database->createSelectQuery();
$q->select( "COUNT(id)" )->from( "lh_faq" );

$whereConditions = array();
$whereConditions[] = $q->expr->eq( 'active', 1 );
$whereConditions[] = $q->expr->lOr(
							$q->expr->eq( 'url', $q->bindValue('') ),
							$q->expr->eq( 'url', $q->bindValue( trim($matchStringURL) ) ),
							$q->expr->lAnd(
									$q->expr->eq( 'is_wildcard', $q->bindValue(1) ),
									$q->expr->like( $session->database->quote(trim($matchStringURL)),'concat(left(url,length(url)-1),\'%\')'))
					);

if ($identifier != '') {
	$whereConditions[] = $q->expr->eq( 'identifier', $q->bindValue($identifier) );
}

$q->where($whereConditions);


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
	$whereConditions = array();
	$whereConditions[] = $q->expr->eq( 'active', 1 );
	$whereConditions[] = $q->expr->lOr(
					$q->expr->eq( 'url', $q->bindValue('') ),
					$q->expr->eq( 'url', $q->bindValue( trim($matchStringURL) ) ),
					$q->expr->lAnd(
			$q->expr->eq( 'is_wildcard', $q->bindValue(1) ),
			$q->expr->like( $session->database->quote(trim($matchStringURL)),'concat(left(url,length(url)-1),\'%\')')) );
	
	if ($identifier != '') {
		$whereConditions[] = $q->expr->eq( 'identifier', $q->bindValue($identifier) );
	}	
	$q->where($whereConditions);
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
			'email' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'validate_email'),
			'url' => new ezcInputFormDefinitionElement(
					ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw')
	);

	if (erLhcoreClassModelChatConfig::fetch('session_captcha')->current_value == 1) {
		// Start session if required only
		$currentUser = erLhcoreClassUser::instance();
		$hashCaptcha = isset($_SESSION[$_SERVER['REMOTE_ADDR']]['form']) ? $_SESSION[$_SERVER['REMOTE_ADDR']]['form'] : null;
    	$nameField = 'captcha_'.$hashCaptcha;
	} else {	
		// Captcha stuff
		$nameField = 'captcha_'.sha1(erLhcoreClassIPDetect::getIP().$_POST['tscaptcha'].erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' ));
	}
	
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

	if ( $form->hasValidData( 'email' ) ) {
		$item_new->email = $form->email;
	} elseif (erLhcoreClassModelChatConfig::fetch('faq_email_required')->current_value == 1 && !$form->hasValidData( 'email' )) {
		$Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('faq/faqwidget','Please enter your email address!');
	}
	
	$item_new->identifier = $identifier;
	
	if (erLhcoreClassModelChatConfig::fetch('session_captcha')->current_value == 1) {
		if ( !$form->hasValidData( $nameField ) || $form->$nameField == '' || $form->$nameField < time()-600 || $hashCaptcha != sha1($_SERVER['REMOTE_ADDR'].$form->$nameField.erConfigClassLhConfig::getInstance()->getSetting( 'site', 'secrethash' ))){
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation("chat/startchat","Your request was not processed as expected - but don't worry it was not your fault. Please re-submit your request. If you experience the same issue you will need to contact us via other means.");
		}
	} else {		
		// Captcha validation
		if ( !$form->hasValidData( $nameField ) || $form->$nameField == '' || $form->$nameField < time()-600)
		{
			$Errors[] = erTranslationClassLhTranslation::getInstance()->getTranslation("chat/startchat","Your request was not processed as expected - but don't worry it was not your fault. Please re-submit your request. If you experience the same issue you will need to contact us via other means.");
		}
	}
	
	// Dynamic URL has higher priority
	if ($dynamic_url != '') {
		$item_new->url = $dynamic_url;
	}

    erLhcoreClassChatEventDispatcher::getInstance()->dispatch('faq.before_filled_by_user', array('faq' => & $item_new, 'errors' => & $Errors));

	if (count($Errors) == 0) {
		$item_new->active = 0;
		$item_new->saveThis();		
		erLhcoreClassChatMail::sendMailFAQ($item_new);		
		$item_new = new erLhcoreClassFaq();
		$tpl->set('success',true);

		erLhcoreClassChatEventDispatcher::getInstance()->dispatch('faq.filled_by_user', array('faq' => & $item_new));
		
		if (isset($_SESSION[erLhcoreClassIPDetect::getIP()]['form'])) {
			unset($_SESSION[erLhcoreClassIPDetect::getIP()]['form']);
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
$Result['dynamic_height_append'] = 0;
if ($embedMode == true) {
	$Result['dynamic_height_message'] = 'lhc_sizing_faq_embed';
	$Result['pagelayout_css_append'] = 'embed-widget';
}

