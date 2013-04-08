<?php

$faq = erLhcoreClassFaq::getSession()->load( 'erLhcoreClassModelFaq', $Params['user_parameters']['id']);
erLhcoreClassFaq::getSession()->delete($faq);

erLhcoreClassModule::redirect('faq/list');
exit;
?>