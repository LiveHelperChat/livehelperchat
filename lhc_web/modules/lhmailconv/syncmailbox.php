<?php

$item =  erLhcoreClassModelMailconvMailbox::fetch($Params['user_parameters']['id']);

erLhcoreClassMailconvParser::syncMailbox($item);

?>